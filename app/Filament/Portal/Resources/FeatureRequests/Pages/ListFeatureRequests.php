<?php

namespace App\Filament\Portal\Resources\FeatureRequests\Pages;

use App\Filament\Portal\Resources\FeatureRequests\FeatureRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFeatureRequests extends ListRecords
{
    protected static string $resource = FeatureRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Ajukan permintaan'),
        ];
    }
}
