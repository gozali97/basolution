<?php

namespace App\Filament\Resources\Messages;

use App\Filament\Resources\Messages\Pages\ManageMessages;
use App\Models\Message;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $modelLabel = 'Pesan';

    protected static ?string $pluralModelLabel = 'Pesan Masuk';

    protected static ?int $navigationSort = 0;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return Message::whereNull('read_at')->count() ?: null;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name')->label('Nama'),
            TextEntry::make('email')->label('Email')->copyable(),
            TextEntry::make('phone')->label('Telepon')->placeholder('-'),
            TextEntry::make('created_at')->label('Dikirim')->dateTime('d M Y H:i'),
            TextEntry::make('subject')->label('Subjek')->placeholder('-')->columnSpanFull(),
            TextEntry::make('body')->label('Pesan')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('read_at')->label('Dibaca')
                    ->state(fn (Message $record) => filled($record->read_at))->boolean(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('subject')->label('Subjek')->limit(40)->placeholder('-'),
                TextColumn::make('created_at')->label('Dikirim')->since()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('read_at')->label('Status')->nullable()
                    ->trueLabel('Sudah dibaca')->falseLabel('Belum dibaca'),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('markRead')->label('Tandai dibaca')->icon(Heroicon::OutlinedCheck)
                    ->visible(fn (Message $record) => blank($record->read_at))
                    ->action(fn (Message $record) => $record->update(['read_at' => now()])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMessages::route('/'),
        ];
    }
}
