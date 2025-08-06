<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'session_id',
        'product_id',
        'quantity',
    ];

    /**
     * Get the customer that owns the cart item.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the product that belongs to the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate the total price for this cart item.
     */
    public function getTotalAttribute(): float
    {
        return $this->quantity * $this->product->price;
    }

    /**
     * Get the total price formatted.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '$' . number_format($this->total, 2);
    }

    /**
     * Scope for cart items by customer.
     */
    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    /**
     * Scope for cart items by session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Get cart items for the given customer or session.
     */
    public static function getCartItems($customerId = null, $sessionId = null)
    {
        $query = self::with('product');

        if ($customerId) {
            return $query->forCustomer($customerId)->get();
        }

        if ($sessionId) {
            return $query->forSession($sessionId)->get();
        }

        return collect();
    }

    /**
     * Calculate cart total for the given customer or session.
     */
    public static function getCartTotal($customerId = null, $sessionId = null): float
    {
        $cartItems = self::getCartItems($customerId, $sessionId);
        
        return $cartItems->sum('total');
    }

    /**
     * Get cart count for the given customer or session.
     */
    public static function getCartCount($customerId = null, $sessionId = null): int
    {
        $query = self::query();

        if ($customerId) {
            return $query->forCustomer($customerId)->sum('quantity');
        }

        if ($sessionId) {
            return $query->forSession($sessionId)->sum('quantity');
        }

        return 0;
    }
}