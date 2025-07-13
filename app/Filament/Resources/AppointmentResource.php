<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Clinic;
use App\Models\Patient;
use Filament\Forms\Form;
use App\Models\VisitType;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use App\Models\AppointmentSubmission;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\BeforeCreate;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Filament\Resources\AppointmentResource\Widgets\AppointmentsStatsOverview;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'fas-clock';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('keywords.reservations');
    }

    public static function getLabel(): ?string
    {
        return __('keywords.appointment');
    }
    public static function getPluralLabel(): ?string
    {
        return __('keywords.appointments');
    }
    public static string|array $routeMiddleware = ['canAny:view_appointments,add_appointments,manage_appointments'];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('view_appointments') || Auth::user()?->can('add_appointments') || Auth::user()?->can('manage_appointments');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('add_appointments') || auth()->user()?->can('manage_appointments');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('add_appointments') || auth()->user()?->can('manage_appointments');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('manage_appointments');
    }

    // protected function getListeners(): array
    // {
    //     return [
    //         'echo:appointment-updated,AppointmentUpdated' => '$refresh',
    //     ];
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('keywords.patient_info'))
                    ->schema([
                        Select::make('patient_id')
                            ->label(__('keywords.choose_patient'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->requiredWithout(['name'])
                            ->options(
                                Patient::query()
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(function ($patient) {
                                        return [
                                            $patient->id => "{$patient->code} - {$patient->name}",
                                        ];
                                    })
                            )
                            ->placeholder(__('keywords.search_by_code_or_name')),

                        TextInput::make('name')
                            ->required(fn ($get) => empty($get('patient_id')))
                            ->maxLength(255)
                            ->live()
                            ->visible(fn ($get) => empty($get('patient_id')))
                            ->label(__('keywords.name')),

                        TextInput::make('age')
                            ->numeric()
                            ->maxLength(255)
                            ->live()
                            ->visible(fn ($get) => empty($get('patient_id')))
                            ->label(__('keywords.age')),

                        TextInput::make('phone')
                            ->maxLength(11)
                            ->live()
                            ->visible(fn ($get) => empty($get('patient_id')))
                            ->label(__('keywords.phone')),

                        TextInput::make('address')
                            ->maxLength(255)
                            ->live()
                            ->visible(fn ($get) => empty($get('patient_id')))
                            ->label(__('keywords.address')),
                    ])->visibleOn('create'),

                Section::make(__('keywords.appointment_info'))
                    ->schema([
                        Select::make('status')
                            ->options(function () {
                                $options = [
                                    'pending' => __('keywords.pending'),
                                    'in_session' => __('keywords.in_session'),
                                    'missed' => __('keywords.missed'),
                                ];

                                if (auth()->user()->can('manage_appointments')) {
                                    $options['cancelled'] = __('keywords.cancelled');
                                    $options['finished'] = __('keywords.finished');
                                }

                                if (auth()->user()->can('view_appointments')) {
                                    $options['finished'] = __('keywords.finished');
                                }
                                return $options;
                            })
                            ->required()
                            ->visibleOn('edit')
                            ->columnSpanFull()
                            ->label(__('keywords.status')),

                        Select::make('clinic')
                            ->required()
                            ->visibleOn('create')
                            ->label(__('keywords.clinic'))
                            ->options(function ($get) {
                                return Clinic::pluck('name', 'id');
                            })
                            ->searchable()
                            ->live()
                            ->columnSpanFull()
                            ->preload(),

                        Select::make('doctor')
                            ->required()
                            ->visibleOn('create')
                            ->label(__('keywords.doctor'))
                            ->options(function ($get) {
                                return User::whereHas('roles.permissions', function ($query) {
                                    return $query->where('name', 'has_specialties');
                                })->where('clinic_id', $get('clinic'))->pluck('name', 'id');
                            })
                            ->searchable()
                            ->live()
                            ->visible(fn($get) => Clinic::find($get('clinic')) !== null)
                            ->columnSpanFull()
                            ->preload(),

                        Select::make('visit_type_id')
                            ->relationship('visitType', 'service_type')
                            ->required()
                            ->visibleOn('create')
                            ->label(__('keywords.visit_type'))
                            ->options(function ($get) {
                                return VisitType::where('doctor_id', $get('doctor'))
                                    ->get()
                                    ->mapWithKeys(function ($visitType) {
                                        return [
                                            $visitType->id => $visitType->service_type . ' - ' . $visitType->price . ' ' . __('keywords.currency'),
                                        ];
                                    });
                            })
                            ->searchable()
                            ->live()
                            ->visible(fn($get) => User::find($get('doctor')) !== null)
                            ->columnSpanFull()
                            ->preload(),

                        Textarea::make('notes')
                            ->columnSpanFull()
                            ->label(__('keywords.notes')),
                    ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('keywords.name')),
                TextColumn::make('number')
                    ->searchable()
                    ->sortable()
                    ->label(__('keywords.number')),
                TextColumn::make('status')
                    ->badge()
                    ->color(function ($state) {
                        return match ($state) {
                            'pending' => 'info',
                            'finished' => 'success',
                            'in_session' => 'warning',
                            'cancelled' => 'danger',
                            'missed' => 'gray',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'pending' => __('keywords.pending'),
                            'finished' => __('keywords.finished'),
                            'in_session' => __('keywords.in_session'),
                            'cancelled' => __('keywords.cancelled'),
                            'missed' => __('keywords.missed'),
                            default => ucfirst($state),
                        };
                    })
                    ->searchable()
                    ->sortable()
                    ->label(__('keywords.status')),
                TextColumn::make('doctor.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('keywords.doctor')),
                TextColumn::make('created_at')
                ->time('h:i:s A')
                ->searchable()
                ->sortable()
                ->label(__('keywords.created_at')),
                TextColumn::make('visitType.service_type')
                    ->searchable()
                    ->sortable()
                    ->label(__('keywords.visit_type')),
                TextColumn::make('visitType.price')
                    ->searchable()
                    ->sortable()
                    ->label(__('keywords.price')),
                TextColumn::make('rescptionist.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('keywords.rescptionist')),
                IconColumn::make('is_submitted')
                    ->boolean()
                    ->searchable()
                    ->sortable()
                    ->state(function ($record) {
                        return $record->submissions()->exists();
                    })
                    ->visible(function() {
                        return auth()->user()->can('view_appointments') || auth()->user()->can('manage_appointments');
                    })
                    ->label(__('keywords.submited')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => __('keywords.pending'),
                        'finished' => __('keywords.finished'),
                        'cancelled' => __('keywords.cancelled'),
                        'missed' => __('keywords.missed'),
                    ])
                    ->label(__('keywords.status')),
                SelectFilter::make('doctor')
                    ->relationship('doctor','name')
                    ->label(__('keywords.doctor')),

                SelectFilter::make('rescptionist_id')
                    ->label(__('keywords.rescptionist'))
                    ->options(function() {
                        return User::whereIn('id', Appointment::query()
                            ->pluck('rescptionist_id')
                            ->unique()
                            ->filter()
                        )->pluck('name', 'id');
                    }),

                Filter::make('is_submitted')
                    ->query(function (Builder $query) {
                        $query->whereDoesntHave('submissions');
                    })
                    ->label(__('keywords.not_submitted')),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(function() {
                        return auth()->user()->can('manage_appointments');
                    }),
                    Tables\Actions\BulkAction::make('is_submitted')
                        ->label( __('keywords.submit'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            // NOTE: validation to check if all selected appointments have the same doctor or already submitted

                            $doctorIds = $records->map(function ($record) {
                                return optional($record->visitType)->doctor_id;
                            })->filter()->unique();

                            if ($doctorIds->count() > 1) {
                                Notification::make()
                                    ->title('خطأ')
                                    ->body('يجب اختيار زيارات لنفس الدكتور فقط.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $submittedAppointments = $records->filter(function ($record) {
                                return $record->submissions()->exists();
                            });

                            if ($submittedAppointments->isNotEmpty()) {
                                Notification::make()
                                    ->title('خطأ')
                                    ->body('يجب اختيار زيارات لم يتم تسليمها.')
                                    ->danger()
                                    ->send();
                                return;
                            }


                            $submission = AppointmentSubmission::create([
                                'doctor_id' => Auth::user()->id,
                                'accountant_id' => Auth::user()->id,
                            ]);

                            $records->each(function (Appointment $record) use ($submission) {
                                $submission->appointments()->attach($record);
                            });

                            return redirect()->route('print.appointment-submission', $submission);
                        })->visible(function() {
                            return auth()->user()->can('appointment_submit') || auth()->user()->can('manage_appointments');
                        })
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
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
