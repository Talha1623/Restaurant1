<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'rating',
        'description',
        'status'
    ];

    /**
     * Get the customer that owns the review.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the restaurant that owns the review.
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Scope a query to only include active reviews.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
