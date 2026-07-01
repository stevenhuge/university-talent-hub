<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>University Talent Hub</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                TalentHub
            </a>
            <div class="navbar-nav">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                        <a href="{{ route('admin.search') }}" class="nav-link">Talent Search</a>
                        <a href="{{ route('admin.verification') }}" class="nav-link">Verifications</a>
                        <a href="{{ route('admin.rewards') }}" class="nav-link">Rewards</a>
                    @else
                        <a href="{{ route('student.dashboard') }}" class="nav-link">Dashboard</a>
                        <a href="{{ route('student.profile') }}" class="nav-link">My Profile</a>
                        <a href="{{ route('student.leaderboard') }}" class="nav-link">Leaderboard</a>
                        <a href="{{ route('student.rewards') }}" class="nav-link">Rewards</a>
                    @endif
                    
                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="nav-link">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Mobile Nav (Visible only on small screens) -->
    @auth
    <div class="navbar-mobile-nav hidden">
         @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="nav-link">Dash</a>
            <a href="{{ route('admin.search') }}" class="nav-link">Search</a>
            <a href="{{ route('admin.verification') }}" class="nav-link">Verify</a>
            <a href="{{ route('admin.rewards') }}" class="nav-link">Rewards</a>
        @else
            <a href="{{ route('student.dashboard') }}" class="nav-link">Dash</a>
            <a href="{{ route('student.profile') }}" class="nav-link">Profile</a>
            <a href="{{ route('student.leaderboard') }}" class="nav-link">Rank</a>
            <a href="{{ route('student.rewards') }}" class="nav-link">Rewards</a>
        @endif
    </div>
    @endauth

    <!-- Main Content -->
    <main class="main-content container animate-fade-in">
        @if(session('success'))
            <div class="card mb-6" style="background-color: rgba(16, 185, 129, 0.1); border-color: var(--secondary);">
                <p style="color: var(--secondary); font-weight: 600;">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="card mb-6" style="background-color: rgba(239, 68, 68, 0.1); border-color: var(--danger);">
                <p style="color: var(--danger); font-weight: 600;">{{ session('error') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p class="text-muted text-sm">&copy; {{ date('Y') }} Ridho Karunia Setiawan (awansetiawann999@students.amikom.ac.id). Built with Laravel.</p>
        </div>
    </footer>

    <script>
        // Simple script to toggle mobile nav visibility based on screen width
        function checkMobileNav() {
            const mobileNav = document.querySelector('.navbar-mobile-nav');
            if (mobileNav) {
                if (window.innerWidth <= 768) {
                    mobileNav.classList.remove('hidden');
                } else {
                    mobileNav.classList.add('hidden');
                }
            }
        }
        window.addEventListener('resize', checkMobileNav);
        checkMobileNav();
    </script>
</body>
</html>
