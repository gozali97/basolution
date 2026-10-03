<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Resources\Projects\Pages\ManageProjects;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $modelLabel = 'Portofolio';

    protected static ?string $pluralModelLabel = 'Portofolio';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Judul')->required()->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
            TextInput::make('slug')->required()->maxLength(255)->alphaDash()->unique(ignoreRecord: true),
            TextInput::make('category')->label('Kategori')->required()->maxLength(100)
                ->datalist(fn () => Project::distinct()->orderBy('category')->pluck('category')->all()),
            TextInput::make('client')->label('Klien')->maxLength(255),
            TagsInput::make('tech_stack')->label('Teknologi')->columnSpanFull(),
            FileUpload::make('cover')->label('Cover')->image()->maxSize(2048)->directory('projects'),
            FileUpload::make('gallery')->label('Galeri')->image()->multiple()->reorderable()->maxFiles(8)->maxSize(2048)->directory('projects'),
            Textarea::make('excerpt')->label('Ringkasan')->maxLength(500)->rows(2)->columnSpanFull(),
            RichEditor::make('body')->label('Deskripsi')->columnSpanFull(),
            TextInput::make('url')->label('Link proyek')->url()->maxLength(255),
            Toggle::make('is_featured')->label('Tampil di beranda'),
            Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                ImageColumn::make('cover')->label('Cover'),
                TextColumn::make('title')->label('Judul')->searchable(),
                TextColumn::make('category')->label('Kategori')->badge(),
                TextColumn::make('client')->label('Klien')->toggleable(),
                ToggleColumn::make('is_featured')->label('Beranda'),
                ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([
                SelectFilter::make('category')->label('Kategori')
                    ->options(fn () => Project::distinct()->orderBy('category')->pluck('category', 'category')->all()),
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
            'index' => ManageProjects::route('/'),
        ];
    }
}
