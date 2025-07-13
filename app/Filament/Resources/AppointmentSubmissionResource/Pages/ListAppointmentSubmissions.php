<?php

namespace App\Filament\Resources\AppointmentSubmissionResource\Pages;

use App\Filament\Resources\AppointmentSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppointmentSubmissions extends ListRecords
{
    protected static string $resource = AppointmentSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
