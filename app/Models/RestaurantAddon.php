<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantAddon extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'price',
        'description',
        'image',
        'is_active'
    ];

    protected $hidden = [
        'pivot', // Hide pivot data from JSON responses
        'description', // Hide description from addon list
        'is_active' // Hide is_active from addon list
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the menus that use this addon.
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_addon', 'restaurant_addon_id', 'menu_id')
                    ->withTimestamps();
    }
}
