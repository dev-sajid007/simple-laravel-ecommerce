@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="container py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col">
            <h1>Welcome back, {{ $customer->first_name }}!</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Dashboard Overview -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bag-check display-4 mb-2"></i>
                    <h4>{{ $customer->order_count }}</h4>
                    <p class="mb-0">Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar display-4 mb-2"></i>
                    <h4>${{ number_format($customer->total_spent, 2) }}</h4>
                    <p class="mb-0">Total Spent</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check display-4 mb-2"></i>
                    <h6>{{ $customer->last_order_at ? $customer->last_order_at->format('M d, Y') : 'Never' }}</h6>
                    <p class="mb-0">Last Order</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cart3 display-4 mb-2"></i>
                    <h4 id="dashboard-cart-count">
                        @php
                            $cartCount = App\Models\Cart::getCartCount($customer->id, null);
                        @endphp
                        {{ $cartCount }}
                    </h4>
                    <p class="mb-0">Items in Cart</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Orders</h5>
                    <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-primary">View All Orders</a>
                </div>
                <div class="card-body">
                    @if($recentOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('customer.orders.show', $order->id) }}" class="text-decoration-none">
                                                {{ $order->formatted_order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>${{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            @switch($order->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                    @break
                                                @case('processing')
                                                    <span class="badge bg-info">Processing</span>
                                                    @break
                                                @case('shipped')
                                                    <span class="badge bg-primary">Shipped</span>
                                                    @break
                                                @case('delivered')
                                                    <span class="badge bg-success">Delivered</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.track', $order->order_number) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-truck"></i> Track
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-bag display-1 text-muted"></i>
                            <h5 class="mt-2">No orders yet</h5>
                            <p class="text-muted">Start shopping to see your orders here.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                <i class="bi bi-shop"></i> Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-shop"></i> Browse Products
                        </a>
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-success">
                            <i class="bi bi-cart3"></i> View Cart
                        </a>
                        <a href="{{ route('customer.orders') }}" class="btn btn-outline-info">
                            <i class="bi bi-bag-check"></i> Order History
                        </a>
                        <a href="{{ route('customer.profile') }}" class="btn btn-outline-warning">
                            <i class="bi bi-person-gear"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Account Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $customer->full_name }}</p>
                    <p><strong>Email:</strong> {{ $customer->email }}</p>
                    @if($customer->phone)
                        <p><strong>Phone:</strong> {{ $customer->phone }}</p>
                    @endif
                    <p><strong>Member since:</strong> {{ $customer->created_at->format('F Y') }}</p>
                    <p class="mb-0">
                        <strong>Marketing emails:</strong> 
                        @if($customer->accepts_marketing)
                            <span class="text-success">Subscribed</span>
                        @else
                            <span class="text-muted">Not subscribed</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection