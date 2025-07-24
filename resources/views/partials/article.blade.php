{{-- filepath: f:\Programming\BattlezoneReport\battlezone_report\resources\views\partials\article.blade.php --}}
<div>
    @if(!$article->hidepermlink || $article->title)
    <div class="article-title-with-permalink">
        @if(!$article->hidepermlink)
        <a href="/article/{{ $type }}/{{ $code }}" class="article-permalink btn btn-primary" aria-label="Permalink">
            <span class="svg-icon">{!! File::get(resource_path('svg/logo_target.svg')) !!}</span>
        </a>
        @endif
        @if($article->title)
        <h1>{{ $article->title }}</h1>
        @endif
    </div>
    @endif
    <div>
        <blockquote>
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
        </blockquote>
    </div>
{!! $content !!}
</div>
