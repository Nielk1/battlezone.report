<?php
//require_once app_path('Models/Channel.php');
//use App\Models\Channel;
//$activeNav = 'issue';

?>

@extends(request()->query('ajax') == 2 ? 'layouts.ajax-subcontent' : 'layouts.channels')

@section('title', 'Battlezone Field Report - Chronicle - ' . ($issue->title ?? "No Volume") . ' - ' . ($article->title ?? "No Title"))

@section('sub-content')
<div class="page-container">
    <div class="sidebar3">
        <div class="header-bar header-bar-menu-pad2">
            <div class="flex-grow-1 text-truncate">{{ $issue->title ?? 'NAME MISSING' }}</div>
        </div>
        <div id="main-scrollable-content" class="sidebar3-content">
            <div data-spy="section" class="issue_mast" id="top" style="<?php if($issue->image != null) { ?>background-image:url('/images/<?php echo $issue->image; ?>');<?php } ?>">
                <div class="container-fluid">
                    <h1>{{ $issue->title ?? 'NAME MISSING' }}</h1>
                </div>
            </div>
            <div data-spy="section" class="container">
                @include('partials.chapter', ['article' => $article, 'content' => $content])
            </div>
        </div>
    </div>
</div>
@endsection
