<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PatientResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PatientResource\RelationManagers;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'fas-bed-pulse';

    public static function getNavigationGroup(): string
    {
        return __('keywords.patient_and_supplies_management');
    }

    public static function getLabel(): string
    {
        return __('keywords.patient');
    }

    public static function getPluralLabel(): string
    {
        return __('keywords.patients');
    }
    public static string|array $routeMiddleware = ['canAny:manage_patients,view_patients'];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('manage_patients') || Auth::user()?->can('view_patients');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label(__('keywords.name')),
                TextInput::make('age')
                    ->numeric()
                    ->maxLength(255)
                    ->label(__('keywords.age')),
                TextInput::make('phone')
                    ->maxLength(11)
                    ->label(__('keywords.phone')),
                TextInput::make('address')
                    ->maxLength(255)
                    ->label(__('keywords.address')),
                Checkbox::make('has_diabetes')
                    ->label(__('keywords.has_diabetes')),
                Checkbox::make('has_heart_disease')
                    ->label(__('keywords.has_heart_disease')),
                Checkbox::make('has_high_blood_pressure')
                    ->label(__('keywords.has_high_blood_pressure')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('keywords.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('age')
                    ->label(__('keywords.age'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('keywords.phone'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code')
                    ->label(__('keywords.code'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('has_diabetes')->toggle(),
                Filter::make('has_heart_disease')->toggle(),
                Filter::make('has_high_blood_pressure')->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
