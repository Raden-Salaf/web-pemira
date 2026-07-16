<?php

namespace App\Filament\Resources\Pemilihs\Pages;

use App\Filament\Resources\Pemilihs\PemilihResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemilih extends EditRecord
{
    protected static string $resource = PemilihResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
