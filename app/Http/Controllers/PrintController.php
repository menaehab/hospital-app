<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppointmentSubmission;

class PrintController extends Controller
{
    public function AppointmentSubmission(AppointmentSubmission $submission)
    {
        $logo = $submission->doctor->clinic->image;
        return view('prints.appointment-submission', compact('submission', 'logo'));
    }
}
