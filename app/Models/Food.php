<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    protected $table = 'food';
    protected $guarded = ['id'];

    public function commonFoods()
    {
        return $this->belongsToMany(User::class, 'food_user', 'food_id', 'user_id')
            ->withTimestamps();
    }

    public function prescriptions()
    {
        return $this->belongsToMany(Prescription::class, 'food_prescription')
            ->withPivot('allow')
            ->using(FoodPrescription::class);
    }
}