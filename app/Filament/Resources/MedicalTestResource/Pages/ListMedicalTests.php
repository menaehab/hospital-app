<?php

namespace App\Filament\Resources\MedicalTestResource\Pages;

use Filament\Actions;
use App\Imports\MedicalTestImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\MedicalTestResource;

class ListMedicalTests extends ListRecords
{
    protected static string $resource = MedicalTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('file')
            ->color("success")
            ->icon('fas-file-import')
            ->modalHeading(__('keywords.import_medical_test'))
            ->modalDescription(__('keywords.import_medical_test_description'))
            ->label(__('keywords.import_medical_test'))
            ->modalWidth('xl')
            ->form([
                FileUpload::make('file')
                    ->required()
                    ->label(__('keywords.file'))
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                        'application/vnd.ms-excel', // .xls
                        'text/csv', // .csv
                    ])
            ])
            ->action(function (array $data) {
                $filePath = $data['file'];
                $file = Storage::disk('public')->path($filePath);
                $import = new MedicalTestImport();
                Excel::import($import, $file);

                if ($import->failures()->isNotEmpty()) {
                    $failures = $import->failures();
                    $errorMessages = [];
                    foreach ($failures as $failure) {
                        $errorMessages[] = __('keywords.row') . ' ' . $failure->row() . ': ' . implode(', ', $failure->errors());
                    }

                    Notification::make()
                        ->title(__('keywords.import_validation_error'))
                        ->body(implode("\n", $errorMessages))
                        ->danger()
                        ->persistent()
                        ->send();
                    return;
                }

                Notification::make()
                    ->title(__('keywords.success'))
                    ->body(__('keywords.medical_test_imported_successfully'))
                    ->success()
                    ->send();
            })
    ];
    }
}