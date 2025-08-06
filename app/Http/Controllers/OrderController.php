<?php

namespace App\Http\Controllers;

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
     * Show checkout page.
     */
    public function checkout()
    {
        $customer = Auth::guard('customer')->user();
        $sessionId = session()->getId();

        $cartItems = Cart::getCartItems($customer?->id, $sessionId);
        $cartTotal = Cart::getCartTotal($customer?->id, $sessionId);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Add some products before checkout.');
        }

        // Check stock availability for all items
        foreach ($cartItems as $item) {
            if (!$item->product->hasStock($item->quantity)) {
                return redirect()->route('cart.index')
                    ->with('error', "Insufficient stock for {$item->product->name}. Please update your cart.");
            }
        }

        return view('orders.checkout', compact('cartItems', 'cartTotal', 'customer'));
    }

    /**
     * Place an order.
     */
    public function place(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $sessionId = session()->getId();

        $cartItems = Cart::getCartItems($customer?->id, $sessionId);
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Calculate total
        $totalAmount = $cartItems->sum('total');

        // Prepare addresses
        $billingAddress = $request->input('billing_address');
        $shippingAddress = $request->boolean('shipping_same_as_billing') 
            ? $billingAddress 
            : $request->input('shipping_address');

        try {
            return DB::transaction(function () use ($request, $customer, $cartItems, $totalAmount, $billingAddress, $shippingAddress, $sessionId) {
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

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to place order: ' . $e->getMessage())
                ->withInput();
        }

        return redirect()->route('orders.track', $order->order_number)
            ->with('success', 'Order placed successfully! Your order number is ' . $order->order_number);
    }

    /**
     * Track an order.
     */
    public function track($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with(['customer', 'orderItems.product'])
            ->firstOrFail();

        // Check if the customer can view this order
        $customer = Auth::guard('customer')->user();
        if ($order->customer_id && (!$customer || $customer->id !== $order->customer_id)) {
            abort(403, 'You are not authorized to view this order.');
        }

        return view('orders.track', compact('order'));
    }

    /**
     * Guest checkout page.
     */
    public function guestCheckout()
    {
        $sessionId = session()->getId();
        $cartItems = Cart::getCartItems(null, $sessionId);
        $cartTotal = Cart::getCartTotal(null, $sessionId);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Add some products before checkout.');
        }

        // Check stock availability for all items
        foreach ($cartItems as $item) {
            if (!$item->product->hasStock($item->quantity)) {
                return redirect()->route('cart.index')
                    ->with('error', "Insufficient stock for {$item->product->name}. Please update your cart.");
            }
        }

        return view('orders.guest-checkout', compact('cartItems', 'cartTotal'));
    }

    /**
     * Place order as guest.
     */
    public function guestPlaceOrder(Request $request)
    {
        $sessionId = session()->getId();
        $cartItems = Cart::getCartItems(null, $sessionId);
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Calculate total
        $totalAmount = $cartItems->sum('total');

        // Prepare addresses
        $billingAddress = $request->input('billing_address');
        $billingAddress['email'] = $request->email; // Add email to billing address
        
        $shippingAddress = $request->boolean('shipping_same_as_billing') 
            ? $billingAddress 
            : $request->input('shipping_address');

        try {
            $order = DB::transaction(function () use ($request, $cartItems, $totalAmount, $billingAddress, $shippingAddress, $sessionId) {
                // Create order (no customer_id for guest orders)
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'customer_id' => null,
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'billing_address' => $billingAddress,
                    'shipping_address' => $shippingAddress,
                ]);

                // Create order items and update stock
                foreach ($cartItems as $cartItem) {
                    // Check stock again
                    if (!$cartItem->product->hasStock($cartItem->quantity)) {
                        throw new \Exception("Insufficient stock for {$cartItem->product->name}");
                    }

                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->product->price,
                    ]);

                    // Reduce stock
                    $cartItem->product->reduceStock($cartItem->quantity);
                }

                // Clear cart
                Cart::forSession($sessionId)->delete();

                // Track order creation in CRM
                $this->crmService->trackOrderCreation($order);

                return $order;
            });

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to place order: ' . $e->getMessage())
                ->withInput();
        }

        return redirect()->route('orders.track', $order->order_number)
            ->with('success', 'Order placed successfully! Your order number is ' . $order->order_number);
    }
}