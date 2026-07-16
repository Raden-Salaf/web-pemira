<?php

namespace App\Filament\Resources\Pemilihans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PemilihanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Pemilihan')
                    ->placeholder('Contoh: Pemira BEM 2026')
                    ->required()
                    ->maxLength(255),

                Select::make('jenis')
                    ->label('Jenis Pemilihan')
                    ->options([
                        'bem' => 'Pemilihan BEM',
                        'hima' => 'Pemilihan Himpunan/Organisasi',
                        'umum' => 'Umum/Lainnya',
                    ])
                    ->required()
                    ->default('umum'),

                Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->placeholder('Deskripsi singkat acara pemilihan ini')
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('Status Pemilihan')
                    ->options([
                        'draft' => 'Draft (belum dimulai)',
                        'berlangsung' => 'Berlangsung (voting dibuka)',
                        'selesai' => 'Selesai',
                    ])
                    ->required()
                    ->default('draft')
                    ->helperText('Status ini dikombinasikan dengan jadwal waktu di bawah untuk menentukan apakah voting benar-benar terbuka.'),

                // FIELD BARU: menentukan dari mana data calon pemilih diambil.
                // 'excel' = semua mahasiswa hasil import Excel boleh coba daftar.
                // 'manual' = HARUS ada di whitelist khusus (dikelola lewat
                // menu "Whitelist Pemilih Manual" di sidebar), cocok buat
                // pemilihan skala kecil yang rawan disusupi orang luar lingkup.
                Select::make('sumber_pemilih')
                    ->label('Sumber Data Pemilih')
                    ->options([
                        'excel' => 'Data Excel (semua mahasiswa hasil import boleh daftar)',
                        'manual' => 'Whitelist Manual (hanya yang didaftarkan admin secara khusus)',
                    ])
                    ->default('excel')
                    ->required()
                    ->helperText('Pilih "Whitelist Manual" untuk pemilihan skala kecil (Hima/organisasi) supaya tidak disusupi pendaftar dari luar lingkup.'),

                DateTimePicker::make('waktu_mulai')
                    ->label('Waktu Mulai Voting')
                    ->seconds(false),

                DateTimePicker::make('waktu_selesai')
                    ->label('Waktu Selesai Voting')
                    ->seconds(false)
                    ->after('waktu_mulai'),

                Toggle::make('is_active')
                    ->label('Tampilkan sebagai Pemilihan Aktif')
                    ->helperText('Hanya 1 pemilihan yang sebaiknya aktif dalam satu waktu — ini yang akan ditampilkan ke halaman publik mahasiswa.')
                    ->default(false),
            ]);
    }
}