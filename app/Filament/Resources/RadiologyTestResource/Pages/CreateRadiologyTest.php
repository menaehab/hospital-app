<?php

namespace App\Filament\Resources\RadiologyTestResource\Pages;

use App\Filament\Resources\RadiologyTestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRadiologyTest extends CreateRecord
{
    protected static string $resource = RadiologyTestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
