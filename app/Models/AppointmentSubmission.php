<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\AppointmentScope;

class AppointmentSubmission extends Model
{
    protected $guarded = ['id'];

    public function doctor()
    {
        return $this->belongsTo(User::class,'doctor_id','id');
    }

    public function accountant()
    {
        return $this->belongsTo(User::class,'accountant_id','id');
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'submitted_appointments')
            ->using(SubmittedAppointment::class)
            ->withoutGlobalScope(AppointmentScope::class)
            ->withTimestamps();
    }

    public function submittedAppointments()
    {
        return $this->hasMany(SubmittedAppointment::class);
    }
}