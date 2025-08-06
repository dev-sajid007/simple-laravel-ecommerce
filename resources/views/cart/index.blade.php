@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1>Shopping Cart</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Cart</li>
                </ol>
            </nav>
        </div>
    </div>

    @if($cartItems->count() > 0)
    <div class="row">
        <div class="col-lg-8">
            <!-- Cart Items -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Cart Items ({{ $cartCount }})</h5>
                    <form action="{{ route('cart.clear') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear your cart?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash"></i> Clear Cart
                        </button>
                    </form>
                </div>
                <div class="card-body p-0">
                    @foreach($cartItems as $item)
                    <div class="cart-item border-bottom p-3" data-item-id="{{ $item->id }}">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                @if($item->product->image)
                                    <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="img-fluid rounded" style="height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px; width: 80px;">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <h6 class="mb-1">{{ $item->product->name }}</h6>
                                <small class="text-muted">{{ $item->product->category }}</small>
                                <div class="mt-1">
                                    <a href="{{ route('products.show', $item->product->id) }}" class="btn btn-link btn-sm p-0">View Product</a>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <span class="fw-bold">${{ number_format($item->product->price, 2) }}</span>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group input-group-sm">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">-</button>
                                    <input type="number" class="form-control text-center quantity-input" value="{{ $item->quantity }}" min="1" max="10" data-item-id="{{ $item->id }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">+</button>
                                </div>
                                <small class="text-muted d-block mt-1">Max: {{ $item->product->stock_quantity }}</small>
                            </div>
                            <div class="col-md-1">
                                <span class="fw-bold item-total" data-item-id="{{ $item->id }}">${{ number_format($item->total, 2) }}</span>
                            </div>
                            <div class="col-md-1">
                                <button onclick="removeItem({{ $item->id }})" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal ({{ $cartCount }} items)</span>
                        <span id="cart-subtotal">${{ number_format($cartTotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping</span>
                        <span class="text-success">Free</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tax</span>
                        <span>$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span id="cart-total">${{ number_format($cartTotal, 2) }}</span>
                    </div>
                </div>
                <div class="card-footer">
                    @auth('customer')
                        <a href="{{ route('orders.checkout') }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-credit-card"></i> Proceed to Checkout
                        </a>
                    @else
                        <a href="{{ route('guest.checkout') }}" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-credit-card"></i> Checkout as Guest
                        </a>
                        <a href="{{ route('customer.login') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-person"></i> Login to Checkout
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Continue Shopping -->
            <div class="mt-3 text-center">
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
    @else
    <!-- Empty Cart -->
    <div class="text-center py-5">
        <i class="bi bi-cart-x display-1 text-muted"></i>
        <h3 class="mt-3">Your cart is empty</h3>
        <p class="text-muted">Looks like you haven't added any items to your cart yet.</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
            <i class="bi bi-shop"></i> Start Shopping
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) {
        removeItem(itemId);
        return;
    }

    $.ajax({
        url: `/cart/update/${itemId}`,
        method: 'PUT',
        data: { quantity: newQuantity },
        success: function(response) {
            if (response.success) {
                // Update quantity input
                $(`.quantity-input[data-item-id="${itemId}"]`).val(newQuantity);
                
                // Update item total
                $(`.item-total[data-item-id="${itemId}"]`).text(response.item_total);
                
                // Update cart totals
                $('#cart-subtotal').text(response.cart_total);
                $('#cart-total').text(response.cart_total);
                
                // Update cart count in navbar
                updateCartCount(response.cart_count);
            } else {
                showAlert('danger', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert('danger', response?.message || 'An error occurred');
        }
    });
}

function removeItem(itemId) {
    if (!confirm('Remove this item from your cart?')) {
        return;
    }

    $.ajax({
        url: `/cart/remove/${itemId}`,
        method: 'DELETE',
        success: function(response) {
            if (response.success) {
                // Remove item from DOM
                $(`.cart-item[data-item-id="${itemId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    
                    // Check if cart is empty
                    if ($('.cart-item').length === 0) {
                        location.reload();
                    }
                });
                
                // Update cart totals
                $('#cart-subtotal').text(response.cart_total);
                $('#cart-total').text(response.cart_total);
                
                // Update cart count in navbar
                updateCartCount(response.cart_count);
                
                showAlert('success', response.message);
            } else {
                showAlert('danger', response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showAlert('danger', response?.message || 'An error occurred');
        }
    });
}

// Handle quantity input changes
$(document).on('change', '.quantity-input', function() {
    const itemId = $(this).data('item-id');
    const newQuantity = parseInt($(this).val());
    updateQuantity(itemId, newQuantity);
});
</script>
@endpush
@endsection