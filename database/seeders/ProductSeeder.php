<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Bluetooth Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation and long battery life. Perfect for music lovers and professionals.',
                'price' => 89.99,
                'image' => 'https://via.placeholder.com/400x300/007bff/ffffff?text=Headphones',
                'category' => 'electronics',
                'stock_quantity' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Smartphone 128GB',
                'description' => 'Latest smartphone with 128GB storage, dual camera, and fast processor. Stay connected wherever you go.',
                'price' => 599.99,
                'image' => 'https://via.placeholder.com/400x300/28a745/ffffff?text=Smartphone',
                'category' => 'electronics',
                'stock_quantity' => 25,
                'is_active' => true,
            ],
            [
                'name' => 'Cotton T-Shirt',
                'description' => '100% cotton comfortable t-shirt available in multiple colors. Perfect for casual wear.',
                'price' => 19.99,
                'image' => 'https://via.placeholder.com/400x300/dc3545/ffffff?text=T-Shirt',
                'category' => 'clothing',
                'stock_quantity' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Jeans - Slim Fit',
                'description' => 'Comfortable slim-fit jeans made from premium denim. Available in various sizes.',
                'price' => 49.99,
                'image' => 'https://via.placeholder.com/400x300/6c757d/ffffff?text=Jeans',
                'category' => 'clothing',
                'stock_quantity' => 75,
                'is_active' => true,
            ],
            [
                'name' => 'Coffee Maker',
                'description' => 'Automatic coffee maker with programmable timer and thermal carafe. Start your day right.',
                'price' => 129.99,
                'image' => 'https://via.placeholder.com/400x300/ffc107/ffffff?text=Coffee+Maker',
                'category' => 'home',
                'stock_quantity' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Kitchen Knife Set',
                'description' => 'Professional kitchen knife set with wooden block. Essential for any home chef.',
                'price' => 79.99,
                'image' => 'https://via.placeholder.com/400x300/20c997/ffffff?text=Knife+Set',
                'category' => 'home',
                'stock_quantity' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Running Shoes',
                'description' => 'Comfortable running shoes with advanced cushioning and breathable material.',
                'price' => 109.99,
                'image' => 'https://via.placeholder.com/400x300/17a2b8/ffffff?text=Running+Shoes',
                'category' => 'sports',
                'stock_quantity' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Yoga Mat',
                'description' => 'Non-slip yoga mat perfect for all types of yoga and exercise routines.',
                'price' => 29.99,
                'image' => 'https://via.placeholder.com/400x300/6f42c1/ffffff?text=Yoga+Mat',
                'category' => 'sports',
                'stock_quantity' => 80,
                'is_active' => true,
            ],
            [
                'name' => 'Laptop Backpack',
                'description' => 'Durable laptop backpack with multiple compartments and padded protection.',
                'price' => 59.99,
                'image' => 'https://via.placeholder.com/400x300/e83e8c/ffffff?text=Backpack',
                'category' => 'accessories',
                'stock_quantity' => 45,
                'is_active' => true,
            ],
            [
                'name' => 'Sunglasses',
                'description' => 'Stylish sunglasses with UV protection and polarized lenses.',
                'price' => 39.99,
                'image' => 'https://via.placeholder.com/400x300/fd7e14/ffffff?text=Sunglasses',
                'category' => 'accessories',
                'stock_quantity' => 70,
                'is_active' => true,
            ],
            [
                'name' => 'Gaming Mouse',
                'description' => 'High-precision gaming mouse with customizable buttons and RGB lighting.',
                'price' => 69.99,
                'image' => 'https://via.placeholder.com/400x300/007bff/ffffff?text=Gaming+Mouse',
                'category' => 'electronics',
                'stock_quantity' => 35,
                'is_active' => true,
            ],
            [
                'name' => 'Wireless Keyboard',
                'description' => 'Compact wireless keyboard with long battery life and quiet keys.',
                'price' => 79.99,
                'image' => 'https://via.placeholder.com/400x300/28a745/ffffff?text=Keyboard',
                'category' => 'electronics',
                'stock_quantity' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Winter Jacket',
                'description' => 'Warm winter jacket with water-resistant fabric and insulated lining.',
                'price' => 149.99,
                'image' => 'https://via.placeholder.com/400x300/dc3545/ffffff?text=Winter+Jacket',
                'category' => 'clothing',
                'stock_quantity' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Desk Lamp',
                'description' => 'Adjustable LED desk lamp with multiple brightness levels and USB charging port.',
                'price' => 49.99,
                'image' => 'https://via.placeholder.com/400x300/ffc107/ffffff?text=Desk+Lamp',
                'category' => 'home',
                'stock_quantity' => 55,
                'is_active' => true,
            ],
            [
                'name' => 'Basketball',
                'description' => 'Official size basketball made from durable rubber with excellent grip.',
                'price' => 24.99,
                'image' => 'https://via.placeholder.com/400x300/17a2b8/ffffff?text=Basketball',
                'category' => 'sports',
                'stock_quantity' => 90,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}