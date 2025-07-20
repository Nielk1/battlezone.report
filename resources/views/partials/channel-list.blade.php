{{-- filepath: resources/views/partials/channel-list.blade.php --}}
<div class="channel-item">
    @if ($channel->icon)
        <span class="svg-icon">{!! File::get(resource_path('svg/' . $channel->icon . '.svg')) !!}</span>
    @endif
    <span class="channel-name">{{ $channel->name }}</span>
    <span class="channel-actions">
        <span class="channel-action">{!! File::get(resource_path('svg/logo_steam.svg')) !!}</span>
        <span class="channel-action">{!! File::get(resource_path('svg/logo_steam.svg')) !!}</span>
    </span>
</div>
@if (!empty($channel->children))
    @php($iconFlags = $channel->childrenHaveIcon())
    <div class="channel-group{{ $channel->icon ? ' has-icon-parent' : '' }}{{ $iconFlags[0] ? ' any-icon' : '' }}{{ $iconFlags[1] ? ' all-icon' : '' }}">
        @foreach($channel->children as $child)
            @include('partials.channel-list', ['channel' => $child])
        @endforeach
    </div>
@endif
