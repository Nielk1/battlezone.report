{{-- price-steamkey.blade.php --}}
<a href="{{ $deal['url'] }}" target="_blank" rel="noopener noreferrer" role="button" class="btn btn-dark d-flex align-items-center sale-btn">
    <span class="svg-icon">
        {!! File::get(resource_path('svg/logo_steam.svg')) !!}
    </span>
    <span class="text-truncate" title="{{ $name ?? $deal['shop']['name'] }}">
        {{ $name ?? $deal['shop']['name'] }}
    </span>
    @include('partials.price-number', ['deal' => $deal])
</a>
