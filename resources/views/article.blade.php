{{-- filepath: resources/views/hello.blade.php --}}

@extends('layouts.app')

{{-- @section('title', 'Issue') --}}

@section('content')

<div class="page-container">
    <aside class="sidebar3">
        <div class="header-bar header-bar-menu-pad">
            Header Bar 2
        </div>
        <div class="sidebar3-content">
            <div class="container">
                @include('partials.article', ['article' => $article, 'type' => $type, 'code' => $code, 'content' => $content])
            </div>
        </div>
    </aside>
</div>

@endsection
