<?php

namespace App\Filament\Resources\Pemilihs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PemilihForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('mahasiswa_id')
                    ->label('Mahasiswa')
                    ->relationship('mahasiswa', 'nama')
                    ->searchable()
                    ->required()
                    ->disabled(),

                Select::make('pemilihan_id')
                    ->label('Pemilihan')
                    ->relationship('pemilihan', 'nama')
                    ->required()
                    ->disabled(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'dps' => 'DPS (Daftar Pemilih Sementara)',
                        'dpt' => 'DPT (Daftar Pemilih Tetap)',
                        'ditolak' => 'Ditolak',
                    ])
                    ->required(),

                Toggle::make('sudah_memilih')
                    ->label('Sudah Memilih')
                    ->disabled()
                    ->helperText('Status ini otomatis terisi sistem saat mahasiswa mencoblos, tidak bisa diubah manual di sini.'),
            ]);
    }
}