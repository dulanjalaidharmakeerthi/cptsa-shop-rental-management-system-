<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
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
            @include('layouts.partials.cashirsid')

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

        // Site language toggle (sidebar button)
        (function(){
            function updateSiteLangButton(){
                var btn = document.getElementById('siteLangToggle');
                if (!btn) return;
                var lang = localStorage.getItem('lang') || 'en';
                btn.textContent = (lang === 'en') ? 'සිං' : 'EN';
            }
            document.addEventListener('DOMContentLoaded', updateSiteLangButton);
            document.addEventListener('click', function(e){
                var btn = e.target.closest('#siteLangToggle');
                if (!btn) return;
                var lang = localStorage.getItem('lang') || 'en';
                var newLang = (lang === 'en') ? 'si' : 'en';
                localStorage.setItem('lang', newLang);
                location.reload();
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
