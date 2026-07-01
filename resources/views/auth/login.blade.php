@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center" style="min-height: 70vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-6">
            <h2>Welcome Back</h2>
            <p class="text-muted">Login to access your dashboard</p>
        </div>

        <form method="POST" action="{{ route('authenticate') }}">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required autofocus>
                @error('email')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </form>

        <div class="mt-6 text-center text-sm">
            <p>Admin Login: admin@example.com / password</p>
            <p>Student Login: student@example.com / password</p>
        </div>
    </div>
</div>
@endsection
