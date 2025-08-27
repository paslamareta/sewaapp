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
}
