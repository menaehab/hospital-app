<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SubmittedAppointment extends Pivot
{
    protected $guarded = ['id'];

    public function appointmentSubmission()
    {
        return $this->belongsTo(AppointmentSubmission::class);
    }
}
