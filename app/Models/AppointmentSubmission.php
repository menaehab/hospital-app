<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentSubmission extends Model
{
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->belongsToMany(Appointment::class, 'submitted_appointments')
            ->using(SubmittedAppointment::class)
            ->withTimestamps();
    }

    public function submittedAppointments()
    {
        return $this->hasMany(SubmittedAppointment::class);
    }
}
