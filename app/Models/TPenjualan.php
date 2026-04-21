<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;
use Exception;

class TPenjualan extends Model
{
    use SoftDeletes;

    protected $table = 't_penjualan';
    protected $primaryKey = 'penjualan_id';
    protected $fillable = ['user_id', 'penjualan_tanggal', 'pembeli', 'penjualan_kode'];

    protected $casts = [
        'penjualan_tanggal' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($penjualan) {

            foreach ($penjualan->details as $detail) {

                $barang = MBarang::find($detail['barang_id']);

                if (!$barang) {
                    throw new Exception("Barang tidak ditemukan");
                }

                if ($barang->stok_sekarang < $detail['jumlah']) {
                    throw new Exception("Stok barang {$barang->barang_nama} tidak mencukupi!");
                }
            }
        });

        static::created(function ($penjualan) {

            DB::transaction(function () use ($penjualan) {

                foreach ($penjualan->details as $detail) {

                    TStok::create([
                        'barang_id' => $detail->barang_id,
                        'supplier_id' => null,
                        'user_id' => $penjualan->user_id,
                        'stok_jumlah' => -$detail->jumlah,
                        'stok_tanggal' => now(),
                    ]);
                }
            });
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(MUser::class, 'user_id', 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(TPenjualanDetail::class, 'penjualan_id', 'penjualan_id');
    }

    public function getTotalAttribute()
    {
        return $this->details()->sum(DB::raw('harga * jumlah'));
    }
}