<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitType extends Model
{
    protected $guarded = ['id'];

    public function doctor()
    {
        return $this->belongsTo(User::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function clinic()
    {
        return $this->hasOneThrough(
            Clinic::class,
            User::class,'id','id','doctor_id','clinic_id'
        );
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
