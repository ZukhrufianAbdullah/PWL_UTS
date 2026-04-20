<?php

namespace App\Filament\Resources\Kategoris\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;

class KategoriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kategori_kode')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true),
                TextInput::make('kategori_nama')
                    ->required()
                    ->maxLength(100),
            ]);
    }
}
