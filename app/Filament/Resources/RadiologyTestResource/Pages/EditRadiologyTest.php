<?php

namespace App\Filament\Resources\RadiologyTestResource\Pages;

use App\Filament\Resources\RadiologyTestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRadiologyTest extends EditRecord
{
    protected static string $resource = RadiologyTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}