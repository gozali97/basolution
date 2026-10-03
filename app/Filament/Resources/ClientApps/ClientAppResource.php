<?php

namespace App\Filament\Resources\ClientApps;

use App\Filament\Resources\ClientApps\Pages\ManageClientApps;
use App\Models\ClientApp;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ClientAppResource extends Resource
{
    protected static ?string $model = ClientApp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDevicePhoneMobile;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen proyek';

    protected static ?string $modelLabel = 'Aplikasi klien';

    protected static ?string $pluralModelLabel = 'Aplikasi klien';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('client_id')->label('Klien')->relationship('client', 'name')->searchable()->preload()->required(),
            TextInput::make('name')->label('Nama aplikasi')->required()->maxLength(255),
            TextInput::make('url')->label('URL')->url()->maxLength(255),
            Textarea::make('notes')->label('Catatan internal')->rows(3)->maxLength(5000)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->label('Aplikasi')->searchable(),
                TextColumn::make('client.name')->label('Klien')->searchable(),
                TextColumn::make('url')->label('URL')->toggleable(),
                TextColumn::make('feature_requests_count')->label('Permintaan')->counts('featureRequests'),
            ])
            ->filters([
                SelectFilter::make('client_id')->label('Klien')->relationship('client', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                // The FK is restrictOnDelete; hide instead of letting the DB throw.
                DeleteAction::make()->hidden(fn (ClientApp $record) => $record->feature_requests_count > 0),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClientApps::route('/'),
        ];
    }
}
