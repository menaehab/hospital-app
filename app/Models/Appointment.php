<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use App\Models\Scopes\AppointmentScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy(AppointmentScope::class)]
class Appointment extends Model
{
    protected $guarded = ['id'];

    public $casts = [
        'submited' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Appointment $appointment) {
            return DB::transaction(function () use ($appointment) {
                $date = now()->format('Ymd');
                $visitTypeId = $appointment->visit_type_id;

                $visitType = VisitType::find($visitTypeId);

                $latest = Appointment::where('number', 'like', $date . '-%')
                    ->whereHas('visitType', function($query) use ($visitType) {
                        $query->where('doctor_id', $visitType->doctor_id);
                    })
                    ->orderBy('number', 'desc')
                    ->lockForUpdate()
                    ->first();

                if ($latest) {
                    $lastNumber = (int) substr($latest->number, -3);
                    $newNumber = $lastNumber + 1;
                } else {
                    $newNumber = 1;
                }

                $appointment->number = $date . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
                $appointment->rescptionist_id = auth()->id();
            });
        });
    }

    public function rescptionist()
    {
        return $this->belongsTo(User::class);
    }


    public function visitType()
    {
        return $this->belongsTo(VisitType::class);
    }

    public function doctor()
    {
        return $this->hasOneThrough(
            User::class,
            VisitType::class,
            'id',
            'id',
            'visit_type_id',
            'doctor_id'
        );
    }

    public function getClinicAttribute()
    {
        return $this->visitType?->doctor?->clinic;
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function submissions()
    {
        return $this->belongsToMany(AppointmentSubmission::class, 'submitted_appointments');
    }

    public function vitalSigns()
    {
        return $this->hasOne(VitalSign::class);
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }
}