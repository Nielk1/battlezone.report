{{-- filepath: resources/views/hello.blade.php --}}
@php($activeNav = 'social')
@extends(request()->query('ajax') ? 'layouts.ajax' : 'layouts.app')

@section('title', 'Battlezone Field Report - Social')

@section('content')
<div class="page-container">
    <div class="sidebar3">
        <div class="header-bar header-bar-menu-pad">
            <div class="flex-grow-1 text-truncate">Social</div>
        </div>
        <div class="sidebar3-content">
            <div class="container my-3">
                <div class="row">
                    <div class="col-12 col-md-7">
                        <p>Lobby-sitting getting you down? Keeping the IRC open starting to get on your nerves? Don't worry - we've got just the thing! Come on down to the Battlezone Discord channel instead; keep track of both lobbies, keep an eye on the news and an ear to the ground, and get help for all your mapping and modding woes... or just talk rubbish until you're blue in the face, we don't judge.</p>
                        <p>
                            <a href="https://discord.battlezone.report" target="_blank" class="btn btn-primary" aria-label="Join"><i class="bi bi-discord"></i> Join Community Discord</a>
                        </p>
                        <img style="margin:auto;width:500px;" class="d-none d-print-block" src="/images/discord-qr.png" alt="DiscordQR"/>
                    </div>
                    <div class="col-12 d-block d-md-none d-print-none"><hr/></div>
                    <div class="col-12 col-md-5 d-print-none">
                        <iframe src="https://discordapp.com/widget?id=271066904284758027&theme=dark" allowtransparency="true" frameborder="0" class="rounded" style="display: block; width:100%; height:max(500px,calc(100vh - 42px - 2rem));"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
