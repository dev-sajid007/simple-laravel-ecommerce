<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'accepts_marketing',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_order_at' => 'datetime',
        'password' => 'hashed',
        'accepts_marketing' => 'boolean',
        'total_spent' => 'decimal:2',
    ];

    /**
     * Get the orders for the customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the cart items for the customer.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the customer's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Update customer statistics after an order.
     */
    public function updateOrderStats(float $orderAmount): void
    {
        $this->increment('order_count');
        $this->increment('total_spent', $orderAmount);
        $this->update(['last_order_at' => now()]);
    }

    /**
     * Get recent orders.
     */
    public function recentOrders(int $limit = 5)
    {
        return $this->orders()
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Check if customer has any orders.
     */
    public function hasOrders(): bool
    {
        return $this->order_count > 0;
    }
}