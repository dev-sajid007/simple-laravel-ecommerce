<?php $__env->startSection('title', 'Welcome to Laravel Ecommerce'); ?>

<?php $__env->startSection('content'); ?>
<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold">Welcome to Laravel Ecommerce</h1>
                <p class="lead">Discover amazing products at unbeatable prices. Shop now and experience the best online shopping.</p>
                <a href="<?php echo e(route('products.index')); ?>" class="btn btn-light btn-lg">Shop Now</a>
            </div>
            <div class="col-lg-6">
                <img src="https://via.placeholder.com/600x400/6c757d/ffffff?text=Hero+Image" alt="Hero Image" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Shop by Category</h2>
        <div class="row">
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-3 mb-4">
                <a href="<?php echo e(route('products.category', $category)); ?>" class="text-decoration-none">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-tag-fill display-4 text-primary mb-3"></i>
                            <h5 class="card-title"><?php echo e(ucfirst($category)); ?></h5>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Featured Products</h2>
        <div class="row">
            <?php $__empty_1 = true; $__currentLoopData = $featuredProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
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
                        <p class="card-text text-muted small flex-grow-1"><?php echo e(Str::limit($product->description, 80)); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 text-primary">$<?php echo e(number_format($product->price, 2)); ?></span>
                            <?php if($product->stock_quantity > 0): ?>
                                <small class="text-success">In Stock</small>
                            <?php else: ?>
                                <small class="text-danger">Out of Stock</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer bg-white border-0">
                        <div class="row g-2">
                            <div class="col">
                                <a href="<?php echo e(route('products.show', $product->id)); ?>" class="btn btn-outline-primary btn-sm w-100">View</a>
                            </div>
                            <?php if($product->stock_quantity > 0): ?>
                            <div class="col">
                                <button onclick="addToCart(<?php echo e($product->id); ?>)" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-cart-plus"></i> Add
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-12 text-center">
                <p class="text-muted">No featured products available at the moment.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if($featuredProducts->count() > 0): ?>
        <div class="text-center mt-4">
            <a href="<?php echo e(route('products.index')); ?>" class="btn btn-primary btn-lg">View All Products</a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card border-0">
                    <div class="card-body">
                        <i class="bi bi-truck display-4 text-primary mb-3"></i>
                        <h5>Free Shipping</h5>
                        <p class="text-muted">Free shipping on orders over $50</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0">
                    <div class="card-body">
                        <i class="bi bi-shield-check display-4 text-primary mb-3"></i>
                        <h5>Secure Payment</h5>
                        <p class="text-muted">Your payment information is safe with us</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0">
                    <div class="card-body">
                        <i class="bi bi-headset display-4 text-primary mb-3"></i>
                        <h5>24/7 Support</h5>
                        <p class="text-muted">Get help anytime, anywhere</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/runner/work/simple-laravel-ecommerce/simple-laravel-ecommerce/resources/views/home.blade.php ENDPATH**/ ?>