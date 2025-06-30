<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    protected $guarded = ['id'];

    public function doctors()
    {
        return $this->belongsToMany(User::class, 'specialty_user', 'specialty_id', 'user_id');
    }

}
