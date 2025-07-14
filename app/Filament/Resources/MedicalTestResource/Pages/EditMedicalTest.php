<?php

namespace App\Filament\Resources\MedicalTestResource\Pages;

use App\Filament\Resources\MedicalTestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMedicalTest extends EditRecord
{
    protected static string $resource = MedicalTestResource::class;

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
