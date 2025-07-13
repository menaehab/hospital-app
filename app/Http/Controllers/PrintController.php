<?php

namespace App\Http\Controllers;

use App\Models\AppointmentSubmission;

class PrintController extends Controller
{
    public function AppointmentSubmissionShow(AppointmentSubmission $submission)
    {
        if($submission->is_printed && !auth()->user()->can('view_reports')){
            abort(403, __('keywords.already_printed'));
        }

        return view('prints.appointment-submission', compact('submission'));
    }

    public function AppointmentSubmissionPrint($id)
    {
        $submission = AppointmentSubmission::findOrFail($id);

        if($submission->is_printed  && !auth()->user()->can('view_reports')){
            abort(403, __('keywords.already_printed'));
        }

        $logo = $submission->doctor->clinic->image;

        $total_amount = 0;
        foreach ($submission->appointments as $appointment) {
            $total_amount += $appointment->visitType->doctor_fee_type == 'fixed'
            ? $appointment->visitType->doctor_fee_value
            : ($appointment->visitType->doctor_fee_value * $appointment->visitType->price) / 100;
        }

        $submission->update([
            'is_printed' => true,
            'total_amount' => $total_amount
        ]);

        return view('prints.appointment-submission-content', compact('submission', 'logo'));
    }
}
