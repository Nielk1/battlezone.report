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
                        <h2>Module: {{ $item['name'] }}</h2>
                        <div class="documentation-desc">
                            @foreach($item['desc'] as $desc)
                                <p>{{ $desc }}</p>
                            @endforeach
                        </div>
                        @if(isset($item['children']) && count($item['children']) > 0)
                            <div class="ps-3 border-5 border-start border-primary">
                                @foreach($item['children'] as $child)
                                    <div class="content-item" data-spy="section" id="{{ $child['code'] }}">
                                        <h3>Section: {{ $child['name'] }}</h3>
                                        <div class="documentation-desc">
                                            @foreach($child['desc'] as $desc)
                                                <p>{{ $desc }}</p>
                                            @endforeach
                                        </div>
                                        @if(isset($child['children']) && count($child['children']) > 0)
                                            <div class="ps-3 border-5 border-start border-primary">
                                                @foreach($child['children'] as $subchild)
                                                    <div class="content-item" data-spy="section" id="{{ $subchild['code'] }}">
                                                        <h4>Item: {{ $subchild['name'] }}</h4>
                                                        <div class="documentation-desc">
                                                            @foreach($subchild['desc'] as $desc)
                                                                <p>{{ $desc }}</p>
                                                            @endforeach
                                                        </div>
                                                        @if(isset($subchild['view']))
                                                            <pre>{{ $subchild['view'] }}</pre>
                                                        @endif
                                                        @if(isset($subchild['children']) && count($subchild['children']) > 0)
                                                            <div class="ps-3 border-5 border-start border-primary">
                                                                @foreach($subchild['children'] as $subsubchild)
                                                                    <div class="content-item" data-spy="section" id="{{ $subsubchild['code'] }}">
                                                                        <h5>Subitem: {{ $subsubchild['name'] }}</h5>
                                                                        @if(isset($subsubchild['view']))
                                                                            <pre>{{ $subsubchild['view'] }}</pre>
                                                                        @endif
                                                                        <div class="documentation-desc">
                                                                            @foreach($subsubchild['desc'] as $desc)
                                                                                <p>{{ $desc }}</p>
                                                                            @endforeach
                                                                        </div>
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
                    </div>
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
