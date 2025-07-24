{{-- filepath: resources/views/hello.blade.php --}}
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

@section('title', 'Battlezone Field Report')

@section('content')
<div class="page-container">
    <aside class="sidebar2" id="sidebar2" @if(isset($sidebarWidth)) style="width: {{ $sidebarWidth }}px;" @endif>
        <div class="header-bar header-bar-menu-pad">{{ $channels_header ?? 'Channels' }}</div>
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

    document.querySelectorAll('.channel-action').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
        });
    });
</script>
@endsection
