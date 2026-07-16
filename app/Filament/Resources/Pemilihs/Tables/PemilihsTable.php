<?php

namespace App\Filament\Resources\Pemilihs\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PemilihsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mahasiswa.nim')
                    ->label('NIM')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mahasiswa.nama')
                    ->label('Nama')
                    ->searchable(),

                TextColumn::make('mahasiswa.jurusan')
                    ->label('Jurusan')
                    ->toggleable(),

                TextColumn::make('pemilihan.nama')
                    ->label('Pemilihan')
                    ->badge(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dps' => 'warning',
                        'dpt' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'dps' => 'DPS',
                        'dpt' => 'DPT',
                        'ditolak' => 'Ditolak',
                        default => $state,
                    }),

                IconColumn::make('sudah_memilih')
                    ->label('Sudah Memilih')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Waktu Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // Filter dropdown, biar admin bisa langsung lihat "yang masih DPS aja"
                // buat diproses satu-satu, tanpa harus scroll cari manual
                SelectFilter::make('status')
                    ->options([
                        'dps' => 'DPS',
                        'dpt' => 'DPT',
                        'ditolak' => 'Ditolak',
                    ]),

                SelectFilter::make('pemilihan_id')
                    ->label('Pemilihan')
                    ->relationship('pemilihan', 'nama'),
            ])
            ->recordActions([
                // Tombol aksi CEPAT per baris, tanpa perlu buka halaman edit dulu.
                // ->visible() memastikan tombol "Approve" cuma muncul kalau statusnya
                // masih DPS (gak masuk akal approve yang udah DPT/ditolak)
                Action::make('approve')
                    ->label('Jadikan DPT')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'dps')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'dpt',
                            'diverifikasi_at' => now(),
                        ]);

                        Notification::make()
                            ->title("{$record->mahasiswa->nama} berhasil diverifikasi jadi DPT")
                            ->success()
                            ->send();
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status === 'dps')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'ditolak']);

                        Notification::make()
                            ->title("Pendaftaran {$record->mahasiswa->nama} ditolak")
                            ->warning()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                // Bulk action: admin centang banyak baris DPS sekaligus, approve semua
                // dalam 1 klik -- penting banget kalau pendaftar ribuan orang
                BulkAction::make('approveSemua')
                    ->label('Jadikan DPT (Terpilih)')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        // Cuma proses yang statusnya masih 'dps', biar gak
                        // "menimpa" ulang yang udah DPT/ditolak sebelumnya
                        $records->where('status', 'dps')->each(function ($record) {
                            $record->update([
                                'status' => 'dpt',
                                'diverifikasi_at' => now(),
                            ]);
                        });

                        Notification::make()
                            ->title('Berhasil approve pemilih terpilih')
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}