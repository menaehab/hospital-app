<?php

namespace App\Filament\Resources\MedicalTestResource\Pages;

use App\Filament\Resources\MedicalTestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMedicalTests extends ListRecords
{
    protected static string $resource = MedicalTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
