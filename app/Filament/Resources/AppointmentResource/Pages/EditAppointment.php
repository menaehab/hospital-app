<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use Filament\Actions;
use App\Models\Appointment;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\AppointmentResource;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function mutateFormDataBeforeSave(array $data): array
    {
    if (isset($data['status']) && $data['status'] === 'in_session') {
        $existingInSession = Appointment::where('status', 'in_session')
            ->whereHas('visitType.doctor', function ($query) {
                $query->where('id', $this->record->visitType->doctor_id);
            })
            ->first();

        if ($existingInSession) {
            $existingInSession->update([
                'status' => 'finished',
                'end_time' => now(),
                'start_time' => $existingInSession->start_time ?? $existingInSession->created_at,
            ]);
        }

        $data['start_time'] = now();
    }

    if (isset($data['status']) && $data['status'] === 'finished') {
        $record = static::getRecord();
        $data['start_time'] = $record->start_time ?? $record->created_at;
        $data['end_time'] = now();
    }

        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
