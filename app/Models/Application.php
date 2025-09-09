<?php

namespace App\Models;


class Application extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'status',
        'type',
        'link',
        'version',
        'host_id'

    ];

    // Relasi ke SewaAplikasi
    public function sewa()
    {
        return $this->hasMany(SewaAplikasi::class, 'application_id');
    }

}