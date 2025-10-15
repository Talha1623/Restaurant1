<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SecondFlavor extends Model
{
    protected $fillable = [
        'name',
        'image',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all menus that use this flavor
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class, 'second_flavor_id');
    }
}

