<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get cart items.
     */
    public function index()
    {
        $customer = Auth::user();
        $sessionId = request()->header('X-Session-ID') ?? request()->ip();

        $cartItems = Cart::getCartItems($customer?->id, $sessionId);
        $cartTotal = Cart::getCartTotal($customer?->id, $sessionId);
        $cartCount = Cart::getCartCount($customer?->id, $sessionId);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'total' => $cartTotal,
                'count' => $cartCount,
            ],
        ]);
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $product = Product::active()->findOrFail($request->product_id);

        // Check stock availability
        if (!$product->hasStock($request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available. Only ' . $product->stock_quantity . ' items left.',
            ], 422);
        }

        $customer = Auth::user();
        $sessionId = request()->header('X-Session-ID') ?? request()->ip();

        // Check if item already exists in cart
        $existingCartItem = Cart::where([
            'product_id' => $product->id,
            $customer ? 'customer_id' : 'session_id' => $customer?->id ?? $sessionId,
        ])->first();

        if ($existingCartItem) {
            $newQuantity = $existingCartItem->quantity + $request->quantity;
            
            // Check total quantity against stock
            if (!$product->hasStock($newQuantity)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more items. Maximum available: ' . $product->stock_quantity,
                ], 422);
            }

            $existingCartItem->update(['quantity' => $newQuantity]);
        } else {
            Cart::create([
                'customer_id' => $customer?->id,
                'session_id' => $customer ? null : $sessionId,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        $cartCount = Cart::getCartCount($customer?->id, $sessionId);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'data' => [
                'cart_count' => $cartCount,
            ],
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer = Auth::user();
        $sessionId = request()->header('X-Session-ID') ?? request()->ip();

        $cartItem = Cart::where('id', $id)
            ->where(function ($query) use ($customer, $sessionId) {
                if ($customer) {
                    $query->where('customer_id', $customer->id);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->with('product')
            ->firstOrFail();

        // Check stock availability
        if (!$cartItem->product->hasStock($request->quantity)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available. Only ' . $cartItem->product->stock_quantity . ' items left.',
            ], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $cartTotal = Cart::getCartTotal($customer?->id, $sessionId);
        $cartCount = Cart::getCartCount($customer?->id, $sessionId);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'data' => [
                'item_total' => $cartItem->fresh()->total,
                'cart_total' => $cartTotal,
                'cart_count' => $cartCount,
            ],
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function remove($id)
    {
        $customer = Auth::user();
        $sessionId = request()->header('X-Session-ID') ?? request()->ip();

        $cartItem = Cart::where('id', $id)
            ->where(function ($query) use ($customer, $sessionId) {
                if ($customer) {
                    $query->where('customer_id', $customer->id);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->firstOrFail();

        $cartItem->delete();

        $cartTotal = Cart::getCartTotal($customer?->id, $sessionId);
        $cartCount = Cart::getCartCount($customer?->id, $sessionId);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart!',
            'data' => [
                'cart_total' => $cartTotal,
                'cart_count' => $cartCount,
            ],
        ]);
    }
}