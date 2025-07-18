{{-- filepath: resources/views/hello.blade.php --}}
@php($activeNav = 'issue')
@extends('layouts.channels')

@section('title', 'Issue')

@section('sub-content')
<style>
    .header-bar-menu-pad2 {
        padding-left: 10px;
        transition: padding-left 0.3s;
        will-change: padding-left;
    }
    .sidebar3 { /*not actually a sidebar, please fix*/
        display: flex;
        flex-direction: column;
        width: 100%;
        height: 100vh;
        overflow: hidden;

        /*background-color: var(--bs-secondary-bg) !important;*/
        background-color: var(--bs-tertiary-bg) !important;
    }
    .sidebar3-content {
        flex: 1;
        overflow: auto;
    }
    .sidebar-hidden .header-bar-menu-pad2 {
        padding-left: 42px;
    }

.sidebar3-content {
    overflow-y: scroll; /* Always reserve scrollbar space */
}
.sidebar3-content::-webkit-scrollbar {
    width: 12px;
    background: transparent;
}
.sidebar3-content::-webkit-scrollbar-thumb {
    background: transparent;
}
.sidebar3-content:hover::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 6px;
    border: 2px solid transparent; /* Simulates margin around the thumb */
    background-clip: padding-box;  /* Ensures background doesn't cover the border */
}

.sidebar3-content {
    padding: 10px;
}
</style>
<div class="page-container">
    <aside class="sidebar3">
        <div class="header-bar header-bar-menu-pad2">
            Header Bar 2
        </div>
        <div class="sidebar3-content">
            <h1>Issue Page</h1>
            @for ($i = 0; $i < 50; $i++)
                <p>Welcome to the issue page!</p>
            @endfor
        </div>
    </aside>
</div>
@endsection
