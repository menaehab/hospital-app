<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalTest extends Model
{
    protected $guarded = ['id'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function commonMedicalTests()
    {
        return $this->belongsToMany(User::class, 'medical_test_user', 'medical_test_id', 'user_id')
            ->withTimestamps();
    }
}