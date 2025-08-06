<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CrmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    private CrmService $crmService;

    public function __construct(CrmService $crmService)
    {
        $this->crmService = $crmService;
    }

    /**
     * Place an order via API.
     */
    public function place(Request $request)
    {
        $customer = Auth::user();
        $sessionId = request()->header('X-Session-ID') ?? request()->ip();

        $cartItems = Cart::getCartItems($customer?->id, $sessionId);
        
        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:credit_card,debit_card,paypal,bank_transfer,cash_on_delivery',
            'billing_address.first_name' => 'required|string|max:255',
            'billing_address.last_name' => 'required|string|max:255',
            'billing_address.address_line_1' => 'required|string|max:255',
            'billing_address.address_line_2' => 'nullable|string|max:255',
            'billing_address.city' => 'required|string|max:255',
            'billing_address.state' => 'required|string|max:255',
            'billing_address.postal_code' => 'required|string|max:20',
            'billing_address.country' => 'required|string|max:255',
            'billing_address.phone' => 'nullable|string|max:20',
            'shipping_same_as_billing' => 'boolean',
            'shipping_address.first_name' => 'required_if:shipping_same_as_billing,false|string|max:255',
            'shipping_address.last_name' => 'required_if:shipping_same_as_billing,false|string|max:255',
            'shipping_address.address_line_1' => 'required_if:shipping_same_as_billing,false|string|max:255',
            'shipping_address.address_line_2' => 'nullable|string|max:255',
            'shipping_address.city' => 'required_if:shipping_same_as_billing,false|string|max:255',
            'shipping_address.state' => 'required_if:shipping_same_as_billing,false|string|max:255',
            'shipping_address.postal_code' => 'required_if:shipping_same_as_billing,false|string|max:20',
            'shipping_address.country' => 'required_if:shipping_same_as_billing,false|string|max:255',
            'shipping_address.phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Calculate total
        $totalAmount = $cartItems->sum('total');

        // Prepare addresses
        $billingAddress = $request->input('billing_address');
        $shippingAddress = $request->boolean('shipping_same_as_billing') 
            ? $billingAddress 
            : $request->input('shipping_address');

        try {
            $order = DB::transaction(function () use ($request, $customer, $cartItems, $totalAmount, $billingAddress, $shippingAddress, $sessionId) {
                // Create order
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'customer_id' => $customer?->id,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'billing_address' => $billingAddress,
                    'shipping_address' => $shippingAddress,
                ]);

                // Create order items and update stock
                foreach ($cartItems as $cartItem) {
                    // Check stock again (to prevent race conditions)
                    if (!$cartItem->product->hasStock($cartItem->quantity)) {
                        throw new \Exception("Insufficient stock for {$cartItem->product->name}");
                    }

                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->product->price, // Current price
                    ]);

                    // Reduce stock
                    $cartItem->product->reduceStock($cartItem->quantity);
                }

                // Update customer statistics
                if ($customer) {
                    $customer->updateOrderStats($totalAmount);
                }

                // Clear cart
                if ($customer) {
                    Cart::forCustomer($customer->id)->delete();
                } else {
                    Cart::forSession($sessionId)->delete();
                }

                // Track order creation in CRM
                $this->crmService->trackOrderCreation($order);

                return $order;
            });

            // Load relationships for response
            $order->load(['customer', 'orderItems.product']);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'data' => [
                    'order' => $order,
                    'order_number' => $order->order_number,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Track an order via API.
     */
    public function track($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['customer', 'orderItems.product'])
            ->firstOrFail();

        // Check if the customer can view this order
        $customer = Auth::user();
        if ($order->customer_id && (!$customer || $customer->id !== $order->customer_id)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view this order.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }
}