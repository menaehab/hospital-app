<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiologyTest extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function commonRadiologyTests()
    {
        return $this->belongsToMany(User::class, 'radiology_test_user', 'radiology_test_id', 'user_id')
            ->withTimestamps();
    }
}
