<?php

namespace App\Filament\Resources\ClienicResource\Pages;

use App\Filament\Resources\ClienicResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClienics extends ListRecords
{
    protected static string $resource = ClienicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
