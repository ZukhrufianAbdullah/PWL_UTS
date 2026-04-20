<?php

namespace App\Filament\Resources\Barangs\Schemas;

use Dom\Text;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kategori_id')
                    ->relationship('kategori', 'kategori_nama')
                    ->required(),
                TextInput::make('barang_kode')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true),
                TextInput::make('barang_nama')
                    ->required()
                    ->maxLength(100),
                TextInput::make('harga_beli')
                    ->required()
                    ->numeric()
                    ->prefix('Rp '),
                TextInput::make('harga_jual')
                    ->required()
                    ->numeric()
                    ->prefix('Rp '),
            ]);
    }
}
