<?php
//require_once app_path('Models/Channel.php');
//use App\Models\Channel;
//$activeNav = 'issue';

?>

@extends('layouts.channels')

@section('title', 'Issue')

@section('sub-content')
<div class="page-container">
    <aside class="sidebar3">
        <div class="header-bar header-bar-menu-pad2">
            <div class="flex-grow-1 text-truncate">{{ $issue->title ?? 'NAME MISSING' }}</div>
            <div>{{ $issue->date ?? 'DATE MISSING' }}</div>
        </div>
        <div id="main-scrollable-content" class="sidebar3-content">
            <div data-spy="section" class="issue_mast" id="issue_mast" style="<?php if($issue->image != null) { ?>background-image:url('/images/<?php echo $issue->image; ?>');<?php } ?>">
                <div class="container-fluid">
                    <h1>{{ $issue->title ?? 'NAME MISSING' }}</h1>
                    <span class="date">{{ $issue->date ?? 'DATE MISSING' }}</span>
                </div>
            </div>
            <div class="container">
                <?php foreach($articles as $article) { ?>
                    <?php if($article['article']->hidenav) continue; ?>
                    <div class="article-title-with-permalink">
                        <a href="/article/{{ $article['type'] }}/{{ $article['code']; }}" class="article-permalink btn btn-primary" aria-label="Permalink">
                            <span class="svg-icon">{!! File::get(resource_path('svg/logo_target.svg')) !!}</span>
                        </a>
                        <div>
                            <h4><a href="#{{ $article['type'] }}/{{ $article['code'] }}">{{ $article['article']->title }}</a></h4>
                            @if (count($article['article']->authors) > 0)
                                by:
                                @php
                                    if($article['article']->authors) {
                                        $comma = false;
                                        foreach($article['article']->authors as $author) {
                                            if($comma) echo ', ';
                                            echo $author['name'];
                                            $comma = true;
                                        }
                                    }
                                @endphp
                            @endif
                        </div>
                    </div>
                <?php } ?>
            </div>
            <hr>
            @foreach($articles as $article)
                <div data-spy="section" class="container" id="{{ $article['type'] }}/{{ $article['code'] }}">
                    @php
                        $content = $article['content'];
                    @endphp
                    @include('partials.article', ['article' => $article['article'], 'type' => $article['type'], 'code' => $article['code'], 'content' => $content])
                </div>
                <hr style="clear:both;padding-top:1rem;">
            @endforeach
        </div>
    </aside>
</div>
<script>
    let lastId = '';
    let content = document.getElementById('main-scrollable-content');
    content.addEventListener('scroll', function() {
        let sections = content.querySelectorAll('[data-spy="section"]');
        let navLinks = document.querySelectorAll('.channel-item');
        let scrollPos = content.scrollY || content.scrollTop;

        let currentId = '';
        sections.forEach(section => {
            if (section.offsetTop <= scrollPos + 100) { // 100px offset for header
                currentId = section.id;
            }
        });

        navLinks.forEach(link => {
            link.classList.toggle('spy', link.getAttribute('href') === window.location.pathname + '#' + currentId);
        });

        // Update URL hash without navigation
        if (currentId && currentId !== lastId) {
            history.replaceState(null, '', '#' + currentId);
            lastId = currentId;
        }
    });
</script>
@endsection
