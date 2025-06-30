<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clienic extends Model
{
    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}