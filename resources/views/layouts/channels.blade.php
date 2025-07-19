{{-- filepath: resources/views/hello.blade.php --}}
@php($activeNav = 'issue')
<?php
    $sidebarWidth = null;
    if (isset($_COOKIE['sidebar2_width'])) {
        $w = intval($_COOKIE['sidebar2_width']);
        // Only use reasonable values to avoid breaking the layout
        if ($w >= 100 && $w <= 1000) {
            $sidebarWidth = $w;
        }
    }
?>

@extends('layouts.app')

@section('title', 'Issue')

@section('content')
<style>
    .sidebar2 {
        display: flex;
        position: relative;
        flex-direction: column;
        width: 250px; /* default */
        height: 100vh;
        z-index: 2;
        /*overflow: hidden;*/

        transition: width 0.1s;
        will-change: min-width, width;
    }
    .sidebar2-content {
        flex: 1;
        overflow: auto;
    }
    .header-bar-menu-pad {
        padding-left: 42px;
    }
    .page-container {
        width: 100%;
        height: 100%;
        inset: 0;
        display: flex;
        z-index: 0;
    }
    .sub-content {
        display: flex;
        flex: 1;
        overflow: auto;
        inset:0;
        transition: margin-left 0.3s;
        will-change: margin-left;
        z-index: 1;
    }
    .resizer {
        width: 4px;
        top: 0;
        right: -2px;
        height: 100%;
        z-index: 10;
        cursor: ew-resize;
        background: rgba(0,0,0,0);
        position: absolute;
        user-select: none;

        transition: background 0.3s;
    }
    .resizer:hover,
    .resizer:active {
        background: rgba(255,255,255,1);
    }
    .sidebar-hidden .sidebar2 {
        width: 0 !important;
        overflow-x: clip;
    }


.sidebar2-content {
    overflow-y: scroll; /* Always reserve scrollbar space */
}
.sidebar2-content::-webkit-scrollbar {
    width: 8px;
    background: transparent;
}
.sidebar2-content::-webkit-scrollbar-thumb {
    background: transparent;
}
.sidebar2-content:hover::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
    border: 2px solid transparent; /* Simulates margin around the thumb */
    background-clip: padding-box;  /* Ensures background doesn't cover the border */
}


</style>
<div class="page-container">
    <aside class="sidebar2" id="sidebar2" @if(isset($sidebarWidth)) style="width: {{ $sidebarWidth }}px;" @endif>
        <div class="header-bar header-bar-menu-pad">
            Header Bar
        </div>
        <div class="sidebar2-content">
            <!-- Example HTML for a Discord-like channel list -->
            <div class="channel-list">
                @foreach($channels as $channel)
                    <div class="channel-section">
                        @include('partials.channel-list', ['channel' => $channel])
                    </div>
                @endforeach
            </div>
        </div>
        <div class="resizer" id="resizer"></div>
    </aside>
    <div class="sub-content">
        @yield('sub-content')
    </div>
</div>
<script>
    // Restore sidebar width from cookie
    function getCookie(name) {
        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        if (match) return match[2];
    }
    function setCookie(name, value, days = 365) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    const sidebar = document.getElementById('sidebar2');
    const resizer = document.getElementById('resizer');
    let startX, startWidth;

    // Restore width
    const savedWidth = getCookie('sidebar2_width');
    if (savedWidth) sidebar.style.width = savedWidth + 'px';

    resizer.addEventListener('mousedown', function(e) {
        startX = e.clientX;
        startWidth = parseInt(document.defaultView.getComputedStyle(sidebar).width, 10);
        document.documentElement.addEventListener('mousemove', doDrag, false);
        document.documentElement.addEventListener('mouseup', stopDrag, false);
    });

    function doDrag(e) {
        let newWidth = startWidth + e.clientX - startX;
        newWidth = Math.max(250, Math.min(newWidth, window.innerWidth * 0.8));
        sidebar.style.width = newWidth + 'px';
    }

    function stopDrag(e) {
        setCookie('sidebar2_width', parseInt(sidebar.style.width, 10));
        document.documentElement.removeEventListener('mousemove', doDrag, false);
        document.documentElement.removeEventListener('mouseup', stopDrag, false);
    }
</script>
@endsection
