<aside class="dashboard-sidebar" id="sidebar">
    <div class="sidebar-inner">
        @auth
            <div class="sidebar-profile">
                <div class="profile-container">
                    <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                    <div class="profile-info">
                        <div class="profile-name">{{ auth()->user()->name }}</div>
                        @if(auth()->user()->phone)
                            <div class="profile-phone">{{ auth()->user()->phone }}</div>
                        @endif
                    </div>
                </div>
            </div>
        @endauth
        @guest
            <div class="sidebar-profile">
                <a href="{{ route('login') }}" class="btn btn-signin">Sign in</a>
            </div>
        @endguest

        <nav class="sidebar-nav">
            <ul>
                @if (strtolower(auth()->user()->position ?? '') === 'accountant')
                    <li class="nav-item"><a href="{{ route('managedashboard') }}" class="nav-link"><i class="fas fa-chart-pie"></i><span>Overview</span></a></li>
                    <li class="nav-item"><a href="{{ route('accountant.payments.index') }}" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
                    <li class="nav-item"><a href="{{ route('history') }}" class="nav-link"><i class="fas fa-history"></i><span>History</span></a></li>
                    <li class="nav-item"><a href="{{ route('dashboard.profile.show') }}" class="nav-link"><i class="fas fa-user-circle"></i><span>My Profile</span></a></li>
                @else
                <li class="nav-item manager-item">
                    <a href="{{ url('/managedashboard') }}" class="nav-link"
                       onclick="event.preventDefault(); window.location.href='{{ url('/managedashboard') }}?t=' + Date.now();">
                        <i class="fas fa-chart-pie"></i><span>Overview</span>
                    </a>
                </li>
                {{-- Create Shop and Users are now admin-only features. See admin sidebar for these routes --}}
                {{-- <li class="nav-item manager-item"><a href="{{ route('shops.create') }}" class="nav-link">      <i class="fas fa-plus-circle"></i><span>Create Shop</span></a></li> --}}
                {{-- <li class="nav-item manager-item"><a href="{{ route('users.create') }}" class="nav-link"><i class="fas fa-user-plus"></i><span>Create User</span></a></li> --}}
                <li class="nav-item manager-item"><a href="{{ route('payments.index') }}" class="nav-link"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
                <li class="nav-item manager-item"><a href="{{ route('history') }}" class="nav-link"><i class="fas fa-history"></i><span>History</span></a></li>

                <li class="nav-item">
                    <div class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle manager-item"><i class="fas fa-file-alt"></i><span>Reports</span> <i class="fas fa-chevron-down dropdown-icon"></i></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item"><a href="{{ route('reports.tenants.index') }}" class="dropdown-link"><i class="fas fa-home"></i>Tenants</a></li>
                            <li class="dropdown-item"><a href="{{ route('reports.shops.index') }}" class="dropdown-link"><i class="fas fa-store"></i>Shops Details</a></li>
                            <li class="dropdown-item"><a href="{{ route('reports.due.index') }}" class="dropdown-link"><i class="fas fa-money-bill-wave"></i>Due</a></li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <div class="dropdown">
                        <a href="#" class="nav-link dropdown-toggle manager-item"><i class="fas fa-cog"></i><span>Settings</span> <i class="fas fa-chevron-down dropdown-icon"></i></a>
                        <ul class="dropdown-menu">
                            <li class="dropdown-item">
                                <div class="dropdown">
                                    <a href="#" class="dropdown-link dropdown-toggle manager-item"><i class="fas fa-globe"></i>Language <i class="fas fa-chevron-down dropdown-icon"></i></a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-item"><a href="#" class="dropdown-link"><i class="fas fa-flag"></i>Sinhala</a></li>
                                        <li class="dropdown-item"><a href="#" class="dropdown-link"><i class="fas fa-flag"></i>English</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li class="dropdown-item"><a href="#" class="dropdown-link"><i class="fas fa-palette"></i>Theme</a></li>
                            <li class="dropdown-item"><a href="{{ route('dashboard.profile.show') }}" class="dropdown-link"><i class="fas fa-user-circle"></i>Profile</a></li>
                        </ul>
                    </div>
                </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>

