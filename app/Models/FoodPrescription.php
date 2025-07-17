<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class FoodPrescription extends Pivot
{
    protected $guarded = ['id'];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }
}