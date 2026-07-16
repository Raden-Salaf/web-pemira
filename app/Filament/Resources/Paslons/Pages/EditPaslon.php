<?php

namespace App\Filament\Resources\Paslons\Pages;

use App\Filament\Resources\Paslons\PaslonResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPaslon extends EditRecord
{
    protected static string $resource = PaslonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
