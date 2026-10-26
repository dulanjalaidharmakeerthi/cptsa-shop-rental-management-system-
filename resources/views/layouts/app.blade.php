<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
    <style>
        /* Small helper to ensure the main layout takes full height */
        html, body, #app { height: 100%; }
    </style>
    @stack('head')
</head>
<body>
    <div id="app" class="dashboard-layout">
        @include('layouts.partials.navbar')

        <div class="dashboard-body">
            @unless(View::hasSection('hideSidebar'))
                @include('layouts.partials.sidebar')
            @endunless

            <main class="dashboard-content" role="main">
                <div class="container">
                    @yield('content')
                </div>
            </main>
        </div>

        @include('layouts.partials.footer')
</div>

    <script>
        // Sidebar and profile toggles for small screens
        (function(){
            var btn = document.querySelector('.nav-toggle');
            var sidebar = document.getElementById('sidebar');
            if (btn && sidebar) {
                btn.addEventListener('click', function(){
                    sidebar.classList.toggle('open');
                });
            }

            var pBtn = document.querySelector('.profile-btn');
            var pMenu = document.querySelector('.profile-menu');
            if (pBtn && pMenu) {
                pBtn.addEventListener('click', function(e){
                    e.stopPropagation();
                    var open = pMenu.classList.toggle('open');
                    pBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                    pMenu.setAttribute('aria-hidden', open ? 'false' : 'true');
                });

                // close on outside click
                document.addEventListener('click', function(){
                    pMenu.classList.remove('open');
                    pBtn.setAttribute('aria-expanded','false');
                    pMenu.setAttribute('aria-hidden','true');
                });

                // prevent menu clicks from closing
                pMenu.addEventListener('click', function(e){ e.stopPropagation(); });
            }
        })();
    </script>

    @stack('scripts')
</body>
</html>
