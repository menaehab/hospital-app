<?php

namespace App\Filament\Resources\MedicalTestResource\Pages;

use App\Filament\Resources\MedicalTestResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalTest extends CreateRecord
{
    protected static string $resource = MedicalTestResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
