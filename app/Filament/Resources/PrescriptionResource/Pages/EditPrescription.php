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
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        // Load the prescription with all necessary relationships
        $prescription = $this->getRecord()->load([
            'appointment.vitalSign',
            'appointment.patient',
            'medicines',
            'medicalTests',
            'radiologyTests',
            'foods'
        ]);

        $appointment = $prescription->appointment;
        $patient = $appointment->patient;
        $vitalSign = $appointment->vitalSign;

        // Initialize vital signs with null values if not exists
        $vitalSignData = [
            'heart_rate' => null,
            'temperature' => null,
            'oxygen_saturation' => null,
            'blood_pressure_systolic' => null,
            'blood_pressure_diastolic' => null,
        ];

        // Merge with existing vital sign data if available
        if ($vitalSign) {
            $vitalSignData = array_merge($vitalSignData, $vitalSign->toArray());
        }

        // Get the default form data from the record
        $data = $this->getRecord()->attributesToArray();
        // Add related data
        $data = array_merge($data, [
            'appointment_id' => $prescription->appointment_id,
            'patient_name' => $patient->name,
            'patient_age' => $patient->age,
            'patient_phone' => $patient->phone,
            'patient_code' => $patient->code,
            'patient_address' => $patient->address,
            'patient_gender' => $patient->gender,
            'has_diabetes' => $patient->has_diabetes,
            'has_heart_disease' => $patient->has_heart_disease,
            'has_high_blood_pressure' => $patient->has_high_blood_pressure,
            'heart_rate' => $vitalSignData['heart_rate'],
            'temperature' => $vitalSignData['temperature'],
            'oxygen_saturation' => $vitalSignData['oxygen_saturation'],
            'blood_pressure_systolic' => $vitalSignData['blood_pressure_systolic'],
            'blood_pressure_diastolic' => $vitalSignData['blood_pressure_diastolic'],
            'medicines' => $prescription->medicines->map(function ($medicine) {
                return [
                    'medicine_id' => $medicine->id,
                    'timing_type' => $medicine->pivot->timing_type,
                    'time_per_day' => $medicine->pivot->time_per_day,
                ];
            })->toArray(),
            'medical_tests' => $prescription->medicalTests->pluck('id')->toArray(),
            'radiology_tests' => $prescription->radiologyTests->pluck('id')->toArray(),
            'foods' => $prescription->foods->map(function ($food) {
                return [
                    'food_id' => $food->id,
                    'allow' => $food->pivot->allow,
                ];
            })->toArray(),
            'notes' => $prescription->notes,
        ]);

        $data = $this->mutateFormDataBeforeFill($data);
        $this->form->fill($data);
        $this->callHook('afterFill');
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

        // Update or create vital signs
        $vitalSignData = [
            'appointment_id' => $appointment->id,
            'heart_rate' => $data['heart_rate'] ?? null,
            'temperature' => $data['temperature'] ?? null,
            'oxygen_saturation' => $data['oxygen_saturation'] ?? null,
            'blood_pressure_systolic' => $data['blood_pressure_systolic'] ?? null,
            'blood_pressure_diastolic' => $data['blood_pressure_diastolic'] ?? null,
        ];

        if ($appointment->vitalSign) {
            $appointment->vitalSign()->update($vitalSignData);
        } else {
            $appointment->vitalSign()->create($vitalSignData);
        }

        // Sync medicines
        if (!empty($data['medicines'])) {
            $medicines = [];
            foreach ($data['medicines'] as $medicine) {
                if (!isset($medicine['medicine_id'])) continue;

                $timingType = $medicine['timing_type'];
                $timePerDay = $medicine['time_per_day'];

                // Handle custom timing if needed
                if ($timingType === 'other' && !empty($medicine['timing_custom'])) {
                    $timing = Timing::firstOrCreate(['label' => $medicine['timing_custom']]);
                    $timingType = $timing->label;
                }

                $medicines[$medicine['medicine_id']] = [
                    'timing_type' => $timingType,
                    'time_per_day' => (int) $timePerDay,
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
                return [$food['food_id'] => ['allow' => $food['allow']]];
            });
            $prescription->foods()->sync($foods);
        } else {
            $prescription->foods()->sync([]);
        }
    }
}