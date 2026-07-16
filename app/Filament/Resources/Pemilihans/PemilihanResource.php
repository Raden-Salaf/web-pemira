<?php

namespace App\Filament\Resources\Pemilihans;

use App\Filament\Resources\Pemilihans\Pages\CreatePemilihan;
use App\Filament\Resources\Pemilihans\Pages\EditPemilihan;
use App\Filament\Resources\Pemilihans\Pages\ListPemilihans;
use App\Filament\Resources\Pemilihans\Schemas\PemilihanForm;
use App\Filament\Resources\Pemilihans\Tables\PemilihansTable;
use App\Models\Pemilihan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PemilihanResource extends Resource
{
    protected static ?string $model = Pemilihan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    /**
     * Menentukan kolom mana yang dipakai sebagai "judul" record ini di berbagai
     * tempat UI Filament (breadcrumb, notifikasi, global search, dll).
     * Kita pakai kolom 'nama' karena itu yang paling deskriptif buat manusia
     * (dibanding cuma nampilin ID angka doang).
     */
    public static function getRecordTitleAttribute(): ?string
    {
        return 'nama';
    }

    public static function form(Schema $schema): Schema
    {
        return PemilihanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PemilihansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPemilihans::route('/'),
            'create' => CreatePemilihan::route('/create'),
            'edit' => EditPemilihan::route('/{record}/edit'),
        ];
    }
}
