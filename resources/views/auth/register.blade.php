@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center" style="min-height: 70vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-6">
            <h2>Join Talent Hub</h2>
            <p class="text-muted">Create an account to start building your profile</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            
            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" required autofocus value="{{ old('name') }}">
                @error('name')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required value="{{ old('email') }}">
                @error('email')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                @error('password')
                    <div style="color: var(--danger); font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
        </form>

        <div class="mt-6 text-center text-sm">
            <p>Already have an account? <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: none;">Login here</a></p>
        </div>
    </div>
</div>
@endsection
