<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\AppointmentResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;

use App\Filament\Resources\AppointmentResource\Widgets\AppointmentsStatsOverview;

class ListAppointments extends ListRecords
{

    use ExposesTableToWidgets;
    protected static string $resource = AppointmentResource::class;

    public function getHeaderWidgets(): array
    {
        return [
            AppointmentsStatsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getListeners(): array
    {
        return [
            'echo-private:appointments,AppointmentUpdated' => '$refresh',
        ];
    }

}