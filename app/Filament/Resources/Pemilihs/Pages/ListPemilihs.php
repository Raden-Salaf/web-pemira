<?php

namespace App\Filament\Resources\Pemilihs\Pages;

use App\Filament\Resources\Pemilihs\PemilihResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemilihs extends ListRecords
{
    protected static string $resource = PemilihResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    public function getSubheading(): ?string
    {
        return 'Verifikasi pendaftar dari Daftar Pemilih Sementara (DPS) menjadi Daftar Pemilih Tetap (DPT).';
    }
}
