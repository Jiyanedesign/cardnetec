<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeforeAfterItem extends Model
{
    protected $table = 'before_after_items';

    protected $fillable = [
        'title',
        'image_before',
        'image_after',
        'technique',
        'material',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
