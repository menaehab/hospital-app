<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Clinic;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ClienicResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClinicResource\RelationManagers;
use App\Filament\Resources\ClienicResource\Pages\EditClinic;
use App\Filament\Resources\ClinicResource\Pages\ListClinics;
use App\Filament\Resources\ClienicResource\Pages\CreateClinic;
use App\Filament\Resources\ClienicResource\RelationManagers\UsersRelationManager;

class ClinicResource extends Resource
{
    protected static ?string $model = Clinic::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function getLabel(): string
    {
        return __('keywords.clinic');
    }

    public static function getPluralLabel(): string
    {
        return __('keywords.clinics');
    }

    public static array|string $routeMiddleware = ['can:manage_clienics'];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('manage_clienics');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label(__('keywords.name'))
                    ->required()
                    ->maxLength(255),
                FileUpload::make('image')
                    ->label(__('keywords.image'))
                    ->image()
                    ->directory('clienics')
                    ->imageEditor(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('keywords.name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                ImageColumn::make('image')
                    ->label(__('keywords.image'))
                    ->toggleable()
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
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClinics::route('/'),
            'create' => CreateClinic::route('/create'),
            'edit' => EditClinic::route('/{record}/edit'),
        ];
    }
}
