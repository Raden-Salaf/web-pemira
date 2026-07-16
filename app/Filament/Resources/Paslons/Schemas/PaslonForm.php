<?php

namespace App\Filament\Resources\Paslons\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PaslonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // relationship('pemilihan', 'nama') -> tampilkan kolom "nama" sebagai label opsi,
                // BUKAN "id" seperti versi auto-generate (admin gak akan ngerti kalau cuma lihat angka ID)
                Select::make('pemilihan_id')
                    ->label('Pemilihan')
                    ->relationship('pemilihan', 'nama')
                    ->required()
                    ->searchable(),

                TextInput::make('nomor_urut')
                    ->label('Nomor Urut')
                    ->numeric()
                    ->required()
                    ->minValue(1),

                TextInput::make('nama_ketua')
                    ->label('Nama Ketua/Kandidat Utama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nama_wakil')
                    ->label('Nama Wakil (opsional)')
                    ->maxLength(255),

                // INI YANG PENTING: ganti dari TextInput jadi FileUpload
                // supaya muncul komponen upload gambar, bukan kotak ketik teks biasa
                FileUpload::make('foto')
                    ->label('Foto Paslon')
                    ->image()
                    // WAJIB eksplisit disk 'public', karena default Filament ('local') sejak
                    // Laravel 11+ menyimpan file ke storage/app/private -- yang TIDAK bisa
                    // diakses lewat URL publik. Foto paslon harus bisa dilihat semua orang
                    // di halaman publik, jadi HARUS disk 'public'.
                    ->disk('public')
                    ->directory('paslon-photos')
                    ->imageEditor()
                    ->required(),

                Textarea::make('visi')
                    ->required()
                    ->columnSpanFull()
                    ->rows(3),

                Textarea::make('misi')
                    ->required()
                    ->columnSpanFull()
                    ->rows(5),

                // Ganti dari Textarea biasa jadi Repeater, supaya program kerja
                // bisa diisi sebagai LIST poin-poin (disimpan sebagai array JSON),
                // bukan 1 blok teks panjang yang susah diformat ulang nanti
                Repeater::make('program_kerja')
                    ->label('Program Kerja')
                    ->schema([
                        TextInput::make('poin')
                            ->label('Poin Program Kerja')
                            ->required(),
                    ])
                    ->columnSpanFull()
                    ->addActionLabel('Tambah Poin Program Kerja')
                    ->defaultItems(1),

                TextInput::make('fakultas_asal')
                    ->label('Fakultas Asal')
                    ->maxLength(255),
            ]);
    }
}
