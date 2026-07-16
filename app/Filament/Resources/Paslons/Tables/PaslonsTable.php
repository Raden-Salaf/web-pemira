<?php

namespace App\Filament\Resources\Paslons\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaslonsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // ImageColumn: nampilin foto sebagai thumbnail kecil di tabel
                // circular() bikin fotonya bulat (biar konsisten kayak avatar)
                ImageColumn::make('foto')
                    ->label('Foto')
                    // Sama seperti fix di PaslonForm.php sebelumnya: WAJIB disk('public')
                    // secara eksplisit, karena file foto disimpan di storage/app/public/
                    // (bukan disk default 'local' yang nunjuk ke storage/app/private/)
                    ->disk('public')
                    ->circular(),

                TextColumn::make('nomor_urut')
                    ->label('No. Urut')
                    ->sortable(),

                // Kita gabung nama_ketua & nama_wakil jadi 1 tampilan "Ketua & Wakil"
                // pakai formatStateUsing() — closure ini nerima $record (bukan cuma $state)
                // supaya kita bisa akses kolom LAIN selain kolom utama yang lagi di-render
                TextColumn::make('nama_ketua')
                    ->label('Nama Paslon')
                    ->formatStateUsing(
                        fn($record) => $record->nama_wakil
                            ? "{$record->nama_ketua} & {$record->nama_wakil}"
                            : $record->nama_ketua
                    )
                    ->searchable(['nama_ketua', 'nama_wakil']), // search bisa cocok ke salah satu nama

                TextColumn::make('pemilihan.nama')
                    ->label('Pemilihan')
                    ->badge()
                    ->searchable(),

                TextColumn::make('fakultas_asal')
                    ->label('Fakultas')
                    ->toggleable(), // bisa disembunyikan/dimunculkan admin lewat menu kolom

                // KOLOM PERHITUNGAN — ini yang jawab requirement poin 5 langsung di tabel admin
                // getStateUsing() dipakai untuk kolom yang NILAINYA BUKAN kolom database asli,
                // melainkan hasil PERHITUNGAN lewat method yang udah kita tulis di Model Paslon
                TextColumn::make('jumlah_suara')
                    ->label('Jumlah Suara')
                    ->getStateUsing(fn($record) => $record->jumlahSuara())
                    ->badge()
                    ->color('info'),

                TextColumn::make('persentase_suara')
                    ->label('Persentase')
                    ->getStateUsing(fn($record) => $record->persentaseSuara() . '%')
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('nomor_urut', 'asc') // urutkan berdasarkan nomor urut paslon
            // Empty state custom: muncul kalau BELUM ada paslon sama sekali,
            // biar admin langsung ngerti harus ngapain, bukan cuma lihat "No records"
            ->emptyStateHeading('Belum ada paslon terdaftar')
            ->emptyStateDescription('Tambahkan paslon untuk salah satu pemilihan menggunakan tombol di atas.')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
