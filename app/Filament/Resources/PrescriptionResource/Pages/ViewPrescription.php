<?php

namespace App\Filament\Resources\PrescriptionResource\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\PrescriptionResource;

class ViewPrescription extends ViewRecord
{
    protected static string $resource = PrescriptionResource::class;

    public function form(Form $form): Form
    {
        $record = $this->getRecord();

        $medicineInputs = [];

        $medicalTestInputs = [];

        $radiologyTestInputs = [];

        $foodInputs = [];

        if ($record && $record->medicines) {
            foreach ($record->medicines as $medicine) {
                $medicineInputs[] = TextInput::make('medicine_' . $medicine->id)
                    ->label(__('keywords.medicine_name'))
                    ->formatStateUsing(fn () => $medicine->name)
                    ->disabled();

                $medicineInputs[] = TextInput::make('time_per_day_' . $medicine->id)
                    ->label(__('keywords.time_per_day'))
                    ->formatStateUsing(fn () => $medicine->pivot->time_per_day)
                    ->disabled();

                $medicineInputs[] = TextInput::make('timing' . $medicine->id)
                    ->label(__('keywords.timing'))
                    ->formatStateUsing(fn () => $medicine->pivot->timing_type)
                    ->disabled();
            }
        }

        if ($record && $record->medicalTests) {
            foreach ($record->medicalTests as $medicalTest) {
                $medicalTestInputs[] = TextInput::make('medical_test_' . $medicalTest->id)
                    ->label('')
                    ->formatStateUsing(fn () => $medicalTest->name . ' - ' . $medicalTest->code)
                    ->disabled();
            }
        }

        if ($record && $record->radiologyTests) {
            foreach ($record->radiologyTests as $radiologyTest) {
                $radiologyTestInputs[] = TextInput::make('radiology_test_' . $radiologyTest->id)
                    ->label('')
                    ->formatStateUsing(fn () => $radiologyTest->name . ' - ' . $radiologyTest->code)
                    ->disabled();
            }
        }

        if ($record && $record->foods) {
            foreach ($record->foods as $food) {
                $foodInputs[] = TextInput::make('food_' . $food->id)
                    ->label('')
                    ->formatStateUsing(fn () => $food->name . ' - ' . $food->code)
                    ->disabled();
            }
        }

        return $form
            ->schema([
                Section::make(__('keywords.appointment_info'))
                ->schema([
                    TextInput::make('appointment_number')
                        ->label(__('keywords.appointment'))
                        ->formatStateUsing(fn () => $record->appointment->patient->name . ' - ' . $record->appointment->number)
                        ->disabled()
                        ->columnSpanFull(),
                ])
                ->columns(1),

                Section::make(__('keywords.patient_info'))
                ->schema([
                    TextInput::make('patient_name')
                        ->formatStateUsing(fn () => $record->appointment->patient->name)
                        ->label(__('keywords.name'))
                        ->disabled()
                        ->columnSpan(3),
                    TextInput::make('patient_age')
                        ->formatStateUsing(fn () => $record->appointment->patient->age)
                        ->label(__('keywords.age'))
                        ->disabled()
                        ->columnSpan(3),

                    TextInput::make('patient_phone')
                        ->formatStateUsing(fn () => $record->appointment->patient->phone)
                        ->label(__('keywords.phone'))
                        ->disabled()
                        ->columnSpan(3),

                    TextInput::make('patient_code')
                        ->formatStateUsing(fn () => $record->appointment->patient->code)
                        ->label(__('keywords.code'))
                        ->disabled()
                        ->columnSpan(3),

                    TextInput::make('patient_address')
                        ->formatStateUsing(fn () => $record->appointment->patient->address)
                        ->label(__('keywords.address'))
                        ->disabled()
                        ->columnSpanFull(),

                    Checkbox::make('has_diabetes')
                        ->formatStateUsing(fn () => $record->appointment->patient->has_diabetes)
                        ->label(__('keywords.has_diabetes'))
                        ->columnSpan(2),

                    Checkbox::make('has_heart_disease')
                        ->formatStateUsing(fn () => $record->appointment->patient->has_heart_disease)
                        ->label(__('keywords.has_heart_disease'))
                        ->columnSpan(2),

                    Checkbox::make('has_high_blood_pressure')
                        ->formatStateUsing(fn () => $record->appointment->patient->has_high_blood_pressure)
                        ->label(__('keywords.has_high_blood_pressure'))
                        ->columnSpan(2),
                ])
                ->columns(6),

                Section::make(__('keywords.vital_signs'))
                        ->schema([
                            TextInput::make('heart_rate')
                                ->label(__('keywords.heart_rate'))
                                ->formatStateUsing(fn () => $record->appointment->vitalSign->heart_rate)
                                ->numeric()
                                ->columnSpan(2),
                            TextInput::make('temperature')
                                ->label(__('keywords.temperature'))
                                ->formatStateUsing(fn () => $record->appointment->vitalSign->temperature)
                                ->numeric()
                                ->columnSpan(2),
                            TextInput::make('oxygen_saturation')
                                ->numeric()
                                ->label(__('keywords.oxygen_saturation'))
                                ->formatStateUsing(fn () => $record->appointment->vitalSign->oxygen_saturation)
                                ->numeric()
                                ->columnSpan(2),
                            TextInput::make('blood_pressure_systolic')
                                ->label(__('keywords.blood_pressure_systolic'))
                                ->formatStateUsing(fn () => $record->appointment->vitalSign->blood_pressure_systolic)
                                ->numeric()
                                ->columnSpan(3),
                            TextInput::make('blood_pressure_diastolic')
                                ->label(__('keywords.blood_pressure_diastolic'))
                                ->formatStateUsing(fn () => $record->appointment->vitalSign->blood_pressure_diastolic)
                                ->numeric()
                                ->columnSpan(3),
                        ])
                        ->columns(6),

                    Section::make(__('keywords.medicines'))
                        ->schema([
                            Group::make()
                                ->label(__('keywords.medicines'))
                                ->schema($medicineInputs)
                                ->columns(3)
                        ]),

                    Section::make(__('keywords.medical_tests'))
                        ->schema([
                            Group::make()
                                ->label(__('keywords.medical_tests'))
                                ->schema([
                                    Group::make()
                                    ->label(__('keywords.medical_tests'))
                                    ->schema($medicalTestInputs)
                                ]),
                        ]),

                    Section::make(__('keywords.radiology_tests'))
                        ->schema([
                            Group::make()
                                ->schema($radiologyTestInputs)
                        ]),

                    Section::make(__('keywords.foods'))
                        ->schema([
                            Group::make()
                                ->label(__('keywords.foods'))
                                ->schema($foodInputs)
                        ]),

                    Section::make(__('keywords.notes'))
                        ->schema([
                            Textarea::make('notes')
                                ->label('')
                                ->formatStateUsing(fn () => $record->notes)
                                ->disabled()
                                ->columnSpanFull(),
                        ])
            ])
            ->columns(1);
    }
}