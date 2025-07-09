<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $guarded = ['id'];

    public $casts = [
        'has_diabetes' => 'boolean',
        'has_heart_disease' => 'boolean',
        'has_hypothyroidism' => 'boolean',
    ];

    public function booted()
    {
        static::creating(function (Patient $patient) {
            $date = now()->format('Ymd');
            do {
                $randomNumber = random_int(100000, 999999);
                $code = $date . '-' . $randomNumber; // example: 20250709-123456
            } while (Patient::where('code', $code)->exists());
            $patient->code = $code;
        });
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getRouteKeyName()
    {
        return 'code';
    }
}