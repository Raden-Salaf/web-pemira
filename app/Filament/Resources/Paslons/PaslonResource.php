<?php

namespace App\Filament\Resources\Paslons;

use App\Filament\Resources\Paslons\Pages\CreatePaslon;
use App\Filament\Resources\Paslons\Pages\EditPaslon;
use App\Filament\Resources\Paslons\Pages\ListPaslons;
use App\Filament\Resources\Paslons\Schemas\PaslonForm;
use App\Filament\Resources\Paslons\Tables\PaslonsTable;
use App\Models\Paslon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaslonResource extends Resource
{
    protected static ?string $model = Paslon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'nama_ketua';

    public static function form(Schema $schema): Schema
    {
        return PaslonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaslonsTable::configure($table);
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
            'index' => ListPaslons::route('/'),
            'create' => CreatePaslon::route('/create'),
            'edit' => EditPaslon::route('/{record}/edit'),
        ];
    }
}
