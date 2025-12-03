<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    
    protected $casts = [
        'attachments' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'archived' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'category',
        'proficiency_level',
        'is_active',
        'description',
        'attachments',
        'tags',
        'notes',
        'archived',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNotArchived($query)
    {
        return $query->where('archived', false);
    }
}