<style>
    /* Import Font Awesome for icons */
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

    .dashboard-sidebar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .sidebar-inner {
        padding: 0;
    }

    /* Profile Section */
    .sidebar-profile {
        padding: 20px 16px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.15);
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }

    .profile-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .profile-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #ffffff;
        font-size: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        animation: avatarPulse 2s ease-in-out infinite;
    }

    @keyframes avatarPulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .profile-info {
        flex: 1;
        color: #ffffff;
    }

    .profile-name {
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 4px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .profile-position {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.85);
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .profile-phone {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.7);
        margin-top: 4px;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .btn-signin {
        width: 100%;
        padding: 10px 16px;
        background: linear-gradient(135deg, #f093fb, #f5576c);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
    }

    .btn-signin:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
    }

    .sidebar-inner {
        padding: 22px 14px;
    }
        list-style: none;
        padding: 16px 8px;
        margin: 0;
    }

    .nav-item {
        margin-bottom: 8px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        border-radius: 10px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        left: -100%;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.15);
        transition: left 0.3s ease;
        z-index: -1;
    }

    .nav-link:hover::before {
        left: 0;
    }

    .nav-link:hover {
        color: #ffffff;
        transform: translateX(4px);
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .nav-link i {
        font-size: 22px;
        min-width: 28px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .nav-link:hover i {
        transform: scale(1.15) rotate(5deg);
    }

    .nav-link span {
        flex: 1;
        font-size: 16px;
        color: #ffffff;
        font-weight: 600;
    }

    /* Manager-specific styling: white text and colorful icon */
    .manager-item .nav-link span {
        color: #ffffff;
        font-weight: 600;
        font-size: 16px;
    }

    /* Different color icons for each menu item */
    .nav-item:nth-child(1) .nav-link i {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-item:nth-child(2) .nav-link i {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-item:nth-child(3) .nav-link i {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-item:nth-child(4) .nav-link i {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-item:nth-child(5) .nav-link i {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-item:nth-child(6) .nav-link i {
        background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-item:nth-child(7) .nav-link i {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-item:nth-child(8) .nav-link i {
        background: linear-gradient(135deg, #ff9a56 0%, #ff6a88 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .manager-item .nav-link:hover i {
        transform: scale(1.15) rotate(6deg);
        opacity: 0.95;
        filter: brightness(1.2);
    }

    /* Dropdown */
    .dropdown {
        position: relative;
    }

    .dropdown-icon {
        font-size: 12px;
        transition: transform 0.3s ease;
        margin-left: auto;
    }

    .dropdown-toggle.show .dropdown-icon {
        transform: rotate(180deg);
    }

    .dropdown-menu {
        display: none;
        padding: 8px 0 8px 16px;
        margin: 8px 0 0 0;
        list-style: none;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background: rgba(255, 255, 255, 0.08);
        border-left: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 0 10px 10px 0;
    }

    .dropdown-menu.show {
        display: block;
        max-height: 1000px;
        animation: slideDown 0.3s ease forwards;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            max-height: 0;
        }
        to {
            opacity: 1;
            max-height: 1000px;
        }
    }

    .dropdown-item {
        position: relative;
    }

    .dropdown-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        color: #ffffff;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 600;
        font-size: 15px;
        margin: 4px 0;
    }

    .dropdown-link i {
        font-size: 18px;
        min-width: 24px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .dropdown-link:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(4px);
        padding-left: 18px;
    }

    .dropdown-link:hover i {
        transform: scale(1.2);
    }

    /* Colorful dropdown icons */
    .dropdown-item:nth-child(1) .dropdown-link i {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dropdown-item:nth-child(2) .dropdown-link i {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dropdown-item:nth-child(3) .dropdown-link i {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dropdown-menu .dropdown-link i {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dropdown-menu .dropdown-menu .dropdown-link i {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Nested Dropdown */
    .dropdown-menu .dropdown {
        position: relative;
    }

    .dropdown-menu .dropdown-menu {
        margin-left: 0;
        padding-left: 12px;
        border-left-color: rgba(255, 255, 255, 0.2);
        margin-top: 4px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var dropdowns = document.querySelectorAll('.dropdown-toggle');
        dropdowns.forEach(function(dropdown) {
            dropdown.addEventListener('click', function(event) {
                event.preventDefault();
                
                var menu = this.nextElementSibling;
                var isShowing = menu.classList.contains('show');
                
                // Close all other menus at the same level
                var siblings = this.parentElement.parentElement.querySelectorAll('.dropdown-menu');
                siblings.forEach(function(sibling) {
                    sibling.classList.remove('show');
                    sibling.previousElementSibling.classList.remove('show');
                });
                
                // Toggle current menu
                if (!isShowing) {
                    menu.classList.add('show');
                    this.classList.add('show');
                } else {
                    menu.classList.remove('show');
                    this.classList.remove('show');
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
                    toggle.classList.remove('show');
                });
            }
        });
    });
</script>
