<?php

namespace App\Filament\Resources\Penjualans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use App\Models\MBarang;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;

class PenjualanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Penjualan')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'nama')
                            ->required()
                            ->default(Auth::id())
                            ->hiddenOn('edit'),
                        TextInput::make('penjualan_kode')
                            ->required()
                            ->maxLength(20)
                            ->default('INV-' . date('Ymd') . '-' . rand(1000, 9999))
                            ->unique(ignoreRecord: true),
                        TextInput::make('pembeli')
                            ->required()
                            ->maxLength(50),
                        DateTimePicker::make('penjualan_tanggal')
                            ->required()
                            ->default(now()),
                        Hidden::make('user_id')
                            ->default(Auth::id()),
                    ])->columns(2),
                
                Section::make('Detail Barang')
                    ->schema([
                        Repeater::make('details')
                            ->relationship()
                            ->schema([
                                Select::make('barang_id')
                                    ->label('Barang')
                                    ->options(function () {
                                        return MBarang::with('kategori')
                                            ->get()
                                            ->mapWithKeys(function ($barang) {
                                                return [$barang->barang_id => $barang->barang_nama . ' (Stok: ' . $barang->stok_sekarang . ') - Rp ' . number_format($barang->harga_jual, 0, ',', '.')];
                                            });
                                    })
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $barang = MBarang::find($state);
                                        if ($barang) {
                                            $set('harga', $barang->harga_jual);
                                            $harga = $barang->harga_jual;
                                            $jumlah = $get('jumlah') ?? 1;
                                            $set('subtotal', $harga * $jumlah);
                                        }
                                    }),
                                TextInput::make('harga')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp ')
                                    ->readOnly(),
                                TextInput::make('jumlah')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $harga = $get('harga') ?? 0;
                                        $set('subtotal', $harga * $state);
                                    }),
                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp ')
                                    ->readOnly()
                                    ->dehydrated(true),
                            ])
                            ->columns(4)
                            ->columnSpanFull()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                static::updateTotal($get, $set);
                            }),

                                TextInput::make('total')
                                    ->label('Total Pembayaran')
                                    ->prefix('Rp ')
                                    ->readOnly()
                                    ->dehydrated(false)
                                    ->formatStateUsing(function (Get $get) {
                                        $total = 0;
                                        $details = $get('details') ?? [];

                                        foreach ($details as $detail) {
                                            $total += ($detail['harga'] ?? 0) * ($detail['jumlah'] ?? 0);
                                        }

                                        return number_format($total, 0, ',', '.');
                                    })
                                    ->columnSpanFull(),                          
                    ]),
            ]);
    }

    protected static function updateTotal(Get $get, Set $set): void
    {
        $total = 0;
        $details = $get('details') ?? [];
        foreach ($details as $detail) {
            $total += ($detail['harga'] ?? 0) * ($detail['jumlah'] ?? 0);
        }
    }
}
