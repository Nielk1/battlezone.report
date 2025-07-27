{{-- filepath: resources/views/hello.blade.php --}}
@php($activeNav = 'about')
@extends(request()->query('ajax') ? 'layouts.ajax' : 'layouts.app')

@section('title', 'Battlezone Field Report - About')

@section('content')
<div class="page-container">
    <div class="sidebar3">
        <div class="header-bar header-bar-menu-pad">
            <div class="flex-grow-1 text-truncate">About</div>
        </div>
        <div class="sidebar3-content">
            <div class="container my-3">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="page-header">
                            <h2>Team</h2>
                            <hr/>
                        </div>
                    </div>
                    @foreach($team as $member)
                        <div class="row mb-3 col-lg-6">
                            <div class="col-auto">
                                <img src="/images/team/{{ $member->image }}" alt="{{ $member->name }}" class="rounded interview-image">
                            </div>
                            <div class="col">
                                <h4 class="mb-1">{{ $member->name }}<br>
                                    <small class="text-muted">{{ $member->position }}</small>
                                </h4>
                                <div class="d-flex align-items-center social-buttons">
                                    @foreach ($member->social as $platform => $username)
                                        @switch($platform)
                                            @case('twitter')
                                                <a href="https://twitter.com/{{ $username }}" target="_blank" class="btn btn-twitter" title="Twitter"><i class="bi bi-twitter-x"></i></a>
                                                @break
                                            @case('mobygames')
                                                <a href="https://www.mobygames.com/person/{{ $username }}" target="_blank" class="btn btn-w2 btn-mobygames" title="MobyGames"><img src="/images/icons/brand_mobygames.svg" alt="MobyGames"></a>
                                                @break
                                            @default
                                        @endswitch
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
