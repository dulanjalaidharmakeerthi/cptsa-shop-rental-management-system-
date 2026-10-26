<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ShopRent</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
        }

        .container-fluid {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            overflow-y: auto;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 1000;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar-header h2 {
            font-size: 20px;
            font-weight: 600;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            font-size: 14px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #fbbf24;
            padding-left: 25px;
        }

        .sidebar-menu .icon {
            margin-right: 10px;
            font-size: 16px;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .topbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .topbar-title h1 {
            font-size: 24px;
            color: #667eea;
        }

        .topbar-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .logout-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239,68,68,0.3);
        }

        /* Content Area */
        .content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            transform: translateY(-4px);
        }

        .stat-card.blue {
            border-left-color: #3b82f6;
        }

        .stat-card.green {
            border-left-color: #10b981;
        }

        .stat-card.purple {
            border-left-color: #667eea;
        }

        .stat-card.orange {
            border-left-color: #f59e0b;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        /* Feature Cards */
        .features-section {
            margin-top: 30px;
        }

        .features-section h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #333;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .feature-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .feature-card:hover {
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            transform: translateY(-6px);
        }

        .feature-icon {
            font-size: 40px;
            margin-bottom: 15px;
            max-width: 50px;
            max-height: 50px;
            line-height: 1;
        }

        .feature-card a {
            display: inline-block;
            margin-top: 15px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 14px !important;
        }

        .feature-card a::after {
            font-size: 12px !important;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 0;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
        }
    </style>
    <style>
        /* Override site-wide large logo/h3 styles on admin pages */
        /* Hide any global .logo element that may be injected */
        .logo { display: none !important; }

        /* Ensure headings inside admin content are a readable size */
        .dashboard-content h1, .dashboard-content h2, .dashboard-content h3,
        .content h1, .content h2, .content h3 {
            font-size: 18px !important;
            line-height: 1.25 !important;
            margin: 0 0 8px 0 !important;
        }

        /* Also reset any overly large heading styles coming from public CSS */
        .dashboard-content .section-title, .content .section-title {
            font-size: 16px !important;
            font-weight: 700 !important;
        }

        /* Constrain all elements to reasonable sizes */
        * { max-width: 100%; }
        svg, img { max-width: 100%; height: auto !important; }
        
        /* Ensure feature card links are small */
        .feature-card a {
            max-width: 100%;
            font-size: 14px !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>ShopRent Admin</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="/admin" class="active"><span class="icon">📊</span>Dashboard</a></li>
                <li><a href="{{ route('admin.users.create') }}"><span class="icon">👤</span>Create User</a></li>
                <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><span class="icon">👥</span>User Management</a></li>
                <li><a href="{{ route('admin.shops.create') }}"><span class="icon">🏪</span>Create Shop</a></li>
                <li><a href="{{ route('admin.bus_stands.create') }}"><span class="icon">🚌</span>Create Bus Stand</a></li>
                <li><a href="{{ route('admin.shop_management.index') }}"><span class="icon">💳</span>Payment Management</a></li>
                <li><a href="{{ route('admin.admins.create') }}"><span class="icon">👥</span>Admin Management</a></li>
                <li><a href="{{ route('admin.payments.index') }}"  class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"><span class="icon">💰</span>Payments</a></li>
                <li><a href="{{ route('admin.backups.index') }}" class="{{ request()->routeIs('admin.backups.*') ? 'active' : '' }}"><span class="icon">💾</span>Create Backup</a></li>
                <li><a href="{{ route('admin.dashboard') }}#permissions"><span class="icon">🔐</span>Permissions</a></li>
                <li><a href="{{ route('admin.audit.index') }}"><span class="icon">📋</span>Audit Logs</a></li>
                <li><a href="#reports"><span class="icon">📈</span>Reports</a></li>
                <li><a href="#settings"><span class="icon">⚙️</span>Settings</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            @php
                // ensure the current admin object is available regardless of which controller rendered the view
                if (!isset($admin) && session('admin_id')) {
                    $admin = \App\Models\Admin::find(session('admin_id'));
                }
            @endphp

            <div class="topbar">
                <div class="topbar-title">
                    <h1>Welcome to Admin Dashboard</h1>
                </div>
                <div class="topbar-right">
                    <a href="{{ route('admin.profile.show') }}" style="text-decoration:none;color:inherit;display:flex;align-items:center;gap:10px;">
                        <div class="admin-info">
                            @if($admin)
                                <div class="admin-avatar">{{ strtoupper(substr($admin->name ?? $admin->username, 0, 1)) }}</div>
                                <div>
                                    <strong>{{ $admin->name ?? $admin->username }}</strong><br>
                                    <small>{{ $admin->email }}</small>
                                </div>
                            @endif
                        </div>
                    </a>
                    <a href="{{ url('/admin/logout') }}" class="logout-btn">Logout</a>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                @if(session('success'))
                    <div style="margin-bottom:20px; padding:15px; background:#d1fae5; border:1px solid #10b981; color:#065f46; border-radius:10px;">
                        {{ session('success') }}
                    </div>
                @endif
                @yield('content')

                {{-- default dashboard when no child content supplied --}}
                @hasSection('content')
                    {{-- child view displayed here --}}
                @else
                    <!-- Statistics -->
                    <div class="dashboard-grid">
                        <div class="stat-card blue">
                            <div class="stat-value">{{ $totalShops ?? 0 }}</div>
                            <div class="stat-label">Total shops</div>
                        </div>
                        <div class="stat-card green">
                            <div class="stat-value">{{ $activeUsers ?? 0 }}</div>
                            <div class="stat-label">Active Users</div>
                        </div>
                        <div class="stat-card purple">
                            <div class="stat-value">{{ $pendingAgreements ?? 0 }}</div>
                            <div class="stat-label">Registered Without Agreement</div>
                        </div>
                        <div class="stat-card orange light">
                            <div class="stat-value">{{ number_format($totalRevenueYear, 2) }}</div>
                            <div class="stat-label">Revenue this year</div>
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="features-section">
                        <h2>Quick Access</h2>
                        <div class="features-grid">
                            <div class="feature-card">
                                <div class="feature-icon">�</div>
                                <h3>Create User</h3>
                                <p>Add new system users with different roles (Manager, Staff, Cashier)</p>
                                <a href="{{ route('admin.users.create') }}">Create User →</a>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">🏪</div>
                                <h3>Create Shop</h3>
                                <p>Register new shops with owner details and agreement information</p>
                                <a href="{{ route('admin.shops.create') }}">Create Shop →</a>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">�</div>
                                <h3>Create Bus Stand</h3>
                                <p>Add new bus stands to the system for shop location management</p>
                                <a href="{{ route('admin.bus_stands.create') }}">Create Bus Stand →</a>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">�👥</div>
                                <h3>Admin Management</h3>
                                <p>Create, edit, and manage admin users with role-based permissions</p>
                                <a href="{{ route('admin.admins.create') }}">Manage →</a>
                            </div>
                            <div class="feature-card">
                                <h3>Payment Management</h3>
                                <p>Track and manage all payment transactions and history</p>
                                <a href="{{ route('admin.payments.index') }}">View →</a>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">🔐</div>
                                <h3>Permissions</h3>
                                <p>Set and manage user permissions and access control</p>
                                <a href="{{ route('admin.dashboard') }}#permissions">Configure →</a>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">📋</div>
                                <h3>Audit Logs</h3>
                                <p>View system audit logs and user activity history</p>
                                <a href="{{ route('admin.audit.index') }}">View →</a>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions Section -->
                    <div id="permissions-section" style="display: none;">
                        <h2>Permissions Management</h2>
                        <p>Configure permissions for different user roles.</p>
                        <div style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 16px; align-items: center;">
                            <div style="background: white; padding: 18px 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <strong>Historical Edit Mode</strong>
                                <p style="margin: 8px 0 0; color: #475569;">
                                    Current status: <span style="font-weight: 700; color: {{ $paymentEditModeEnabled ? '#16a34a' : '#dc2626' }};">{{ $paymentEditModeEnabled ? 'Enabled' : 'Disabled' }}</span>
                                </p>
                            </div>
                            <form method="POST" action="{{ route('admin.settings.payment_edit_mode') }}" style="background: white; padding: 18px 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="payment_edit_mode_enabled" value="{{ $paymentEditModeEnabled ? 0 : 1 }}">
                                <button type="submit" class="btn btn-primary" style="background: #2563eb; color: white; border-radius: 8px; padding: 10px 18px; border: none;">
                                    {{ $paymentEditModeEnabled ? 'Disable' : 'Enable' }} Historical Edit Mode
                                </button>
                            </form>
                        </div>
                        <div class="permissions-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                            <div class="permission-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <h3>Manager Permissions</h3>
                                <ul>
                                    <li>✓ View payments</li>
                                    <li>✓ Record payments</li>
                                    <li>✓ View shop details</li>
                                    <li>✗ Enable Edit Mode (historical data)</li>
                                </ul>
                            </div>
                            
                            <div class="permission-card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <h3>Admin Permissions</h3>
                                <ul>
                                    <li>✓ All user permissions</li>
                                    <li>✓ Admin panel access</li>
                                    <li>✓ User management</li>
                                    <li>✓ Shop management</li>
                                    <li>✓ Payment management</li>
                                    <li>✓ Audit logs</li>
                                    <!-- Hidden: Enable Edit Mode is available by default for admins -->
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
<script>
    function showPermissionsSection() {
        const featuresSection = document.querySelector('.features-section');
        const permissionsSection = document.getElementById('permissions-section');
        if (featuresSection) {
            featuresSection.style.display = 'none';
        }
        if (permissionsSection) {
            permissionsSection.style.display = 'block';
        }
    }

    // Show permissions section if URL has #permissions
    if (window.location.hash === '#permissions') {
        showPermissionsSection();
    }

    window.addEventListener('hashchange', () => {
        if (window.location.hash === '#permissions') {
            showPermissionsSection();
        }
    });

    // Handle menu clicks
    document.querySelectorAll('.sidebar-menu a[href$="#permissions"], a[href$="#permissions"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.features-section').style.display = 'none';
            document.getElementById('permissions-section').style.display = 'block';
            window.location.hash = '#permissions';
        });
    });
</script>
</html>
