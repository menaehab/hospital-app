<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasRoles;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function specialties()
    {
        return $this->belongsToMany(Specialty::class, 'specialty_user', 'user_id', 'specialty_id');
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function commonMedicalTests()
    {
        return $this->belongsToMany(MedicalTest::class, 'medical_test_user', 'user_id', 'medical_test_id')
            ->withTimestamps();
    }

    public function commonRadiologyTests()
    {
        return $this->belongsToMany(RadiologyTest::class, 'radiology_test_user', 'user_id', 'radiology_test_id')
            ->withTimestamps();
    }

    public function commonFoods()
    {
        return $this->belongsToMany(Food::class, 'food_user', 'user_id', 'food_id')
            ->withTimestamps();
    }

    public function commonMedicines()
    {
        return $this->belongsToMany(Medicine::class, 'medicine_user', 'user_id', 'medicine_id')
            ->withTimestamps();
    }

}
