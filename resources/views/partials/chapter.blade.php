{{-- filepath: f:\Programming\BattlezoneReport\battlezone_report\resources\views\partials\article.blade.php --}}
<div class="container">
    <h1>{{ $article->title }}</h1>
    <div>
        <blockquote class="article-author-with-permalink">
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
