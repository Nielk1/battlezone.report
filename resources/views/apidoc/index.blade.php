{{-- filepath: resources/views/apidoc/index.blade.php --}}
@extends(request()->query('ajax') == 2 ? 'layouts.ajax-subcontent' : 'layouts.channels')

@section('title', 'Battlezone Field Report - API Reference')

@section('sub-content')
<div class="page-container">
    <div class="sidebar3">
        <div class="header-bar header-bar-menu-pad2">
            <div class="flex-grow-1 text-truncate">{{ $issue->title ?? 'NAME MISSING' }}</div>
            <div>{{ $issue->date ?? 'DATE MISSING' }}</div>
        </div>
        <div id="main-scrollable-content" class="sidebar3-content">
            <div class="container">
                @foreach($content as $item)
                    <div class="content-item" data-spy="section" id="{{ $item['code'] }}">
                        <h4>Module: {{ $item['name'] }}</h4>
                        <pre>{{ $item['desc'] }}</pre>
                    </div>
                    {{-- Recursive dive the children, where they will still be rendered flat but the data matches the channel tree --}}
                    @if(isset($item['children']) && count($item['children']) > 0)
                        <div class="content-children">
                            @foreach($item['children'] as $child)
                                <div class="content-item" data-spy="section" id="{{ $child['code'] }}">
                                    <h5>Section: {{ $child['name'] }}</h5>
                                    <pre>{{ $child['desc'] }}</pre>
                                    @if(isset($child['children']) && count($child['children']) > 0)
                                        <div class="content-children">
                                            @foreach($child['children'] as $subchild)
                                                <div class="content-item" data-spy="section" id="{{ $subchild['code'] }}">
                                                    <h6>Item: {{ $subchild['name'] }}</h6>
                                                    <pre>{{ $subchild['desc'] }}</pre>
                                                    @if(isset($subchild['view']))
                                                        <pre>{{ $subchild['view'] }}</pre>
                                                    @endif
                                                    @if(isset($subchild['children']) && count($subchild['children']) > 0)
                                                        <div class="content-children">
                                                            @foreach($subchild['children'] as $subsubchild)
                                                                <div class="content-item" data-spy="section" id="{{ $subsubchild['code'] }}">
                                                                    <h6>Subitem: {{ $subsubchild['name'] }}</h6>
                                                                    <pre>{{ $subsubchild['desc'] }}</pre>
                                                                    @if(isset($subsubchild['view']))
                                                                        <pre>{{ $subsubchild['view'] }}</pre>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <hr>
                @endforeach
            </div>
            {{--<div data-spy="section" class="issue_mast" id="top" style="<?php if($issue->image != null) { ?>background-image:url('/images/<?php echo $issue->image; ?>');<?php } ?>">
                <div class="container-fluid">
                    <h1>{{ $issue->title ?? 'NAME MISSING' }}</h1>
                    <span class="date">{{ $issue->date ?? 'DATE MISSING' }}</span>
                </div>
            </div>
            <div class="container">
                <?php foreach($articles as $article) { ?>
                    <?php if($article['article']->hidenav) continue; ?>
                    <div class="article-title-with-permalink">
                        <a href="/article/{{ $article['type'] }}/{{ $article['code']; }}" data-ajaxnav="1" class="article-permalink btn btn-primary" aria-label="Permalink" title="Permalink">
                            <span class="svg-icon">{!! File::get(resource_path('svg/glyph/tablericons/target.svg')) !!}</span>
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
            @endforeach--}}
        </div>
    </div>
</div>
@endsection
