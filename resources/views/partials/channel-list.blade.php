{{-- filepath: resources/views/partials/channel-list.blade.php --}}
@if ($channel->url)
    <a href="{{ $channel->url }}" class="channel-item{{ $channel->type == $type && $channel->code == $code && $channel->subcode == ($subcode ?? null) ? ' active' : '' }}">
@else
    <span class="channel-item{{ $channel->type == $type && $channel->code == $code && $channel->subcode == ($subcode ?? null) ? ' active' : '' }}">
@endif
    @if ($channel->icon)
        <span class="svg-icon">{!! File::get(resource_path('svg/' . $channel->icon . '.svg')) !!}</span>
    @endif
    <span class="channel-name">{{ $channel->name }}</span>
    @if (!empty($channel->buttons))
    <span class="channel-actions">
        @foreach ($channel->buttons as $button)
            <button class="channel-action" title="{{ $button->name }}"
                @foreach($button->attr ?? [] as $attrName => $attrValue)
                    {{ $attrName }}="{{ $attrValue }}"
                @endforeach
            >{!! File::get(resource_path('svg/' . $button->icon . '.svg')) !!}</button>
        @endforeach
    </span>
    @endif
@if ($channel->url)
    </a>
@else
    </span>
@endif
@if (!empty($channel->children))
    @php($iconFlags = $channel->childrenHaveIcon())
    <div class="channel-group{{ $channel->icon ? ' has-icon-parent' : '' }}{{ $iconFlags[0] ? ' any-icon' : '' }}{{ $iconFlags[1] ? ' all-icon' : '' }}">
        @foreach($channel->children as $child)
            @include('partials.channel-list', ['channel' => $child])
        @endforeach
    </div>
@endif
