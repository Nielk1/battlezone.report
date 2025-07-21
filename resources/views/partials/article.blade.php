{{-- filepath: f:\Programming\BattlezoneReport\battlezone_report\resources\views\partials\article.blade.php --}}
<div class="container">
    <h1>{{ $article->title }}</h1>
    <div>
        <blockquote class="article-author-with-permalink">
            <a href="/article/{{ $type }}/{{ $code }}" class="article-permalink btn btn-default" aria-label="Permalink">
                <span class="glyphicon glyphicon-link" aria-hidden="true"></span>
                LINK
            </a>
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
