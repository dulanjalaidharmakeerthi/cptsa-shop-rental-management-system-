<header class="dashboard-navbar">
    
    <div class="nav-center">@yield('title')</div>
    <div class="nav-right">
        <div class="profile" id="profile">
            <button class="profile-btn" aria-haspopup="true" aria-expanded="false">
                <img src="{{ asset('images/logo1.jpeg') }}" alt="avatar" class="avatar">
            </button>

            <div class="profile-menu" role="menu" aria-hidden="true">
                <div class="profile-info">
                    <div class="profile-name">{{ Auth::user()->name ?? 'Manager' }}</div>
                    <div class="profile-email">{{ Auth::user()->email ?? 'manager@example.com' }}</div>
                </div>
                <div class="profile-actions">
                    <a href="{{ route('dashboard.profile.show') }}">View profile</a>
                    <a href="/logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>
