<?php

namespace App\Filament\Resources\PrescriptionResource\Pages;

use App\Filament\Resources\PrescriptionResource;
use Filament\Actions;
use App\Models\Food;
use App\Models\Timing;
use Filament\Resources\Pages\EditRecord;

class EditPrescription extends EditRecord
{

    protected static string $resource = PrescriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeFill(): void
    {
        $prescription = $this->getRecord();
        $appointment = $prescription->appointment;
        $patient = $appointment->patient;

        $this->form->fill([
            'appointment_id' => $prescription->appointment_id,
            'patient_name' => $patient->name,
            'patient_age' => $patient->age,
            'patient_gender' => $patient->gender,
            'has_diabetes' => $patient->has_diabetes,
            'has_heart_disease' => $patient->has_heart_disease,
            'has_high_blood_pressure' => $patient->has_high_blood_pressure,
            'heart_rate' => $appointment->heart_rate,
            'temperature' => $appointment->temperature,
            'oxygen_saturation' => $appointment->oxygen_saturation,
            'blood_pressure' => $appointment->blood_pressure,
            'medicines' => $prescription->medicines->map(function ($medicine) {
                return [
                    'medicine_id' => $medicine->id,
                    'timing_type' => $medicine->pivot->timing_type,
                    'time_per_day' => $medicine->pivot->time_per_day,
                    'notes' => $medicine->pivot->notes,
                ];
            })->toArray(),
            'medical_tests' => $prescription->medicalTests->pluck('id')->toArray(),
            'radiology_tests' => $prescription->radiologyTests->pluck('id')->toArray(),
            'foods' => $prescription->foods->map(function ($food) {
                return [
                    'food' => $food->id,
                    'allow' => $food->pivot->allow,
                ];
            })->toArray(),
            'notes' => $prescription->notes,
        ]);
    }

    protected function afterSave(): void
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
        if (isset($data['blood_pressure'])) $appointmentData['blood_pressure'] = $data['blood_pressure'];

        if (!empty($appointmentData)) {
            $appointment->update($appointmentData);
        }

        // Sync medicines
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
                    'notes' => $medicine['notes'] ?? null,
                ];
            }
            $prescription->medicines()->sync($medicines);
        } else {
            $prescription->medicines()->sync([]);
        }

        // Sync medical tests
        if (!empty($data['medical_tests'])) {
            $prescription->medicalTests()->sync($data['medical_tests']);
        } else {
            $prescription->medicalTests()->sync([]);
        }

        // Sync radiology tests
        if (!empty($data['radiology_tests'])) {
            $prescription->radiologyTests()->sync($data['radiology_tests']);
        } else {
            $prescription->radiologyTests()->sync([]);
        }

        // Sync foods
        if (!empty($data['foods'])) {
            $foods = collect($data['foods'])->mapWithKeys(function ($food) {
                return [$food['food'] => ['allow' => $food['allow']]];
            });
            $prescription->foods()->sync($foods);
        } else {
            $prescription->foods()->sync([]);
        }
    }
}