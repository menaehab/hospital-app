<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RoleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Filament\Resources\RoleResource\RelationManagers\UsersRelationManager;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function getNavigationGroup(): string
    {
        return __('keywords.system_settings');
    }

    public static function getLabel(): string
    {
        return __('keywords.role');
    }

    public static function getPluralLabel(): string
    {
        return __('keywords.roles');
    }

    public static array|string $routeMiddleware = ['can:manage_roles'];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('manage_roles');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label(__('keywords.name'))
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('permissions')
                    ->relationship('permissions', 'display_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->label(__('keywords.permissions'))
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()->sortable()->searchable()->label(__('keywords.name')),
            ])
            ->filters([
                SelectFilter::make('permissions')->relationship('permissions', 'display_name')->preload()->searchable()->multiple()->label(__('keywords.permissions')),
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
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
