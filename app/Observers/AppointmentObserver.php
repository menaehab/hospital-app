<?php

namespace App\Observers;

use App\Events\AppointmentUpdated;
use App\Models\Appointment;

class AppointmentObserver
{
    /**
     * Handle the Appointment "created" event.
     */
    public function created(Appointment $appointment): void
    {
        event(new AppointmentUpdated());
    }

    /**
     * Handle the Appointment "updated" event.
     */
    public function updated(Appointment $appointment): void
    {
        event(new AppointmentUpdated());
    }

    /**
     * Handle the Appointment "deleted" event.
     */
    public function deleted(Appointment $appointment): void
    {
        event(new AppointmentUpdated());
    }

    /**
     * Handle the Appointment "restored" event.
     */
    public function restored(Appointment $appointment): void
    {
        event(new AppointmentUpdated());
    }

    /**
     * Handle the Appointment "force deleted" event.
     */
    public function forceDeleted(Appointment $appointment): void
    {
        event(new AppointmentUpdated());
    }
}