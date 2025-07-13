<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\Appointment;
use App\Models\AppointmentSubmission;

class SubmittedAppointment extends Pivot
{
    protected $table = 'submitted_appointments';
    protected $guarded = ['id'];

    public function appointmentSubmission()
    {
        return $this->belongsTo(AppointmentSubmission::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
