{{-- filepath: f:\Programming\BattlezoneReport\battlezone_report\resources\views\partials\article.blade.php --}}
<div>
    <div class="article-title-with-permalink">
        <a href="/article/{{ $type }}/{{ $code }}" class="article-permalink btn btn-primary" aria-label="Permalink">
            <span class="svg-icon">{!! File::get(resource_path('svg/logo_target.svg')) !!}</span>
        </a>
        <h1>{{ $article->title }}</h1>
    </div>
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
