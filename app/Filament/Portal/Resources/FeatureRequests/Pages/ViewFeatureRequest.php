<?php

namespace App\Filament\Portal\Resources\FeatureRequests\Pages;

use App\Filament\Portal\Resources\FeatureRequests\FeatureRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFeatureRequest extends ViewRecord
{
    protected static string $resource = FeatureRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->visible(fn () => FeatureRequestResource::canEdit($this->getRecord())),
        ];
    }
}
