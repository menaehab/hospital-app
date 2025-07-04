<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AppointmentScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (auth()->user()->can('manage_appointments')){
            return;
        } else if(auth()->user()->can('appointment_view_add_by_himself')){
            $builder->where('submited',false)->where('rescptionist_id', auth()->user()->id);
        } else if (auth()->user()->can('appointment_view')){
            $builder->whereHas('visitType.doctor', function ($query) {
                return $query->where('doctor_id', auth()->user()->id);
            });
        }

    }
}
