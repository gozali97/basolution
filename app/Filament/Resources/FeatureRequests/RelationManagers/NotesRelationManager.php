<?php

namespace App\Filament\Resources\FeatureRequests\RelationManagers;

use App\Models\FeatureRequestNote;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Shared by the admin edit page and the client portal view page. */
class NotesRelationManager extends RelationManager
{
    protected static string $relationship = 'notes';

    protected static ?string $title = 'Catatan progres';

    protected static ?string $modelLabel = 'catatan';

    // The portal shows notes on a View page, which is read-only by default.
    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Textarea::make('body')->label('Catatan')->required()->rows(4)->maxLength(5000)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user.roles'))
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25])
            ->columns([
                TextColumn::make('body')->label('Catatan')->wrap(),
                TextColumn::make('user.name')->label('Oleh')->placeholder('Sistem')
                    ->description(fn (FeatureRequestNote $record) => match (true) {
                        $record->user === null => null,
                        $record->user->hasRole('client') => 'Klien',
                        default => 'Tim BASolution',
                    }),
                TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i'),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah catatan')
                    ->mutateDataUsing(fn (array $data) => [...$data, 'user_id' => auth()->id()]),
            ]);
    }
}
