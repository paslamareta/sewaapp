<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'created_by_id',
        'created_by_name',
        'updated_by_id',
        'updated_by_name'

    ];
}
