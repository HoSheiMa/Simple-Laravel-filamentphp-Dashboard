<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'proficiency_level',
        'is_active',
        'description',
        'attachments',
        'tags',
        'notes',
        'archived',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'archived' => 'boolean',
        'attachments' => 'array',
        'tags' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotArchived($query)
    {
        return $query->where('archived', false);
    }
}
