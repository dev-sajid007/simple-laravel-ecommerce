<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CrmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    private CrmService $crmService;

    public function __construct(CrmService $crmService)
    {
        $this->crmService = $crmService;
    }

    /**
     * Display products with pagination and filtering.
     */
    public function index(Request $request)
    {
        $query = Product::active()->inStock();

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        // Get all categories for filter
        $categories = Product::active()
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('products.index', compact('products', 'categories', 'sortBy'));
    }

    /**
     * Display a single product.
     */
    public function show($id)
    {
        $product = Product::active()->findOrFail($id);

        // Track product view in CRM
        $customer = Auth::guard('customer')->user();
        $sessionId = session()->getId();
        
        $this->crmService->trackProductView(
            $product,
            $customer,
            $sessionId
        );

        // Get related products (same category)
        $relatedProducts = Product::active()
            ->inStock()
            ->byCategory($product->category)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Display products by category.
     */
    public function category($category, Request $request)
    {
        $query = Product::active()
            ->inStock()
            ->byCategory($category);

        // Search within category
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        // Get all categories for navigation
        $categories = Product::active()
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('products.category', compact('products', 'categories', 'category', 'sortBy'));
    }

    /**
     * Search products.
     */
    public function search(Request $request)
    {
        $searchTerm = $request->get('q', '');
        
        if (empty($searchTerm)) {
            return redirect()->route('products.index');
        }

        $query = Product::active()
            ->inStock()
            ->search($searchTerm);

        // Sort options
        $sortBy = $request->get('sort', 'relevance');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                // For relevance, we'll just use latest for now
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        // Get all categories for filter
        $categories = Product::active()
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('products.search', compact('products', 'categories', 'searchTerm', 'sortBy'));
    }
}