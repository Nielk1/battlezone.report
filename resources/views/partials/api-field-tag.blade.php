
@if(isset($tags['internal_use']) && $tags['internal_use'])
    <span class="badge text-bg-warning"><span class="print-and-select">[</span>internal use<span class="print-and-select">]</span></span>
@endif
@if(isset($tags['local']))
    <span class="badge text-bg-warning"><span class="print-and-select">[</span>{{ $tags['local'] }}<span class="print-and-select">]</span></span>
@endif
@if(isset($tags['version']))
    <span class="badge text-bg-info"><span class="print-and-select">[</span>version {{ $tags['version'] }}<span class="print-and-select">]</span></span>
@endif
@if(isset($tags))
    @foreach($tags as $tag => $tagcontent)
        @if($tag === 'version' || $tag === 'local' || $tag === 'internal_use' || $tag === '(i)' || $tag === '(!)' || $tag === '(!!)' || $tag === 'mod')
            @continue
        @endif
        <span class="badge text-bg-info"><span class="print-and-select">[</span>{{ $tag }}: {{ $tagcontent }}<span class="print-and-select">]</span></span>
    @endforeach
@endif