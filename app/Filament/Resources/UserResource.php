<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getLabel(): string
    {
        return __('keywords.user');
    }

    public static function getPluralLabel(): string
    {
        return __('keywords.users');
    }

    public static array|string $routeMiddleware = ['can:manage_users_and_roles'];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('manage_users_and_roles');
    }

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->toggleable()->label(__('keywords.name')),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable()->toggleable()->label(__('keywords.email')),
                Tables\Columns\TextColumn::make('roles.name')->searchable()->sortable()->toggleable()->label(__('keywords.role')),
            ])
            ->filters([
                SelectFilter::make('role')->relationship('roles', 'name')->label(__('keywords.role')),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}