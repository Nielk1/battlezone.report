<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <title>@yield('title', 'Battlezone Field Report')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/bf75e31b39.js" crossorigin="anonymous"></script>
</head>
<body class="sidebar-layout{{ request()->query('sbh') ? ' sidebar-hidden' : '' }}" id="main-layout">
    <aside class="sidebar" id="sidebar">
        <div id="sidebar-toggle" class="corner-button">☰</div>
        <div class="sidebar-scroll">
            <div class="sidebar-top" data-cluster-id="custom">
                <a data-nav="home"      data-ajaxnav="true" href="{{ route('home') }}"      class="sidebar-icon-box{{ ($activeNav ?? '') === 'home'      ? ' active' : '' }}"><div class="sidebar-icon">{!! File::get(resource_path('svg/logo_battlezone.svg')) !!}</div></a>
                <hr class="border border-primary border-2">
                <a data-nav="issue"     data-ajaxnav="true" href="{{ route('issue') }}"     class="sidebar-icon-box{{ ($activeNav ?? '') === 'issue'     ? ' active' : '' }}"><div class="sidebar-icon"><i class="bi bi-newspaper"></i></div></a>
                <a data-nav="chronicle" data-ajaxnav="true" href="{{ route('chronicle') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === 'chronicle' ? ' active' : '' }}"><div class="sidebar-icon"><i class="bi bi-feather"></i></div></a>
                <a data-nav="games"     data-ajaxnav="true" href="{{ route('games') }}"     class="sidebar-icon-box{{ ($activeNav ?? '') === 'games'     ? ' active' : '' }}"><div class="sidebar-icon"><i class="bi bi-controller"></i></div></a>
                <a data-nav="modding"   data-ajaxnav="true" href="{{ route('home') }}"      class="sidebar-icon-box{{ ($activeNav ?? '') === 'modding'   ? ' active' : '' }}"><div class="sidebar-icon"><i class="bi bi-tools"></i></div></a>
                {{--<a data-nav="archive"   data-ajaxnav="true" href="{{ route('home') }}"      class="sidebar-icon-box{{ ($activeNav ?? '') === 'archive'   ? ' active' : '' }}"><div class="sidebar-icon"><i class="bi bi-archive-fill"></i></div></a>--}}
                <a data-nav="social"    data-ajaxnav="true" href="{{ route('social') }}"      class="sidebar-icon-box{{ ($activeNav ?? '') === 'social'    ? ' active' : '' }}"><div class="sidebar-icon"><i class="bi bi-chat-fill"></i></div></a>
            </div>
            {{--
            <div class="sidebar-top">
                <a href="{{ route('home') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === 'home' ? ' active' : '' }}"><div class="sidebar-icon">BZ</div></a>
                <!--<a href="{{ route('home') }}" class="sidebar-icon-box{{ request()->routeIs('home') ? ' active' : '' }}"><div class="sidebar-icon">BZ</div></a>-->
                <hr class="border border-primary border-2">
                <a href="{{ route('issue') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === 'issue' ? ' active' : '' }}"><div class="sidebar-icon">Isu</div></a>
                <a href="{{ route('home') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === 'crn' ? ' active' : '' }}"><div class="sidebar-icon">Crn</div></a>
                <a href="{{ route('home') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === '98r' ? ' active' : '' }}"><div class="sidebar-icon">98R</div></a>
                <a href="{{ route('home') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === 'cc' ? ' active' : '' }}"><div class="sidebar-icon">CC</div></a>
            </div>
            --}}
            <div class="sidebar-spacer"></div>
            <div class="sidebar-bottom">
                <hr class="border border-secondary border-2">
                <div class="sidebar-icon-box"><div class="sidebar-icon">★</div></div>
            </div>
            <div class="scroll-arrow up" id="scrollUp" onclick="scrollSidebar(-100)" style="display: none;">▲</div>
            <div class="scroll-arrow down" id="scrollDown" onclick="scrollSidebar(100)" style="display: none;">▼</div>
        </div>
    </aside>
    <main id="main-content">
        @yield('content')
        @include('partials.nav-data')
    </main>
    <script type="text/javascript">
        function setQueryParam(key, value) {
            const url = new URL(window.location);
            url.searchParams.set(key, value);
            window.history.replaceState({}, '', url);
        }
        function removeQueryParam(key) {
            const url = new URL(window.location);
            url.searchParams.delete(key);
            window.history.replaceState({}, '', url);
        }

        function scrollSidebar(amount) {
            const scrollArea = document.querySelector('.sidebar-scroll');
            scrollArea.scrollBy({ top: amount, behavior: 'smooth' });
        }

        function updateScrollArrows() {
            const scrollArea = document.querySelector('.sidebar-scroll');
            const upArrow = document.getElementById('scrollUp');
            const downArrow = document.getElementById('scrollDown');

            // At top
            if (scrollArea.scrollTop === 0) {
                upArrow.style.display = 'none';
            } else {
                upArrow.style.display = '';
            }

            // At bottom
            if (scrollArea.scrollHeight - scrollArea.clientHeight - scrollArea.scrollTop <= 1) {
                downArrow.style.display = 'none';
            } else {
                downArrow.style.display = '';
            }
        }

        //let lastDevicePixelRatio = window.devicePixelRatio;

        // Listen for scroll
        document.querySelector('.sidebar-scroll').addEventListener('scroll', updateScrollArrows);

        // Listen for zoom changes
        window.addEventListener('resize', () => {
            //if (window.devicePixelRatio !== lastDevicePixelRatio) {
            //    lastDevicePixelRatio = window.devicePixelRatio;
                updateScrollArrows();
            //}
        });

        // Initial check
        window.addEventListener('load', updateScrollArrows);
    </script>
</body>
</html>
