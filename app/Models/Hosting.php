<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hosting extends Model
{
    protected $fillable = ['nama_hosting', 'url', 'active'];
protected $casts = [ 'active' => 'boolean' ];
}
