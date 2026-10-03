<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\ManageServices;
use App\Models\Service;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static ?string $modelLabel = 'Layanan';

    protected static ?string $pluralModelLabel = 'Layanan';

    protected static ?int $navigationSort = 1;

    public const ICONS = [
        'code-bracket' => 'Kode / Web',
        'device-phone-mobile' => 'Mobile',
        'building-office-2' => 'Bisnis / ERP',
        'paint-brush' => 'Desain',
        'cloud' => 'Cloud',
        'server-stack' => 'Server',
        'cpu-chip' => 'Teknologi',
        'shield-check' => 'Keamanan',
        'chart-bar' => 'Analitik',
        'globe-alt' => 'Internet',
        'light-bulb' => 'Konsultasi',
        'cog-6-tooth' => 'Integrasi',
        'check-badge' => 'Jaminan',
        'clock' => 'Waktu',
        'chat-bubble-left-right' => 'Diskusi',
        'banknotes' => 'Biaya',
        'user-group' => 'Tim',
        'calendar-days' => 'Jadwal',
        'map-pin' => 'Lokasi',
    ];

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Judul')->required()->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            TextInput::make('slug')->required()->maxLength(255)->alphaDash()->unique(ignoreRecord: true),
            Select::make('icon')->label('Ikon')->options(self::ICONS)->required(),
            FileUpload::make('image')->label('Gambar')->image()->maxSize(2048)->directory('services'),
            Textarea::make('excerpt')->label('Ringkasan')->maxLength(500)->rows(2)->columnSpanFull(),
            RichEditor::make('body')->label('Deskripsi')->columnSpanFull(),
            Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('image')->label('Gambar'),
                TextColumn::make('title')->label('Judul')->searchable(),
                ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->recordActions([
                EditAction::make()->modalWidth(Width::FourExtraLarge),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServices::route('/'),
        ];
    }
}
