@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.category', $product->category) }}">{{ ucfirst($product->category) }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <!-- Product Details -->
    <div class="row">
        <div class="col-md-6 mb-4">
            @if($product->image)
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="img-fluid rounded shadow">
            @else
                <div class="bg-light rounded shadow d-flex align-items-center justify-content-center" style="height: 400px;">
                    <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                </div>
            @endif
        </div>
        
        <div class="col-md-6">
            <h1 class="mb-3">{{ $product->name }}</h1>
            
            <div class="mb-3">
                <span class="badge bg-secondary">{{ ucfirst($product->category) }}</span>
            </div>
            
            <p class="text-muted mb-4">{{ $product->description }}</p>
            
            <div class="mb-4">
                <h3 class="text-primary">${{ number_format($product->price, 2) }}</h3>
                @if($product->stock_quantity > 0)
                    <p class="text-success mb-0">
                        <i class="bi bi-check-circle"></i> {{ $product->stock_quantity }} items in stock
                    </p>
                @else
                    <p class="text-danger mb-0">
                        <i class="bi bi-x-circle"></i> Out of stock
                    </p>
                @endif
            </div>
            
            @if($product->stock_quantity > 0)
            <div class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label">Quantity</label>
                        <select id="quantity" class="form-select">
                            @for($i = 1; $i <= min(10, $product->stock_quantity); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-8 d-flex align-items-end">
                        <button onclick="addToCartWithQuantity({{ $product->id }})" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                        <button class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-heart"></i> Wishlist
                        </button>
                    </div>
                </div>
            </div>
            @else
            <div class="mb-4">
                <button class="btn btn-secondary btn-lg me-2" disabled>
                    <i class="bi bi-cart-x"></i> Out of Stock
                </button>
                <button class="btn btn-outline-primary btn-lg">
                    <i class="bi bi-bell"></i> Notify When Available
                </button>
            </div>
            @endif
            
            <!-- Product Features -->
            <div class="border-top pt-4">
                <h5>Product Features</h5>
                <ul class="list-unstyled">
                    <li><i class="bi bi-check text-success"></i> High quality materials</li>
                    <li><i class="bi bi-check text-success"></i> Fast shipping</li>
                    <li><i class="bi bi-check text-success"></i> 30-day return policy</li>
                    <li><i class="bi bi-check text-success"></i> Customer support</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
    <div class="mt-5">
        <h3 class="mb-4">Related Products</h3>
        <div class="row">
            @foreach($relatedProducts as $relatedProduct)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm">
                    @if($relatedProduct->image)
                        <img src="{{ $relatedProduct->image }}" class="card-img-top" alt="{{ $relatedProduct->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title">{{ $relatedProduct->name }}</h6>
                        <p class="card-text text-muted small flex-grow-1">{{ Str::limit($relatedProduct->description, 80) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h6 mb-0 text-primary">${{ number_format($relatedProduct->price, 2) }}</span>
                            @if($relatedProduct->stock_quantity > 0)
                                <small class="text-success">In Stock</small>
                            @else
                                <small class="text-danger">Out of Stock</small>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-0">
                        <div class="row g-2">
                            <div class="col">
                                <a href="{{ route('products.show', $relatedProduct->id) }}" class="btn btn-outline-primary btn-sm w-100">View</a>
                            </div>
                            @if($relatedProduct->stock_quantity > 0)
                            <div class="col">
                                <button onclick="addToCart({{ $relatedProduct->id }})" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function addToCartWithQuantity(productId) {
    const quantity = document.getElementById('quantity').value;
    addToCart(productId, parseInt(quantity));
}
</script>
@endpush
@endsection