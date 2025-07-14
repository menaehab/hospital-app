<?php

namespace App\Imports;

use App\Models\RadiologyTest;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;

class RadiologyTestImport implements  ToModel, WithStartRow, WithValidation, SkipsOnFailure
{
    use Importable, SkipsFailures;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new RadiologyTest([
            'name' => $row[0],
            'code' => $row[1],
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => ['required', 'string', 'unique:radiology_tests,name'],
            '1' => ['nullable', 'string', 'unique:radiology_tests,code'],
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            '0' => __('keywords.name'),
            '1' => __('keywords.code'),
        ];
    }

    public function startRow(): int
    {
        return 2;
    }
}
