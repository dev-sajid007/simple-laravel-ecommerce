@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1>Products</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filters and Sorting -->
    <div class="row mb-4">
        <div class="col-md-3">
            <!-- Category Filter -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Categories</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('products.index') }}" class="d-block text-decoration-none mb-2 {{ !request('category') ? 'fw-bold text-primary' : 'text-muted' }}">
                        All Categories
                    </a>
                    @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category] + request()->except('category')) }}" 
                       class="d-block text-decoration-none mb-2 {{ request('category') === $category ? 'fw-bold text-primary' : 'text-muted' }}">
                        {{ ucfirst($category) }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Search and Sort -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="{{ route('products.index') }}" method="GET" class="d-flex">
                        @foreach(request()->except(['search', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary ms-2">Search</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('products.index') }}" method="GET" class="d-flex justify-content-end">
                        @foreach(request()->except(['sort', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="sort" class="form-select w-auto" onchange="this.form.submit()">
                            <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Name</option>
                            <option value="price_low" {{ $sortBy === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ $sortBy === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row">
                @forelse($products as $product)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($product->image)
                            <img src="{{ $product->image }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $product->name }}</h6>
                            <p class="card-text text-muted small flex-grow-1">{{ Str::limit($product->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h5 mb-0 text-primary">${{ number_format($product->price, 2) }}</span>
                                @if($product->stock_quantity > 0)
                                    <small class="text-success">{{ $product->stock_quantity }} in stock</small>
                                @else
                                    <small class="text-danger">Out of Stock</small>
                                @endif
                            </div>
                            <span class="badge bg-secondary mb-2">{{ ucfirst($product->category) }}</span>
                        </div>
                        
                        <div class="card-footer bg-white border-0">
                            <div class="row g-2">
                                <div class="col">
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </div>
                                @if($product->stock_quantity > 0)
                                <div class="col">
                                    <button onclick="addToCart({{ $product->id }})" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h3 class="mt-3">No products found</h3>
                    <p class="text-muted">Try adjusting your search or filter criteria.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">View All Products</a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
            <div class="d-flex justify-content-center">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection