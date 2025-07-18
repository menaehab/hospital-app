<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $table = 'medicines';
    protected $guarded = ['id'];

    public function commonMedicines()
    {
        return $this->belongsToMany(User::class, 'medicine_user', 'medicine_id', 'user_id')
            ->withTimestamps();
    }

    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, 'medicine_prescription', 'medicine_id', 'prescription_id')
            ->withTimestamps();
    }
}
