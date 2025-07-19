<?php
//require_once app_path('Models/Channel.php');
//use App\Models\Channel;
$activeNav = 'issue';

/*$channels = [
    Channel::fromArray([
        'name' => 'Issue 2016-04-25',
        'type' => 'issue',
        'children' => [
            [
                'name' => 'general',
                'icon' => 'logo_article',
                'type' => 'article',
                'action' => 'issue/2016-04-25#article1',
                'buttons' => ['permalink'],
            ],
            [
                'name' => 'random',
                'icon' => 'logo_steam',
                'type' => 'article',
                'action' => 'issue/2016-04-25#article2',
                'buttons' => ['permalink'],
                'children' => [
                    [
                        'name' => 'general',
                        'icon' => 'logo_steam',
                        'type' => 'article',
                        'action' => 'issue/2017-02-09#article1',
                        'buttons' => ['permalink'],
                    ],
                    [
                        'name' => 'random',
                        'icon' => 'logo_steam',
                        'type' => 'article',
                        'action' => 'issue/2017-02-09#article2',
                        'buttons' => ['permalink'],
                    ]
                ]
            ]
        ]
    ]),
    Channel::fromArray([
        'name' => 'Issue 2017-02-09',
        'type' => 'issue',
        'icon' => 'logo_steam',
        'children' => [
            [
                'name' => 'general',
                'icon' => 'logo_steam',
                'type' => 'article',
                'action' => 'issue/2017-02-09#article1',
                'buttons' => ['permalink'],
            ],
            [
                'name' => 'random',
                'type' => 'article',
                'action' => 'issue/2017-02-09#article2',
                'buttons' => ['permalink'],
                'children' => [
                    [
                        'name' => 'general',
                        'icon' => 'logo_steam',
                        'type' => 'article',
                        'action' => 'issue/2017-02-09#article1',
                        'buttons' => ['permalink'],
                    ],
                    [
                        'name' => 'random',
                        'icon' => 'logo_steam',
                        'type' => 'article',
                        'action' => 'issue/2017-02-09#article2',
                        'buttons' => ['permalink'],
                    ]
                ]
            ]
        ]
    ]),
];*/
?>

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
