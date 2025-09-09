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

    // Relasi ke SewaAplikasi
    public function sewaAplikasi()
    {
        return $this->hasMany(SewaAplikasi::class, 'customer_id');
    }

    // Relasi ke Application lewat tabel sewa_aplikasi
    public function applications()
    {
        return $this->belongsToMany(Application::class, 'sewa_aplikasi', 'customer_id', 'application_id')
                    ->withPivot(['tanggal_mulai', 'tanggal_expired', 'status'])
                    ->withTimestamps();
    }

    public function hostings()
   {
    return $this->hasMany(Hosting::class);
   }

}