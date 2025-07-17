<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MedicinePrescription extends Pivot
{
    protected $guarded = ['id'];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}