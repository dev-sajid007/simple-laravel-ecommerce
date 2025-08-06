<?php $__env->startSection('title', 'Products'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1>Products</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
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
                    <a href="<?php echo e(route('products.index')); ?>" class="d-block text-decoration-none mb-2 <?php echo e(!request('category') ? 'fw-bold text-primary' : 'text-muted'); ?>">
                        All Categories
                    </a>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('products.index', ['category' => $category] + request()->except('category'))); ?>" 
                       class="d-block text-decoration-none mb-2 <?php echo e(request('category') === $category ? 'fw-bold text-primary' : 'text-muted'); ?>">
                        <?php echo e(ucfirst($category)); ?>

                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <!-- Search and Sort -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="<?php echo e(route('products.index')); ?>" method="GET" class="d-flex">
                        <?php $__currentLoopData = request()->except(['search', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo e(request('search')); ?>">
                        <button type="submit" class="btn btn-outline-primary ms-2">Search</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form action="<?php echo e(route('products.index')); ?>" method="GET" class="d-flex justify-content-end">
                        <?php $__currentLoopData = request()->except(['sort', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <select name="sort" class="form-select w-auto" onchange="this.form.submit()">
                            <option value="latest" <?php echo e($sortBy === 'latest' ? 'selected' : ''); ?>>Latest</option>
                            <option value="name" <?php echo e($sortBy === 'name' ? 'selected' : ''); ?>>Name</option>
                            <option value="price_low" <?php echo e($sortBy === 'price_low' ? 'selected' : ''); ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo e($sortBy === 'price_high' ? 'selected' : ''); ?>>Price: High to Low</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if($product->image): ?>
                            <img src="<?php echo e($product->image); ?>" class="card-img-top" alt="<?php echo e($product->name); ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?php echo e($product->name); ?></h6>
                            <p class="card-text text-muted small flex-grow-1"><?php echo e(Str::limit($product->description, 100)); ?></p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="h5 mb-0 text-primary">$<?php echo e(number_format($product->price, 2)); ?></span>
                                <?php if($product->stock_quantity > 0): ?>
                                    <small class="text-success"><?php echo e($product->stock_quantity); ?> in stock</small>
                                <?php else: ?>
                                    <small class="text-danger">Out of Stock</small>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-secondary mb-2"><?php echo e(ucfirst($product->category)); ?></span>
                        </div>
                        
                        <div class="card-footer bg-white border-0">
                            <div class="row g-2">
                                <div class="col">
                                    <a href="<?php echo e(route('products.show', $product->id)); ?>" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </div>
                                <?php if($product->stock_quantity > 0): ?>
                                <div class="col">
                                    <button onclick="addToCart(<?php echo e($product->id); ?>)" class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h3 class="mt-3">No products found</h3>
                    <p class="text-muted">Try adjusting your search or filter criteria.</p>
                    <a href="<?php echo e(route('products.index')); ?>" class="btn btn-primary">View All Products</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if($products->hasPages()): ?>
            <div class="d-flex justify-content-center">
                <?php echo e($products->appends(request()->query())->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/runner/work/simple-laravel-ecommerce/simple-laravel-ecommerce/resources/views/products/index.blade.php ENDPATH**/ ?>