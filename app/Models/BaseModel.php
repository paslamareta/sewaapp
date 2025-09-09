<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BaseModel extends Model
{
    protected static function boot()
    {
        parent::boot();

        // Saat create
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by_id   = Auth::id();
                $model->created_by_name = Auth::user()->name;
                $model->updated_by_id   = Auth::id();
                $model->updated_by_name = Auth::user()->name;
            }
        });

        // Saat update
        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by_id   = Auth::id();
                $model->updated_by_name = Auth::user()->name;
            }
        });
    }
}
