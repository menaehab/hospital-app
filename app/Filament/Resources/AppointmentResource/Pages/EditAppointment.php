<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function mutateFormDataBeforeUpdate(array $data): array
    {
        if ($data['status'] === 'in_session') {
            $data['start_time'] = now();
        }

        if ($data['status'] === 'finished') {
            $data['start_time'] = $this->record->start_time ??  $this->record->created_at;
            $data['end_time'] = now();
        }

        return $data;
    }
}
