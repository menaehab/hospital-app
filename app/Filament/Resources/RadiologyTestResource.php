<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RadiologyTest;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RadiologyTestResource\Pages;
use App\Filament\Resources\RadiologyTestResource\RelationManagers;

class RadiologyTestResource extends Resource
{
    protected static ?string $model = RadiologyTest::class;

    protected static ?string $navigationIcon = 'fas-x-ray';

    public static function getNavigationGroup(): string
    {
        return __('keywords.patient_and_supplies_management');
    }

    public static function getLabel(): string
    {
        return __('keywords.radiology_test');
    }

    public static function getPluralLabel(): string
    {
        return __('keywords.radiology_tests');
    }

    protected static ?int $navigationSort = 4;

    protected static string|array $routeMiddleware = ['canAny:manage_radiology_tests'];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('manage_radiology_tests');
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
                TextInput::make('code')
                    ->maxLength(255)
                    ->unique()
                    ->label(__('keywords.code')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('keywords.name'))
                    ->sortable(),
                TextColumn::make('code')
                    ->searchable()
                    ->label(__('keywords.code'))
                    ->sortable(),
                IconColumn::make('common')
                    ->searchable()
                    ->label(__('keywords.common'))
                    ->boolean()
                    ->sortable()
                    ->getStateUsing(fn ($record): bool => $record->commonRadiologyTests()->where('user_id', auth()->user()->id)->exists()),
            ])
            ->filters([
                Filter::make('common')
                    ->toggle()
                    ->label(__('keywords.common'))
                    ->query(fn (Builder $query): Builder => $query->whereHas('commonRadiologyTests',
                    fn (Builder $query): Builder => $query->where('user_id', auth()->user()->id))),
            ])
            ->actions([
                Tables\Actions\Action::make('common')
                    ->label(__('keywords.common'))
                    ->color('info')
                    ->icon('fas-star')
                    ->action(function (RadiologyTest $record) {
                        $record->commonRadiologyTests()->toggle(auth()->user()->id);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('common')
                        ->label(__('keywords.common'))
                        ->color('info')
                        ->icon('fas-star')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->commonRadiologyTests()->toggle(auth()->user()->id);
                            });
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
            'index' => Pages\ListRadiologyTests::route('/'),
            'create' => Pages\CreateRadiologyTest::route('/create'),
            'edit' => Pages\EditRadiologyTest::route('/{record}/edit'),
        ];
    }
}