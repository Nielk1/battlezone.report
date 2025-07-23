<!DOCTYPE html>
<html data-bs-theme="dark">
<head>
    <title>@yield('title', 'My App')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="sidebar-layout{{ request()->query('sbh') ? ' sidebar-hidden' : '' }}" id="main-layout">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-toggle" onclick="toggleSidebar()">☰</div>
        <div class="sidebar-scroll">
            <div class="sidebar-top" data-cluster-id="custom">
                <a href="{{ route('home') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === 'home' ? ' active' : '' }}"><div class="sidebar-icon">BZ</div></a>
                <!--<a href="{{ route('home') }}" class="sidebar-icon-box{{ request()->routeIs('home') ? ' active' : '' }}"><div class="sidebar-icon">BZ</div></a>-->
                <hr class="border border-primary border-2">
                <a href="{{ route('issue') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === 'issue' ? ' active' : '' }}"><div class="sidebar-icon">ISU</div></a>
                <a href="{{ route('chronicle') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === 'chronicle' ? ' active' : '' }}"><div class="sidebar-icon">CRN</div></a>
                <a href="{{ route('games_bz98r') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === 'games_bz98r' ? ' active' : '' }}"><div class="sidebar-icon">98R</div></a>
                <a href="{{ route('home') }}" class="sidebar-icon-box{{ ($activeNav ?? '') === 'cc' ? ' active' : '' }}"><div class="sidebar-icon">CC</div></a>
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
    <main class="main-content">
        @yield('content')
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

        function toggleSidebar() {
            const layout = document.getElementById('main-layout');
            const isHidden = layout.classList.toggle('sidebar-hidden');
            if (isHidden) {
                setQueryParam('sbh', '1');
            } else {
                removeQueryParam('sbh');
            }
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
