
@if(isset($tags['internal_use']) && $tags['internal_use'])
    <span class="badge api_tag text-bg-warning"><span class="select-only">[</span>internal use<span class="select-only">]</span></span>
@endif
@if(isset($tags['local']))
    <span class="badge api_tag text-bg-warning"><span class="select-only">[</span>{{ $tags['local'] }}<span class="select-only">]</span></span>
@endif
@if(isset($tags['version']))
    <span class="badge api_tag text-bg-info"><span class="select-only">[</span>version {{ $tags['version'] }}<span class="select-only">]</span></span>
@endif
@if(isset($tags))
    @foreach($tags as $tag => $tagcontent)
        @if($tag === 'version' || $tag === 'local' || $tag === 'internal_use' || $tag === '(i)' || $tag === '(!)' || $tag === '(!!)' || $tag === 'mod')
            @continue
        @endif
        <span class="badge api_tag text-bg-info"><span class="select-only">[</span>{{ $tag }}: {{ $tagcontent }}<span class="select-only">]</span></span>
    @endforeach
@endif
