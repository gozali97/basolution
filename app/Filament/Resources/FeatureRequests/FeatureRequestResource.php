<?php

namespace App\Filament\Resources\FeatureRequests;

use App\Enums\RequestStatus;
use App\Filament\Resources\FeatureRequests\Pages\EditFeatureRequest;
use App\Filament\Resources\FeatureRequests\Pages\ListFeatureRequests;
use App\Filament\Resources\FeatureRequests\RelationManagers\NotesRelationManager;
use App\Models\ClientApp;
use App\Models\FeatureRequest;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FeatureRequestResource extends Resource
{
    protected static ?string $model = FeatureRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen proyek';

    protected static ?string $modelLabel = 'Permintaan fitur';

    protected static ?string $pluralModelLabel = 'Permintaan fitur';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = FeatureRequest::where('status', RequestStatus::Submitted)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Permintaan')->columns(2)->columnSpanFull()->schema([
                Select::make('client_app_id')->label('Aplikasi')
                    ->relationship('clientApp', 'name', fn (Builder $query) => $query->with('client'))
                    ->getOptionLabelFromRecordUsing(fn (ClientApp $record) => "{$record->client->name} - {$record->name}")
                    ->searchable()->preload()->required(),
                Select::make('type')->label('Jenis')->options(FeatureRequest::TYPES)->default('feature')->required(),
                TextInput::make('title')->label('Judul')->required()->maxLength(255)->columnSpanFull(),
                Textarea::make('description')->label('Deskripsi')->required()->rows(5)->maxLength(10000)->columnSpanFull(),
                TextInput::make('budget')->label('Budget klien')->prefix('Rp')->integer()->minValue(0),
            ]),
            Section::make('Pengerjaan')->columns(3)->columnSpanFull()->schema([
                Select::make('status')->label('Status')->options(RequestStatus::class)
                    ->default(RequestStatus::Submitted)->required(),
                TextInput::make('agreed_price')->label('Harga disepakati')->prefix('Rp')->integer()->minValue(0),
                DatePicker::make('due_date')->label('Deadline'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('clientApp.client'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->wrap(),
                TextColumn::make('clientApp.client.name')->label('Klien'),
                TextColumn::make('clientApp.name')->label('Aplikasi'),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('agreed_price')->label('Harga')->money('IDR', locale: 'id', decimalPlaces: 0)->placeholder('-'),
                TextColumn::make('due_date')->label('Deadline')->date('d M Y')->sortable()->placeholder('-'),
                TextColumn::make('created_at')->label('Diajukan')->date('d M Y')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(RequestStatus::class),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            NotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeatureRequests::route('/'),
            'edit' => EditFeatureRequest::route('/{record}/edit'),
        ];
    }
}
