{{-- filepath: f:\Programming\BattlezoneReport\battlezone_report\resources\views\partials\article.blade.php --}}
<div>
    @if(!$article->hidepermlink || $article->title)
    <div class="article-title-with-permalink">
        @if(!$article->hidepermlink)
        <a href="/article/{{ $type }}/{{ $code }}" data-ajaxnav="1" class="article-permalink btn btn-primary" aria-label="Permalink" title="Permalink">
            <span class="svg-icon">{!! File::get(resource_path('svg/glyph/tablericons/target.svg')) !!}</span>
        </a>
        @endif
        <div>
            @if($article->title)
                <h1>{{ $article->title }}</h1>
            @endif
            @if(!$article->hidepermlink)
                <div class="d-print-block d-none">
                    <span class="article-permalink" title="Permalink">/article/{{ $type }}/{{ $code }}</span>
                </div>
            @endif
        </div>
    </div>
    @endif
    <div>
        <p>
            @if(count($article->authors) > 0)
                by:
            @endif
            @php($comma = false)
            @foreach($article->authors as $author)
                @if($comma), @endif
                {{ $author['name'] }}
                @php($comma = true)
            @endforeach
        </p>
    </div>
{!! $content !!}
</div>
