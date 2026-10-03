<?php

namespace App\Filament\Portal\Resources\FeatureRequests\Pages;

use App\Enums\RequestStatus;
use App\Filament\Portal\Resources\FeatureRequests\FeatureRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFeatureRequest extends CreateRecord
{
    protected static string $resource = FeatureRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set server-side last so form input can never override them.
        return [...$data, 'user_id' => auth()->id(), 'status' => RequestStatus::Submitted];
    }
}
