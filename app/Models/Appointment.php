<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
            $date = now()->format('Ymd');
            $countToday = Appointment::whereDate('created_at', now()->toDateString())->count();
            $appointment->number = $date . '-' . ($countToday + 1);
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
    public function clinic()
    {
        return $this->hasOneThrough(
            Clinic::class,
            User::class,'id','id','doctor_id','clinic_id'
        );
    }

    public function doctor()
    {
        return $this->hasOneThrough(
            User::class,
            VisitType::class,'id','id','doctor_id','clinic_id'
        );
    }
}