<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CrmService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private CrmService $crmService;

    public function __construct(CrmService $crmService)
    {
        $this->crmService = $crmService;
    }

    /**
     * Display the home page.
     */
    public function index()
    {
        // Get featured products (latest 8 products)
        $featuredProducts = Product::active()
            ->inStock()
            ->latest()
            ->limit(8)
            ->get();

        // Get product categories
        $categories = Product::active()
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('home', compact('featuredProducts', 'categories'));
    }
}