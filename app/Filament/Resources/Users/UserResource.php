<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    public const ROLES = ['admin' => 'Admin', 'client' => 'Klien'];

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'Akun';

    protected static ?string $pluralModelLabel = 'Akun';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama')->required()->maxLength(255),
            TextInput::make('email')->label('Email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
            TextInput::make('password')->label('Password')->password()->revealable()
                ->required(fn (string $operation) => $operation === 'create')
                ->dehydrated(fn (?string $state) => filled($state))
                ->minLength(8)->maxLength(255)
                ->helperText(fn (string $operation) => $operation === 'edit' ? 'Kosongkan jika tidak diganti.' : null),
            // Role names as values; saved through Spatie instead of a raw column.
            Select::make('role')->label('Role')->options(self::ROLES)->required()->live()
                ->afterStateHydrated(fn (Select $component, ?User $record) => $component->state($record?->getRoleNames()->first()))
                ->afterStateUpdated(fn (?string $state, Set $set) => $state === 'client' ? null : $set('client_id', null))
                ->dehydrated(false)
                ->saveRelationshipsUsing(fn (User $record, ?string $state) => $record->syncRoles([$state]))
                // Prevents an admin from locking themselves out of /admin.
                ->disabled(fn (?User $record) => $record?->is(auth()->user())),
            Select::make('client_id')->label('Perusahaan klien')->relationship('client', 'name')
                ->searchable()->preload()
                ->visible(fn (Get $get) => $get('role') === 'client')
                ->required(fn (Get $get) => $get('role') === 'client')
                ->dehydratedWhenHidden()
                ->helperText('Akun ini hanya melihat aplikasi dan permintaan milik perusahaan ini.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['roles', 'client']))
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('roles.name')->label('Role')->badge()
                    ->formatStateUsing(fn (string $state) => self::ROLES[$state] ?? $state),
                TextColumn::make('client.name')->label('Perusahaan')->placeholder('-'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (User $record) => $record->is(auth()->user())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
