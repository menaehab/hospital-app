<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Food;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\FoodResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FoodResource\RelationManagers;

class FoodResource extends Resource
{
    protected static ?string $model = Food::class;

    protected static ?string $navigationIcon = 'fas-utensils';

    public static function getNavigationGroup(): string
    {
        return __('keywords.patient_and_supplies_management');
    }

    public static function getLabel(): string
    {
        return __('keywords.food');
    }

    public static function getPluralLabel(): string
    {
        return __('keywords.foods');
    }

    protected static ?int $navigationSort = 2;

    protected static string|array $routeMiddleware = ['canAny:manage_food'];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('manage_food');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique()
                    ->label(__('keywords.name')),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('keywords.name'))
                    ->searchable()
                    ->sortable(),

                IconColumn::make('common')
                    ->label(__('keywords.common'))
                    ->boolean()
                    ->sortable()
                    ->getStateUsing(fn ($record): bool => $record->commonFoods()->where('user_id', auth()->user()->id)->exists()),
            ])
            ->filters([
                Filter::make('common')
                    ->toggle()
                    ->label(__('keywords.common'))
                    ->query(fn (Builder $query): Builder => $query->whereHas('commonFoods',
                    fn (Builder $query): Builder => $query->where('user_id', auth()->user()->id))),
            ])
            ->actions([
                Tables\Actions\Action::make('common')
                    ->label(__('keywords.common'))
                    ->color('info')
                    ->icon('fas-star')
                    ->action(function (Food $record) {
                        $record->commonFoods()->toggle(auth()->user()->id);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\Action::make('common')
                        ->label(__('keywords.common'))
                        ->color('info')
                        ->icon('fas-star')
                        ->action(function (Food $record) {
                            $record->commonFoods()->toggle(auth()->user()->id);
                        }),
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
            'index' => Pages\ListFood::route('/'),
            'create' => Pages\CreateFood::route('/create'),
            'edit' => Pages\EditFood::route('/{record}/edit'),
        ];
    }
}
