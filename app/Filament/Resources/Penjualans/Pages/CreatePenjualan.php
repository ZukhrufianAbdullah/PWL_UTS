<?php

namespace App\Filament\Resources\Penjualans\Pages;

use App\Filament\Resources\Penjualans\PenjualanResource;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\DB;
use App\Models\MBarang;
use App\Models\TStok;
use App\Models\TPenjualan;
use Exception;

class CreatePenjualan extends CreateRecord
{
    protected static string $resource = PenjualanResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($data) {

            $details = $data['details'] ?? [];

            if (empty($details)) {
                throw new Exception("Detail penjualan tidak boleh kosong!");
            }

            foreach ($details as $detail) {

                $barang = MBarang::find($detail['barang_id']);

                if (!$barang) {
                    throw new Exception("Barang tidak ditemukan");
                }

                if ($barang->stok_sekarang < (int)$detail['jumlah']) {
                    throw new Exception("Stok {$barang->barang_nama} tidak mencukupi!");
                }
            }

            $penjualan = TPenjualan::create([
                'user_id' => $data['user_id'],
                'penjualan_tanggal' => $data['penjualan_tanggal'],
                'pembeli' => $data['pembeli'],
                'penjualan_kode' => $data['penjualan_kode'],
            ]);

            foreach ($details as $detail) {

                $jumlah = (int)$detail['jumlah'];
                $harga  = (int)$detail['harga'];

                $penjualan->details()->create([
                    'barang_id' => $detail['barang_id'],
                    'harga' => $harga,
                    'jumlah' => $jumlah,
                ]);

                TStok::create([
                    'barang_id' => $detail['barang_id'],
                    'supplier_id' => 1,
                    'user_id' => $data['user_id'],
                    'stok_jumlah' => -$jumlah,
                    'stok_tanggal' => now(),
                ]);
            }

            return $penjualan;
        });
    }
}