<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Food;
use Filament\Tables;
use App\Models\Timing;
use App\Models\Medicine;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use App\Models\MedicalTest;
use App\Models\Prescription;
use App\Models\RadiologyTest;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PrescriptionResource\Pages;
use App\Filament\Resources\PrescriptionResource\RelationManagers;

class PrescriptionResource extends Resource
{
    protected static ?string $model = Prescription::class;


    protected static ?string $navigationIcon = 'fas-file-prescription';

    public static function getNavigationGroup(): string
    {
        return __('keywords.patient_and_supplies_management');
    }

    public static function getLabel(): string
    {
        return __('keywords.prescription');
    }

    public static function getPluralLabel(): string
    {
        return __('keywords.prescriptions');
    }
    public static function canDelete($record): bool
    {
        return Auth::user()?->can('manage_prescriptions');
    }
    public static function canEdit($record): bool
    {
        return Auth::user()?->can('manage_prescriptions');
    }
    public static function canCreate(): bool
    {
        return Auth::user()?->can('manage_prescriptions') || Auth::user()?->can('add_prescriptions');
    }
    protected static ?int $navigationSort = 1;

    public static string|array $routeMiddleware = ['canAny:manage_prescriptions,view_his_prescriptions_only,add_prescriptions'];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('manage_prescriptions') || Auth::user()?->can('view_his_prescriptions_only') || Auth::user()?->can('add_prescriptions');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('keywords.appointment_info'))
                    ->schema([
                        Select::make('appointment_id')
                            ->required()
                            ->label(__('keywords.appointment'))
                            ->options(function () {
                                return Appointment::where('status', 'in_session')
                                    ->orWhere('status', 'missed')
                                    ->orWhere('status', 'pending')
                                    ->whereDoesntHave('submissions')
                                    ->whereHas('visitType', function ($query) {
                                        return $query->where('doctor_id', Auth::user()->id);
                                    })->get()
                                    ->mapWithKeys(function ($appointment) {
                                        return [
                                            $appointment->id => $appointment->patient->name . ' - ' . $appointment->number,
                                        ];
                                    });
                            })
                            ->searchable()
                            ->live()
                            ->columnSpanFull()
                            ->preload()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $appointment = Appointment::with('patient')->find($state);

                                if ($appointment && $appointment->patient) {
                                    $set('patient_name', $appointment->patient->name);
                                    $set('patient_age', $appointment->patient->age);
                                    $set('patient_phone', $appointment->patient->phone);
                                    $set('patient_code', $appointment->patient->code);
                                    $set('patient_address', $appointment->patient->address);
                                    $set('has_diabetes', $appointment->patient->has_diabetes);
                                    $set('has_heart_disease', $appointment->patient->has_heart_disease);
                                    $set('has_high_blood_pressure', $appointment->patient->has_high_blood_pressure);
                                } else {
                                    $set('patient_name', null);
                                    $set('patient_age', null);
                                    $set('patient_phone', null);
                                    $set('patient_code', null);
                                    $set('patient_address', null);
                                    $set('has_diabetes', false);
                                    $set('has_heart_disease', false);
                                    $set('has_high_blood_pressure', false);
                                }
                            }),
                    ])
                    ->visibleOn('create'),
                    Section::make(__('keywords.patient_info'))
                    ->schema([
                        TextInput::make('patient_name')
                            ->required()
                            ->label(__('keywords.name'))
                            ->disabled()
                            ->columnSpan(3),
                        TextInput::make('patient_age')
                            ->required()
                            ->label(__('keywords.age'))
                            ->disabled()
                            ->columnSpan(3),

                        TextInput::make('patient_phone')
                            ->required()
                            ->label(__('keywords.phone'))
                            ->disabled()
                            ->columnSpan(3),

                        TextInput::make('patient_code')
                            ->required()
                            ->label(__('keywords.code'))
                            ->disabled()
                            ->columnSpan(3),

                        TextInput::make('patient_address')
                            ->required()
                            ->label(__('keywords.address'))
                            ->disabled()
                            ->columnSpanFull(),

                        Checkbox::make('has_diabetes')
                            ->label(__('keywords.has_diabetes'))
                            ->columnSpan(2),

                        Checkbox::make('has_heart_disease')
                            ->label(__('keywords.has_heart_disease'))
                            ->columnSpan(2),

                        Checkbox::make('has_high_blood_pressure')
                            ->label(__('keywords.has_high_blood_pressure'))
                            ->columnSpan(2),
                    ])
                    ->visible(fn ($get) => $get('appointment_id') != null)
                    ->columns(6),

                Section::make(__('keywords.vital_signs'))
                    ->schema([
                        TextInput::make('heart_rate')
                            ->label(__('keywords.heart_rate'))
                            ->numeric()
                            ->columnSpan(2),
                        TextInput::make('temperature')
                            ->label(__('keywords.temperature'))
                            ->numeric()
                            ->columnSpan(2),
                        TextInput::make('oxygen_saturation')
                            ->numeric()
                            ->label(__('keywords.oxygen_saturation'))
                            ->numeric()
                            ->columnSpan(2),
                        TextInput::make('blood_pressure_systolic')
                            ->label(__('keywords.blood_pressure_systolic'))
                            ->numeric()
                            ->columnSpan(3),
                        TextInput::make('blood_pressure_diastolic')
                            ->label(__('keywords.blood_pressure_diastolic'))
                            ->numeric()
                            ->columnSpan(3),
                    ])
                    ->visible(fn ($get) => $get('appointment_id') != null)
                    ->columns(6),

                    Section::make(__('keywords.medicines'))
                    ->schema([
                        Repeater::make('medicines')
                            ->label(__('keywords.medicines'))
                            ->schema([
                                Select::make('medicine_id')
                                ->label(__('keywords.medicine_name'))
                                ->options(function () {
                                    $doctorId = Auth::id();

                                    // Get frequently used medicine IDs by this doctor
                                    $frequentMedicineIds = DB::table('medicine_user')
                                        ->where('user_id', $doctorId)
                                        ->pluck('medicine_id')
                                        ->toArray();

                                    // Fetch frequently used medicines
                                    $frequentMedicines = collect();
                                    if (!empty($frequentMedicineIds)) {
                                        $frequentMedicines = Medicine::whereIn('id', $frequentMedicineIds)
                                            ->get()
                                            ->mapWithKeys(function ($medicine) {
                                                return [
                                                    $medicine->id => '⭐ ' . $medicine->name,
                                                ];
                                            });
                                    }

                                    // Fetch other medicines
                                    $otherMedicines = Medicine::when(!empty($frequentMedicineIds), function ($query) use ($frequentMedicineIds) {
                                            return $query->whereNotIn('id', $frequentMedicineIds);
                                        })
                                        ->get()
                                        ->mapWithKeys(function ($medicine) {
                                            return [
                                                $medicine->id => $medicine->name,
                                            ];
                                        });

                                    // Merge both lists, frequent on top
                                    return $frequentMedicines->union($otherMedicines);
                                })
                                ->searchable()
                                ->required(),
                                TextInput::make('time_per_day')
                                    ->label(__('keywords.time_per_day'))
                                    ->numeric()
                                    ->required(),

                                Group::make([
                                    Select::make('timing_type')
                                        ->label(__('keywords.timing'))
                                        ->options(
                                            fn () =>
                                                Timing::pluck('label', 'label')->toArray()
                                                + ['other' => __('keywords.other')]
                                        )
                                        ->required()
                                        ->reactive(),

                                    TextInput::make('timing_custom')
                                        ->label(__('keywords.custom_timing'))
                                        ->placeholder(__('keywords.custom_timing_placeholder'))
                                        ->visible(fn ($get) => $get('timing_type') === 'other')
                                        ->required(fn ($get) => $get('timing_type') === 'other'),
                                ])
                                ->columns(2),
                            ])
                            ->columns(3)
                            ->createItemButtonLabel(__('keywords.add_new_medicine'))
                    ])
                    ->visible(fn ($get) => $get('appointment_id') != null),

                    Section::make(__('keywords.medical_tests'))
                    ->schema([
                        Select::make('medical_tests')
                            ->label(__('keywords.medical_test'))
                            ->options(function () {
                                $doctorId = auth()->id();

                                $frequentTestIds = DB::table('medical_test_user')
                                    ->where('user_id', $doctorId)
                                    ->pluck('medical_test_id')
                                    ->toArray();

                                $frequentTests = MedicalTest::whereIn('id', $frequentTestIds)
                                    ->get()
                                    ->mapWithKeys(function ($test) {
                                        return [
                                            $test->id => '⭐ ' . $test->name . ($test->code ? ' - ' . $test->code : ''),
                                        ];
                                    });

                                $otherTests = MedicalTest::whereNotIn('id', $frequentTestIds)
                                    ->get()
                                    ->mapWithKeys(function ($test) {
                                        return [
                                            $test->id => $test->name . ($test->code ? ' - ' . $test->code : ''),
                                        ];
                                    });

                                return $frequentTests->union($otherTests);
                            })
                            ->searchable()
                            ->multiple()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($get) => $get('appointment_id') != null)
                    ->columns(2),

                    Section::make(__('keywords.radiology_tests'))
                    ->schema([
                        Select::make('radiology_tests')
                            ->label(__('keywords.radiology_test'))
                            ->options(function () {
                                $doctorId = auth()->id();

                                $frequentTestIds = DB::table('radiology_test_user')
                                    ->where('user_id', $doctorId)
                                    ->pluck('radiology_test_id')
                                    ->toArray();

                                $frequentTests = RadiologyTest::whereIn('id', $frequentTestIds)
                                    ->get()
                                    ->mapWithKeys(function ($test) {
                                        return [
                                            $test->id => '⭐ ' . $test->name . ($test->code ? ' - ' . $test->code : ''),
                                        ];
                                    });

                                $otherTests = RadiologyTest::whereNotIn('id', $frequentTestIds)
                                    ->get()
                                    ->mapWithKeys(function ($test) {
                                        return [
                                            $test->id => $test->name . ($test->code ? ' - ' . $test->code : ''),
                                        ];
                                    });

                                return $frequentTests->union($otherTests);
                            })
                            ->searchable()
                            ->multiple()
                            ->columnSpanFull(),

                    ])
                    ->visible(fn ($get) => $get('appointment_id') != null)
                    ->columns(2),

                    Section::make(__('keywords.foods'))
                    ->schema([
                        Repeater::make('foods')
                        ->schema([
                            Select::make('food_id')
                            ->label(__('keywords.food'))
                            ->options(function () {
                                $doctorId = auth()->id();

                                $frequentFoodIds = DB::table('food_user')
                                    ->where('user_id', $doctorId)
                                    ->pluck('food_id')
                                    ->toArray();

                                $frequentFoods = Food::whereIn('id', $frequentFoodIds)
                                    ->get()
                                    ->mapWithKeys(function ($food) {
                                        return [
                                            $food->id => '⭐ ' . $food->name . ($food->code ? ' - ' . $food->code : ''),
                                        ];
                                    });

                                $otherFoods = Food::whereNotIn('id', $frequentFoodIds)
                                    ->get()
                                    ->mapWithKeys(function ($food) {
                                        return [
                                            $food->id => $food->name . ($food->code ? ' - ' . $food->code : ''),
                                        ];
                                    });

                                return $frequentFoods->union($otherFoods);
                            })
                            ->searchable()
                            ->required()
                            ->columnSpan('1'),
                            Select::make('allow')
                            ->label(__('keywords.allow'))
                            ->options([
                                'yes' => __('keywords.yes'),
                                'no' => __('keywords.no'),
                            ])
                            ->required()
                            ->columnSpan('1'),
                        ])
                        ->columns(2)
                        ->createItemButtonLabel(__('keywords.add_new_food')),
                    ])
                    ->visible(fn ($get) => $get('appointment_id') != null),


                Section::make(__('keywords.notes'))
                ->schema([
                    Textarea::make('notes')
                        ->columnSpanFull()
                        ->label(__('keywords.notes')),
                ])
                ->visible(fn ($get) => $get('appointment_id') != null),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $query->latest();

        if (Auth::user()?->can('view_his_prescriptions_only')) {
            $query->whereHas('appointment.visitType', function ($q) {
                $q->where('doctor_id', Auth::id());
            });
            $query->whereHas('appointment', function ($q) {
                $q->whereDoesntHave('submissions');
            });
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('appointment.patient.name')
                    ->label(__('keywords.patient'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('appointment.number')
                    ->label(__('keywords.appointment_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('keywords.date'))
                    ->time('Y-m-d h:i:s A')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrescriptions::route('/'),
            'create' => Pages\CreatePrescription::route('/create'),
            'edit' => Pages\EditPrescription::route('/{record}/edit'),
            'view' => Pages\ViewPrescription::route('/{record}/show'),
        ];
    }
}
