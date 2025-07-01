<?php

namespace App\Filament\Resources\VisitTypeResource\Pages;

use App\Filament\Resources\VisitTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVisitType extends EditRecord
{
    protected static string $resource = VisitTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
