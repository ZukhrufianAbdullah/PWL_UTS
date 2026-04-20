<?php

namespace App\Filament\Widgets;

use App\Models\TPenjualan;
use App\Models\MBarang;
use App\Models\MUser;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPenjualanHariIni = TPenjualan::whereDate('penjualan_tanggal', today())->count();
        $totalPendapatanHariIni = TPenjualan::whereDate('penjualan_tanggal', today())
            ->get()
            ->sum(function ($penjualan) {
                return $penjualan->total;
            });
        
        $totalBarang = MBarang::count();
        $totalUser = MUser::count();
        
        return [
            Stat::make('Total Penjualan Hari Ini', $totalPenjualanHariIni)
                ->description('Jumlah transaksi hari ini')
                ->icon('heroicon-o-shopping-cart')
                ->color('success'),
            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($totalPendapatanHariIni, 0, ',', '.'))
                ->description('Total pendapatan hari ini')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning'),
            Stat::make('Total Barang', $totalBarang)
                ->description('Jumlah produk tersedia')
                ->icon('heroicon-o-cube')
                ->color('info'),
            Stat::make('Total User', $totalUser)
                ->description('User terdaftar')
                ->icon('heroicon-o-users')
                ->color('primary'),
        ];
    }
}