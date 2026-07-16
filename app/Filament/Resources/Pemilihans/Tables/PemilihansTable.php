<?php

namespace App\Filament\Resources\Pemilihans\Tables;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class PemilihansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jenis')
                    ->badge(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'berlangsung' => 'success',
                        'selesai' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('waktu_mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('waktu_selesai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->recordActions([
                // TOMBOL ARSIPKAN.
                // ->visible() memastikan tombol ini CUMA muncul kalau pemilihan
                // masih is_active = true -- gak masuk akal nampilin tombol
                // "Arsipkan" buat pemilihan yang udah diarsipkan sebelumnya.
                Action::make('arsipkan')
                    ->label('Arsipkan')
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->visible(fn($record) => $record->is_active)
                    ->requiresConfirmation()
                    ->modalDescription('Pemilihan ini akan disembunyikan dari halaman publik dan ditandai selesai. Data paslon, pemilih, dan suara TETAP tersimpan sebagai histori, tidak dihapus.')
                    ->action(function ($record) {
                        $record->update([
                            'is_active' => false,
                            'status' => 'selesai',
                        ]);

                        Notification::make()
                            ->title("\"{$record->nama}\" berhasil diarsipkan")
                            ->success()
                            ->send();
                    }),

                // TOMBOL DUPLIKAT.
                // Dipakai kalau admin mau bikin pemilihan BARU yang mirip
                // struktur/paslonnya dengan pemilihan lama (misal Pemira BEM
                // tahun ini mirip formatnya sama tahun lalu, tinggal ganti
                // nama-nama kandidat & foto).
                Action::make('duplikat')
                    ->label('Duplikat')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalDescription('Akan dibuat pemilihan baru dengan data paslon yang sama (bisa diedit setelahnya). Jadwal & status akan direset ke awal (draft), pemilih dan suara TIDAK ikut disalin.')
                    ->action(function ($record) {
                        // DB::transaction() memastikan proses duplikasi pemilihan
                        // BESERTA semua paslonnya itu "all or nothing" -- kalau
                        // di tengah proses ada error (misal duplikasi paslon ke-3
                        // gagal), SEMUA perubahan (termasuk pemilihan baru yang
                        // sudah sempat dibuat) otomatis dibatalkan, gak ada
                        // data "setengah jadi" yang nyangkut di database.
                        DB::transaction(function () use ($record) {
                            // replicate() bikin salinan record ini TANPA nyimpen
                            // ke database dulu -- semua kolom ke-copy PERSIS,
                            // KECUALI primary key (id) dan timestamps, karena itu
                            // otomatis di-generate baru pas kita save().
                            $pemilihanBaru = $record->replicate();
                            $pemilihanBaru->nama = $record->nama . ' (Salinan)';

                            // slug DIKOSONGKAN supaya method boot() di Model Pemilihan
                            // (yang sudah kita buat waktu bikin fitur slug) otomatis
                            // generate slug BARU dari nama baru ini -- mencegah
                            // slug bentrok dengan pemilihan asal.
                            $pemilihanBaru->slug = null;

                            // Reset semua status/jadwal ke kondisi "baru", karena
                            // pemilihan hasil duplikat ini belum tentu langsung
                            // dipakai sekarang -- admin perlu atur ulang jadwalnya
                            $pemilihanBaru->status = 'draft';
                            $pemilihanBaru->waktu_mulai = null;
                            $pemilihanBaru->waktu_selesai = null;
                            $pemilihanBaru->is_active = false;
                            $pemilihanBaru->save();

                            // Duplikat SEMUA paslon yang terkait ke pemilihan lama,
                            // arahkan pemilihan_id-nya ke pemilihan yang BARU dibuat.
                            // Foto ikut ke-copy referensinya (path file sama), admin
                            // bisa ganti foto satu-satu nanti kalau memang berbeda orang.
                            foreach ($record->paslons as $paslon) {
                                $paslonBaru = $paslon->replicate();
                                $paslonBaru->pemilihan_id = $pemilihanBaru->id;
                                $paslonBaru->save();
                            }

                            // SENGAJA TIDAK duplikat data 'pemilihs' (pendaftar DPS/DPT)
                            // maupun 'suaras' (hasil vote) -- itu data yang MEMANG
                            // spesifik untuk periode pemilihan yang lama, pemilihan
                            // baru harus mulai dari pendaftaran pemilih dari nol.
                        });

                        Notification::make()
                            ->title('Pemilihan berhasil diduplikat')
                            ->body('Silakan atur jadwal & aktifkan pemilihan baru saat siap digunakan.')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada pemilihan dibuat')
            ->emptyStateDescription('Klik "New pemilihan" untuk membuat acara pemira pertamamu.')
            ->emptyStateIcon('heroicon-o-flag');
    }
}
