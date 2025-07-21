<?php
//require_once app_path('Models/Channel.php');
//use App\Models\Channel;
$activeNav = 'issue';

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
                <div class="row">
                    <div class="col-xs-12">
                        <a id="toc"></a>
                        <?php foreach($articles as $article) { ?>
                            <div class="media">
                                <span class="float-start tocLogo">
                                    <span class="glyphicon glyphicon-record"></span>
                                </span>
                                <div class="media-body">
                                    <h4 class="media-heading"><a href="#<?php echo $article['type']; ?>/<?php echo $article['code']; ?>"><?php echo $article['article']->title; ?></a></h4>
                                    <?php if (count($article['article']->authors) > 0) { ?>
                                        by:
                                    <?php } ?>
                                    <?php
                                    {
                                        if($article['article']->authors) {
                                            $comma = false;
                                            foreach($article['article']->authors as $author) {
                                                if($comma) echo ', ';
                                                echo $author['name'];
                                                $comma = true;
                                            }
                                        }
                                    } ?>
                                    <em><a class="ajaxLoaderAware" href="/article/<?php echo $article['type']; ?>/<?php echo $article['code']; ?>">(permalink)</a></em>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <hr>
            @foreach($articles as $article)
                <div data-spy="section" class="container" id="{{ $article['type'] }}/{{ $article['code'] }}">
                    @php
                        $content = $article['content'];
                    @endphp
                    @include('partials.article', ['article' => $article['article'], 'type' => $article['type'], 'code' => $article['code'], 'content' => $content])
                </div>
                <hr>
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
