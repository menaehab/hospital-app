<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use Filament\Actions;
use App\Models\Patient;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AppointmentResource;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        // creating or getting patient
        if (empty($data['patient_id'])) {
            if (empty($data['name'])) {
                throw new \Exception(__('keywords.choose_patient_or_fill_patient_info'));
            }
            $patient = Patient::create([
                'name' => $data['name'],
                'age' => $data['age'],
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);
        } else {
            $patient = Patient::find($data['patient_id']);
        }

        $data['patient_id'] = $patient->id;

        unset($data['name'], $data['age'], $data['phone'], $data['address']);


        // status
        if (isset($data['status']) && $data['status'] === 'in_session') {
            $data['start_time'] = now();
        }

        if (isset($data['status']) && $data['status'] === 'finished') {
            $data['start_time'] = now();
            $data['end_time'] = now();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
