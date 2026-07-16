<?php

namespace App\Filament\Resources\Pemilihans\Pages;

use App\Filament\Resources\Pemilihans\PemilihanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemilihan extends EditRecord
{
    protected static string $resource = PemilihanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
