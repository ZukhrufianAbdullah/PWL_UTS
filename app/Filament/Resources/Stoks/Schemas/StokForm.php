<?php

namespace App\Filament\Resources\Stoks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class StokForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('supplier_id')
                    ->relationship('supplier', 'supplier_nama')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'nama')
                    ->required(),
                Select::make('barang_id')
                    ->relationship('barang', 'barang_nama')
                    ->required(),
                DateTimePicker::make('stok_tanggal')
                    ->required()
                    ->default(now()),
                TextInput::make('stok_jumlah')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Hidden::make('user_id')
                    ->default(Auth::id()),
            ]);
    }
}
