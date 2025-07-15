<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Medicine;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\MedicineResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MedicineResource\RelationManagers;

class MedicineResource extends Resource
{
    protected static ?string $model = Medicine::class;

    protected static ?string $navigationIcon = 'fas-pills';

    public static function getNavigationGroup(): string
    {
        return __('keywords.patient_and_supplies_management');
    }

    public static function getLabel(): string
    {
        return __('keywords.medicine');
    }

    public static function getPluralLabel(): string
    {
        return __('keywords.medicines');
    }

    protected static ?int $navigationSort = 3;

    protected static string|array $routeMiddleware = ['canAny:manage_medicines'];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('manage_medicines');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('keywords.name'))
                    ->required()
                    ->unique(ignoreRecord: true)

                    ->maxLength(255),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('keywords.name')),
                IconColumn::make('common')
                    ->label(__('keywords.common'))
                    ->boolean()
                    ->sortable()
                    ->getStateUsing(fn ($record): bool => $record->commonMedicines()->where('user_id', auth()->user()->id)->exists()),
            ])
            ->filters([
                Filter::make('common')
                    ->toggle()
                    ->label(__('keywords.common'))
                    ->query(fn (Builder $query): Builder => $query->whereHas('commonMedicines',
                    fn (Builder $query): Builder => $query->where('user_id', auth()->user()->id))),
            ])
            ->actions([
                Tables\Actions\Action::make('common')
                    ->label(__('keywords.common'))
                    ->color('info')
                    ->icon('fas-star')
                    ->action(function (Medicine $record) {
                        $record->commonMedicines()->toggle(auth()->user()->id);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('common')
                        ->label(__('keywords.common'))
                        ->color('info')
                        ->icon('fas-star')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->commonMedicines()->toggle(auth()->user()->id);
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
            'index' => Pages\ListMedicines::route('/'),
            'create' => Pages\CreateMedicine::route('/create'),
            'edit' => Pages\EditMedicine::route('/{record}/edit'),
        ];
    }
}