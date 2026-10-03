<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\ManageClients;
use App\Models\Client;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static ?string $modelLabel = 'Klien';

    protected static ?string $pluralModelLabel = 'Klien';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama')->required()->maxLength(255),
            TextInput::make('url')->label('Website')->url()->maxLength(255),
            FileUpload::make('logo')->label('Logo')->image()->maxSize(2048)->directory('clients')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        // Clients with apps or accounts are protected by restrictOnDelete FKs; block deletion in the UI too.
        $inUse = fn (Client $record) => $record->apps_count > 0 || $record->users_count > 0;

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['apps', 'users']))
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('logo')->label('Logo'),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('url')->label('Website')->toggleable(),
                TextColumn::make('apps_count')->label('Aplikasi')->toggleable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden($inUse),
            ])
            ->checkIfRecordIsSelectableUsing(fn (Client $record) => ! $inUse($record))
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClients::route('/'),
        ];
    }
}
