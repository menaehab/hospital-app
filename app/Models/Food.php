<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Food extends Model
{
    protected $guarded = ['id'];

    /**
     * The users that have this food as common.
     */
    public function commonFoods(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'common_foods', 'food_id', 'user_id')
            ->withTimestamps();
    }
}
