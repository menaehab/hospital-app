<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use App\Models\VisitType;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

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

    public static string|array $routeMiddleware = ['canAny:appointment_view,appointment_view_add_by_himself,manage_appointments'];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('appointment_view') || Auth::user()?->can('appointment_view_add_by_himself') || Auth::user()?->can('manage_appointments');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('appointment_view_add_by_himself') || auth()->user()?->can('manage_appointments');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('appointment_view_add_by_himself') || auth()->user()?->can('manage_appointments');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('manage_appointments');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label(__('keywords.name'))
                    ->visibleOn('create')
                    ->columnSpanFull()
                    ->maxLength(255),
                Select::make('status')
                    ->options([
                        'pending' => __('keywords.pending'),
                        'finished' => __('keywords.finished'),
                        'cancelled' => __('keywords.cancelled'),
                        'missed' => __('keywords.missed'),
                    ])
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
                        return User::whereHas('roles.permissions',function($query) {
                            return $query->where('name', 'doctor_has_specialties');
                        })->where('clinic_id',$get('clinic'))->pluck('name', 'id');
                    })
                    ->searchable()
                    ->live()
                    ->visible(fn($get) => Clinic::find($get('clinic')) !== null)
                    ->columnSpanFull()
                    ->preload(),

                Select::make('visit_type_id')
                ->relationship('visitType','service_type')
                ->required()
                ->visibleOn('create')
                ->label(__('keywords.visit_type'))
                ->options(function ($get) {
                    return VisitType::where('doctor_id',$get('doctor'))->pluck('service_type', 'id');
                })
                ->searchable()
                ->live()
                ->visible(fn($get) => User::find($get('doctor')) !== null)
                ->columnSpanFull()
                ->preload(),

                Textarea::make('notes')
                    ->columnSpanFull()
                    ->label(__('keywords.notes')),
                Checkbox::make('submited')
                    ->label(__('keywords.submited')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
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
                            'cancelled' => 'danger',
                            'missed' => 'warning',
                        };
                    })
                    ->formatStateUsing(function ($state) {
                        return match ($state) {
                            'pending' => __('keywords.pending'),
                            'finished' => __('keywords.finished'),
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
                TextColumn::make('visitType.service_type')
                    ->searchable()
                    ->sortable()
                    ->label(__('keywords.visit_type')),
                TextColumn::make('rescptionist.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('keywords.rescptionist')),
                IconColumn::make('submited')
                    ->searchable()
                    ->sortable()
                    ->visible(function() {
                        return auth()->user()->can('appointment_view') || auth()->user()->can('manage_appointments');
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

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('submited')
                        ->label( __('keywords.submit'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $records->each(function (Appointment $record) {
                                $record->submited = true;
                                $record->save();
                            });
                        }),
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
