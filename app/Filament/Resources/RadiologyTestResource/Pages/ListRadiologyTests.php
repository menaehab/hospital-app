<?php

namespace App\Filament\Resources\RadiologyTestResource\Pages;

use App\Filament\Resources\RadiologyTestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRadiologyTests extends ListRecords
{
    protected static string $resource = RadiologyTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
