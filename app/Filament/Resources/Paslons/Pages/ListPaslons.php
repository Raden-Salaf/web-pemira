<?php

namespace App\Filament\Resources\Paslons\Pages;

use App\Filament\Resources\Paslons\PaslonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaslons extends ListRecords
{
    protected static string $resource = PaslonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    // Method ini nambahin subjudul kecil di bawah judul halaman, ngasih
    // konteks singkat soal apa fungsi halaman ini -- detail kecil yang
    // bikin panel terasa lebih "dipikirin", bukan generic CRUD kosongan
    public function getSubheading(): ?string
    {
        return 'Kelola kandidat & pasangan calon untuk setiap pemilihan.';
    }
}
