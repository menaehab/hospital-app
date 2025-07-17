<?php

namespace App\Filament\Resources\PrescriptionResource\Pages;

use App\Models\Food;
use Filament\Actions;
use App\Models\Timing;
use App\Models\Patient;
use App\Models\VitalSign;
use App\Models\Appointment;
use App\Models\Prescription;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PrescriptionResource;

class CreatePrescription extends CreateRecord
{
    public function mount(): void
    {
        parent::mount();

        if (request()->has('appointment_id')) {
            $appointmentId = request('appointment_id');
            $appointment = Appointment::with('patient')->find($appointmentId);

            if ($appointment) {
                $this->form->fill([
                    'appointment_id' => $appointmentId,
                    'patient_name' => $appointment->patient->name,
                    'patient_age' => $appointment->patient->age,
                    'patient_gender' => $appointment->patient->gender,
                    'has_diabetes' => $appointment->patient->has_diabetes,
                    'has_heart_disease' => $appointment->patient->has_heart_disease,
                    'has_high_blood_pressure' => $appointment->patient->has_high_blood_pressure,
                    'heart_rate' => $appointment->heart_rate,
                    'temperature' => $appointment->temperature,
                    'oxygen_saturation' => $appointment->oxygen_saturation,
                    'blood_pressure' => $appointment->blood_pressure,
                ]);
            }
        }
    }


    protected static string $resource = PrescriptionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = parent::handleRecordCreation($data);

        $record->appointment->update(['status' => 'finished']);

        return $record;
    }


    protected function afterCreate(): void
    {
        $prescription = $this->getRecord();
        $data = $this->form->getState();
        $appointment = $prescription->appointment;

        // Update patient info
        $patientData = [];
        if (isset($data['has_diabetes'])) $patientData['has_diabetes'] = $data['has_diabetes'];
        if (isset($data['has_heart_disease'])) $patientData['has_heart_disease'] = $data['has_heart_disease'];
        if (isset($data['has_high_blood_pressure'])) $patientData['has_high_blood_pressure'] = $data['has_high_blood_pressure'];

        if (!empty($patientData)) {
            $appointment->patient()->update($patientData);
        }

        // Update vital signs
        $appointmentData = [];
        if (isset($data['heart_rate'])) $appointmentData['heart_rate'] = $data['heart_rate'];
        if (isset($data['temperature'])) $appointmentData['temperature'] = $data['temperature'];
        if (isset($data['oxygen_saturation'])) $appointmentData['oxygen_saturation'] = $data['oxygen_saturation'];
        if (isset($data['blood_pressure_diastolic']))
            $appointmentData['blood_pressure'] = $data['blood_pressure_diastolic'];
        if (isset($data['blood_pressure_systolic']))
            $appointmentData['blood_pressure'] = $data['blood_pressure_systolic'];
        if (!empty($appointmentData)) {
            $appointmentData['appointment_id'] = $appointment->id;
            VitalSign::create($appointmentData);
        }

        // Attach medicines
        if (!empty($data['medicines'])) {
            $medicines = [];
            foreach ($data['medicines'] as $medicine) {
                if ($medicine['timing_type'] == 'other' && !empty($medicine['timing_custom'])) {
                    $timing = Timing::firstOrCreate(['label' => $medicine['timing_custom']]);
                    $medicine['timing_type'] = $timing->label;
                }
                $medicines[$medicine['medicine_id']] = [
                    'timing_type' => $medicine['timing_type'],
                    'time_per_day' => $medicine['time_per_day'],
                ];
            }
            $prescription->medicines()->sync($medicines);
        }

        // Attach medical tests
        if (!empty($data['medical_tests'])) {
            $prescription->medicalTests()->sync(array_filter($data['medical_tests']));
        }

        // Attach radiology tests
        if (!empty($data['radiology_tests'])) {
            $prescription->radiologyTests()->sync(array_filter($data['radiology_tests']));
        }

        // Attach foods
        if (!empty($data['foods'])) {
            $foods = collect($data['foods'])->mapWithKeys(function ($food) {
                return [$food['food_id'] => ['allow' => $food['allow']]];
            });
            $prescription->foods()->sync($foods);
        }
    }
}