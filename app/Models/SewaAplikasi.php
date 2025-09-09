<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewaAplikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'application_id',
        'hosting_id',
        'domain',
        'biaya',
        'tanggal_mulai',
        'tanggal_expired',
        'status',
    ];

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke Application
        public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }


    // Relasi ke Hosting
    public function hosting()
    {
        return $this->belongsTo(Hosting::class);
    }
}