<?php

namespace App\Filament\Portal\Resources\FeatureRequests;

use App\Enums\RequestStatus;
use App\Filament\Portal\Resources\FeatureRequests\Pages\CreateFeatureRequest;
use App\Filament\Portal\Resources\FeatureRequests\Pages\EditFeatureRequest;
use App\Filament\Portal\Resources\FeatureRequests\Pages\ListFeatureRequests;
use App\Filament\Portal\Resources\FeatureRequests\Pages\ViewFeatureRequest;
use App\Filament\Resources\FeatureRequests\RelationManagers\NotesRelationManager;
use App\Models\FeatureRequest;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/** Client side. Every query is scoped to the signed-in user's client_id. */
class FeatureRequestResource extends Resource
{
    protected static ?string $model = FeatureRequest::class;

    protected static ?string $slug = 'permintaan';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?string $modelLabel = 'Permintaan';

    protected static ?string $pluralModelLabel = 'Permintaan saya';

    // Also scopes record route binding, so another client's id in the URL returns 404.
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->forClient(auth()->user()?->client_id);
    }

    public static function canEdit(Model $record): bool
    {
        return $record->status === RequestStatus::Submitted;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            // The scoped query also feeds Filament's "in" validation, so a forged app id fails.
            Select::make('client_app_id')->label('Aplikasi')
                ->relationship('clientApp', 'name', fn (Builder $query) => $query->where('client_id', auth()->user()?->client_id))
                ->preload()->required(),
            Select::make('type')->label('Jenis')->options(FeatureRequest::TYPES)->default('feature')->required(),
            TextInput::make('title')->label('Judul')->required()->maxLength(255)->columnSpanFull(),
            Textarea::make('description')->label('Deskripsi')->required()->rows(6)->maxLength(10000)->columnSpanFull()
                ->helperText('Jelaskan apa yang dibutuhkan dan siapa yang akan memakainya.'),
            TextInput::make('budget')->label('Perkiraan budget (opsional)')->prefix('Rp')->integer()->minValue(0),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Status pengerjaan')->columns(3)->columnSpanFull()->schema([
                TextEntry::make('status')->label('Status')->badge(),
                TextEntry::make('agreed_price')->label('Harga disepakati')
                    ->money('IDR', locale: 'id', decimalPlaces: 0)->placeholder('Belum ditentukan'),
                TextEntry::make('due_date')->label('Deadline')->date('d M Y')->placeholder('Belum ditentukan'),
            ]),
            Section::make('Detail permintaan')->columns(2)->columnSpanFull()->schema([
                TextEntry::make('clientApp.name')->label('Aplikasi'),
                TextEntry::make('type')->label('Jenis')->formatStateUsing(fn (string $state) => FeatureRequest::TYPES[$state] ?? $state),
                TextEntry::make('title')->label('Judul')->columnSpanFull(),
                // Escaped before nl2br, so the html() output only contains <br>.
                TextEntry::make('description')->label('Deskripsi')->columnSpanFull()
                    ->formatStateUsing(fn (string $state) => nl2br(e($state)))->html(),
                TextEntry::make('budget')->label('Perkiraan budget')->money('IDR', locale: 'id', decimalPlaces: 0)->placeholder('-'),
                TextEntry::make('created_at')->label('Diajukan')->dateTime('d M Y H:i'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('clientApp'))
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada permintaan')
            ->emptyStateDescription('Ajukan fitur baru atau perbaikan untuk aplikasi Anda.')
            ->columns([
                TextColumn::make('title')->label('Judul')->searchable()->wrap(),
                TextColumn::make('clientApp.name')->label('Aplikasi'),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('agreed_price')->label('Harga')->money('IDR', locale: 'id', decimalPlaces: 0)->placeholder('-'),
                TextColumn::make('due_date')->label('Deadline')->date('d M Y')->placeholder('-'),
                TextColumn::make('created_at')->label('Diajukan')->date('d M Y')->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
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
            'create' => CreateFeatureRequest::route('/create'),
            'view' => ViewFeatureRequest::route('/{record}'),
            'edit' => EditFeatureRequest::route('/{record}/edit'),
        ];
    }
}
