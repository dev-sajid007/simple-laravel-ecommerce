@extends('layouts.app')

@section('title', 'Customer Login')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-person-circle"></i> Customer Login</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('customer.login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p class="mb-2">Don't have an account? <a href="{{ route('customer.register') }}">Register here</a></p>
                    <a href="#" class="text-muted small">Forgot your password?</a>
                </div>
            </div>

            <!-- Demo Account Info -->
            <div class="alert alert-info mt-3" role="alert">
                <h6><i class="bi bi-info-circle"></i> Demo Account</h6>
                <p class="mb-1"><strong>Email:</strong> demo@example.com</p>
                <p class="mb-0"><strong>Password:</strong> password</p>
            </div>
        </div>
    </div>
</div>
@endsection