<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\AppointmentSubmission;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AppointmentSubmissionResource\Pages;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;

class AppointmentSubmissionResource extends Resource
{
    protected static ?string $model = AppointmentSubmission::class;

    protected static ?string $navigationIcon = 'fas-file-invoice-dollar';

    protected static ?int $navigationSort = 1;

    public static string|array $routeMiddleware = ['can:view_reports'];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_reports') ?? false;
    }

    public static function getNavigationGroup(): string
    {
        return __('keywords.Reports_and_finances');
    }

    public static function getLabel(): ?string
    {
        return __('keywords.appointment_submission');
    }

    public static function getPluralLabel(): ?string
    {
        return __('keywords.appointment_submissions');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canUpdate($record): bool
    {
        return false;
    }

    public static function canView($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doctor.name')
                    ->label(__('keywords.doctor'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('accountant.name')
                    ->label(__('keywords.accountant'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label(__('keywords.total'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('keywords.submitted_at'))
                    ->dateTime('Y-m-d h:i A')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->label(__('keywords.doctor')),

                SelectFilter::make('accountant_id')
                    ->relationship('accountant', 'name')
                    ->label(__('keywords.accountant')),

                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label(__('keywords.from')),
                        \Filament\Forms\Components\DatePicker::make('until')->label(__('keywords.until')),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('print')
                    ->label(__('keywords.print'))
                    ->url(fn (AppointmentSubmission $record) => route('print.appointment-submission', $record))
                    ->icon('fas-print')
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointmentSubmissions::route('/'),
            // 'view' => Pages\ViewAppointmentSubmission::route('/{record}'),
        ];
    }
}
