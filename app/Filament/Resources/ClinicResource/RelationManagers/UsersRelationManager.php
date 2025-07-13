<?php

namespace App\Filament\Resources\ClinicResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('keywords.users');
    }

    public static function getLabel(): string
    {
        return __('keywords.user');
    }

    public static function getModelLabel(): string
    {
        return __('keywords.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('keywords.users');
    }

    public static function canViewForRecord($ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('manage_users');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                    TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label(__('keywords.name')),

                    TextInput::make('email')
                        ->required()
                        ->email()
                        ->unique(ignoreRecord: true)
                        ->label(__('keywords.email')),

                        TextInput::make('phone')
                    ->maxLength(11)
                    ->label(__('keywords.phone')),

                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->label(__('keywords.role'))
                    ->live()
                    ->afterStateUpdated(fn (callable $set) => $set('specialties', null)),

                Select::make('specialties')
                    ->label(__('keywords.specialties'))
                    ->relationship('specialties', 'name')
                    ->multiple()
                    ->preload()
                    ->live()
                    ->visible(function ($get) {
                        $roleIds = $get('roles');

                        if (empty($roleIds)) return false;

                        $roleIds = is_array($roleIds) ? $roleIds : [$roleIds];

                        $role = Role::with('permissions')
                            ->whereIn('id', $roleIds)
                            ->whereHas('permissions', function($query) {
                                $query->where('name', 'doctor_has_specialties');
                            })
                            ->first();

                        return $role !== null;
                    })
                    ->dehydrated(fn ($state) => filled($state)),

                Select::make('clinic')
                    ->label(__('keywords.clinic'))
                    ->relationship('clinic', 'name')
                    ->preload()
                    ->live()
                    ->visible(function ($get) {
                        $roleIds = $get('roles');

                        if (empty($roleIds)) return false;

                        $roleIds = is_array($roleIds) ? $roleIds : [$roleIds];

                        $role = Role::with('permissions')
                            ->whereIn('id', $roleIds)
                            ->whereHas('permissions', function($query) {
                                $query->where('name', 'doctor_has_specialties');
                            })
                            ->first();

                        return $role !== null;
                    })
                    ->dehydrated(fn ($state) => filled($state)),

                    TextInput::make('password')
                        ->password()
                        ->minLength(8)
                        ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                        ->dehydrated(fn ($state) => filled($state))
                        ->label(__('keywords.password'))
                        ->required(fn (string $operation) => $operation === 'create'),

                    TextInput::make('password_confirmation')
                        ->password()
                        ->minLength(8)
                        ->same('password')
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $operation) => $operation === 'create')
                        ->label(__('keywords.password_confirmation')),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->toggleable()->label(__('keywords.name')),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable()->toggleable()->label(__('keywords.email')),
                Tables\Columns\TextColumn::make('phone')->searchable()->sortable()->toggleable()->label(__('keywords.phone')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}