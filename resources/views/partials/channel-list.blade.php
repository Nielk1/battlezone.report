{{-- filepath: resources/views/partials/channel-list.blade.php --}}
<span class="channel-item{{ $channel->type == ($type ?? null) && $channel->code == ($code ?? null) && $channel->subcode == ($subcode ?? null) ? ' active' : '' }}">
    @if ($channel->url)
        <a class="channel-link" data-ajaxnav="2" data-ajaxnav-target="#sub-content" href="{{ $channel->url }}">
    @else
        <span class="channel-link">
    @endif
    @if ($channel->icon)
        <span class="svg-icon">{!! File::get(resource_path('svg/' . $channel->icon . '.svg')) !!}</span>
    @endif
    <span class="channel-name">{{ $channel->name }}</span>
    @if ($channel->url)
        </a>
    @else
        </span>
    @endif
    @if (!empty($channel->buttons))
        <span class="channel-actions">
            @foreach ($channel->buttons as $button)
                <a href="{{ $button->href ?? '#' }}" class="channel-action" title="{{ $button->name }}"
                    @foreach($button->attr ?? [] as $attrName => $attrValue)
                        {{ $attrName }}="{{ $attrValue }}"
                    @endforeach
                >{!! File::get(resource_path('svg/' . $button->icon . '.svg')) !!}</a>
            @endforeach
        </span>
    @endif
</span>
@if (!empty($channel->children))
    @php($iconFlags = $channel->childrenHaveIcon())
    <div class="channel-group{{ $channel->icon ? ' has-icon-parent' : '' }}{{ $iconFlags[0] ? ' any-icon' : '' }}{{ $iconFlags[1] ? ' all-icon' : '' }}">
        @foreach($channel->children as $child)
            @include('partials.channel-list', ['channel' => $child])
        @endforeach
    </div>
@endif
