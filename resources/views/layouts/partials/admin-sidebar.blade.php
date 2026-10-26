<div class="sidebar">
    <div class="sidebar-header">
        <h2>ShopRent Admin</h2>
    </div>
    <ul class="sidebar-menu">
        <li><a href="/admin" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span class="icon">📊</span>Dashboard</a></li>
        <li><a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><span class="icon">👥</span>Users</a></li>
        <li><a href="{{ route('admin.users.create') }}" class="nav-link {{ request()->routeIs('admin.users.create') ? 'active' : '' }}"><span class="icon">👤</span>Create User</a></li>
        <li><a href="{{ route('admin.shops.create') }}" class="nav-link {{ request()->routeIs('admin.shops.*') ? 'active' : '' }}"><span class="icon">🏪</span>Create Shop</a></li>
        <li><a href="{{ route('admin.shops.manage_edit') }}" class="nav-link"><span class="icon">🔧</span>Manage Shops</a></li>
        <li><a href="{{ route('admin.shop_management.index') }}" class="nav-link {{ request()->routeIs('admin.shop_management.*') ? 'active' : '' }}"><span class="icon">💳</span>Payment Management</a></li>
        <li><a href="{{ route('admin.admins.create') }}" class="nav-link"><span class="icon">👥</span>Manage Admins</a></li>
        <li><a href="{{ route('admin.payments.index') }}" class="nav-link"><span class="icon">💳</span>Payments</a></li>
        <li><a href="{{ route('admin.dashboard') }}#permissions" class="nav-link"><span class="icon">🔐</span>Permissions</a></li>
        <li><a href="{{ route('admin.audit.index') }}" class="nav-link"><span class="icon">📋</span>Audit Logs</a></li>
        <li><a href="#reports" class="nav-link"><span class="icon">📈</span>Reports</a></li>
        <li><a href="{{ route('admin.profile.show') }}" class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"><span class="icon">👤</span>Profile</a></li>
        <li><a href="#settings" class="nav-link"><span class="icon">⚙️</span>Settings</a></li>
    </ul>
</div>
