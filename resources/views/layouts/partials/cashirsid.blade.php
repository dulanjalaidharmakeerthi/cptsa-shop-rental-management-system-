<aside class="dashboard-sidebar" id="sidebar">
    <div class="sidebar-inner">
        @auth
            <div class="sidebar-profile" style="padding:14px; border-bottom:1px solid rgba(0,0,0,0.04);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:48px; height:48px; border-radius:50%; background:#e6eef6; display:flex; align-items:center; justify-content:center; font-weight:700; color:#1f4e79">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                    <div style="flex:1;">
                        <div style="font-weight:600">{{ auth()->user()->name }}</div>
                        <div style="font-size:13px; color:#6b7280">{{ auth()->user()->position }}</div>
                        @if(auth()->user()->phone)
                            <div style="font-size:12px; color:#9ca3af; margin-top:4px">{{ auth()->user()->phone }}</div>
                        @endif
                    </div>
                </div>
            </div>
        @endauth
        @guest
            <div class="sidebar-profile" style="padding:14px; border-bottom:1px solid rgba(0,0,0,0.04);">
                <a href="{{ route('login') }}" class="btn">Sign in</a>
            </div>
        @endguest

        <nav class="sidebar-nav">
            <ul>
                <br>
            <br>

                <li><a href="/cashierdashboard">Overview</a></li>
                         <br>

                <li><a href="{{ route('payments.index') }}">Payments</a></li>
                            <br>

                <li><a href="#">Reports</a></li>
                            <br>

                <li><a href="#">Settings</a></li>
                            <br>
            <br>

                <li>
                    <button id="siteLangToggle" class="btn" style="padding:6px 10px; border-radius:6px">සිං</button>
                </li>
            </ul>
        </nav>
    </div>
</aside>
