<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CrmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CustomerController extends Controller
{
    private CrmService $crmService;

    public function __construct(CrmService $crmService)
    {
        $this->crmService = $crmService;
    }

    /**
     * Show the customer registration form.
     */
    public function showRegistrationForm()
    {
        return view('customer.register');
    }

    /**
     * Handle customer registration.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::defaults()],
            'accepts_marketing' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'accepts_marketing' => $request->boolean('accepts_marketing'),
        ]);

        // Track customer registration in CRM
        $this->crmService->trackCustomerRegistration($customer);

        // Log the customer in
        Auth::guard('customer')->login($customer);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Registration successful! Welcome to our store.');
    }

    /**
     * Show the customer login form.
     */
    public function showLoginForm()
    {
        return view('customer.login');
    }

    /**
     * Handle customer login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::guard('customer')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('customer.dashboard'))
                ->with('success', 'Welcome back!');
        }

        return redirect()->back()
            ->withErrors(['email' => 'Invalid credentials'])
            ->withInput();
    }

    /**
     * Handle customer logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the customer dashboard.
     */
    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        $recentOrders = $customer->recentOrders(5);

        return view('customer.dashboard', compact('customer', 'recentOrders'));
    }

    /**
     * Show the customer profile.
     */
    public function profile()
    {
        $customer = Auth::guard('customer')->user();
        return view('customer.profile', compact('customer'));
    }

    /**
     * Update customer profile.
     */
    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:password',
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'accepts_marketing' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $customer->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is incorrect'])
                    ->withInput();
            }
        }

        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'accepts_marketing' => $request->boolean('accepts_marketing'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $customer->update($updateData);

        // Sync updated customer data with CRM
        $this->crmService->syncCustomerData($customer);

        return redirect()->back()
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show customer orders.
     */
    public function orders()
    {
        $customer = Auth::guard('customer')->user();
        $orders = $customer->orders()
            ->with('orderItems.product')
            ->latest()
            ->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    /**
     * Show specific order details.
     */
    public function orderDetails($id)
    {
        $customer = Auth::guard('customer')->user();
        $order = $customer->orders()
            ->with(['orderItems.product'])
            ->findOrFail($id);

        return view('customer.order-details', compact('order'));
    }
}