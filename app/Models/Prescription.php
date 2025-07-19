<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $guarded = ['id'];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'medicine_prescription')
            ->withPivot('timing_type', 'time_per_day')
            ->using(MedicinePrescription::class)
            ->withTimestamps();
    }

    public function foods()
    {
        return $this->belongsToMany(Food::class, 'food_prescription')
            ->withPivot('allow')
            ->using(FoodPrescription::class);
    }

    public function medicalTests()
    {
        return $this->belongsToMany(MedicalTest::class, 'medical_test_prescription', 'prescription_id', 'medical_test_id')
            ->withTimestamps();
    }

    public function radiologyTests()
    {
        return $this->belongsToMany(RadiologyTest::class, 'radiology_test_prescription', 'prescription_id', 'radiology_test_id')
            ->withTimestamps();
    }
}
