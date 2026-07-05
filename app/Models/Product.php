<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description_short',
        'description_long',
        'category_id',
        'image_main',
        'is_active',
        'is_featured',
        'is_carousel',
        'allows_simulation',
        'order',
        'cta_text',
        'cta_url',
        'whatsapp_message',
        'meta_title',
        'meta_description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_carousel' => 'boolean',
        'allows_simulation' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'product_material');
    }

    public function techniques(): BelongsToMany
    {
        return $this->belongsToMany(PersonalizationTechnique::class, 'product_technique', 'product_id', 'technique_id');
    }
}
