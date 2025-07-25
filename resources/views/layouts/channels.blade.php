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

@extends(request()->query('ajax') ? 'layouts.ajax' : 'layouts.app')

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
    <div id="sub-content">
        @yield('sub-content')
    </div>
</div>
@endsection
