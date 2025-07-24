{{-- filepath: resources/views/hello.blade.php --}}

@extends(request()->query('ajax') ? 'layouts.ajax' : 'layouts.app')

{{-- @section('title', 'Issue') --}}
@section('title', 'Battlezone Field Report - Article - ' . ($article->title ?? "No Title"))

@section('content')

<div class="page-container">
    <aside class="sidebar3">
        <div class="header-bar header-bar-menu-pad">
            <div class="flex-grow-1 text-truncate">{{ $article->title ?? 'NAME MISSING' }}</div>
        </div>
        <div class="sidebar3-content">
            <div class="container">
                @include('partials.article', ['article' => $article, 'type' => $type, 'code' => $code, 'content' => $content])
            </div>
        </div>
    </aside>
</div>

@endsection
