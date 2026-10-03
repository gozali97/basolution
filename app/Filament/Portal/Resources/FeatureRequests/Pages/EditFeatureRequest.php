<?php

namespace App\Filament\Portal\Resources\FeatureRequests\Pages;

use App\Filament\Portal\Resources\FeatureRequests\FeatureRequestResource;
use Filament\Resources\Pages\EditRecord;

class EditFeatureRequest extends EditRecord
{
    protected static string $resource = FeatureRequestResource::class;

    protected function getRedirectUrl(): ?string
    {
        return $this->getResourceUrl('view', ['record' => $this->getRecord()]);
    }
}
