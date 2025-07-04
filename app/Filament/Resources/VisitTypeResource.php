<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\VisitType;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\VisitTypeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VisitTypeResource\RelationManagers;
use App\Filament\Resources\VisitTypeResource\RelationManagers\UserRelationManager;

class VisitTypeResource extends Resource
{
    protected static ?string $model = VisitType::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('keywords.reservations');
    }

    public static function getLabel(): string
    {
        return __('keywords.visit_type');
    }

    public static function getPluralLabel(): string
    {
        return __('keywords.visit_types');
    }

    public static string|array $routeMiddleware = ['can:manage_visit_types'];

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->can('manage_visit_types');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('service_type')
                    ->required()
                    ->columnSpanFull()
                    ->label(__('keywords.service_type'))
                    ->maxLength(255),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->label(__('keywords.price')),

                Select::make('doctor_id')
                    ->relationship(
                        name: 'doctor',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            $query->whereHas('roles.permissions', function ($q) {
                                $q->where('name', 'doctor_has_specialties');
                            });
                        }
                    )
                    ->preload()
                    ->searchable()
                    ->required()
                    ->label(__('keywords.doctor')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service_type')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('keywords.service_type')),
                TextColumn::make('price')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('keywords.price')),
                TextColumn::make('doctor.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->label(__('keywords.doctor')),
            ])
            ->filters([
                SelectFilter::make('doctor')
                ->relationship(
                    name: 'doctor',
                    titleAttribute: 'name',
                    modifyQueryUsing: function (Builder $query) {
                        $query->whereHas('roles.permissions', function ($q) {
                            $q->where('name', 'doctor_has_specialties');
                        });
                    }
                )
                    ->label(__('keywords.doctor')),
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
            UserRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVisitTypes::route('/'),
            'create' => Pages\CreateVisitType::route('/create'),
            'edit' => Pages\EditVisitType::route('/{record}/edit'),
        ];
    }
}
