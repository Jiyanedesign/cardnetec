<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'name',
        'company',
        'whatsapp',
        'email',
        'qty',
        'message',
        'product_name',
        'logo_path',
        'simulation_image_path',
        'simulation_data',
        'status',
        'admin_notes'
    ];

    protected $casts = [
        'simulation_data' => 'array',
    ];
}
