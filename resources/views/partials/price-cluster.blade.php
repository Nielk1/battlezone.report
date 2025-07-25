{{-- price-cluster.blade.php --}}
<div class="price-row" id="price-cluster-{{ $code ?? 'UNK' }}">
    @if (isset($deal['GOG']) || isset($deal['Steam']))
        <div class="d-flex flex-row flex-md-column flex-xl-row flex-row" role="group">
            @if (isset($deal['GOG']))
                <a href="{{ $deal['GOG']['url'] }}" target="_blank" rel="noopener noreferrer" role="button" class="btn btn-gog d-flex align-items-center sale-btn">
                    <span class="svg-icon">
                        {!! File::get(resource_path('svg/logo_gog.svg')) !!}
                    </span>
                    @include('partials.price-number', ['deal' => $deal['GOG']])
                </a>
            @endif
            @if (isset($deal['Steam']))
                <a href="{{ $deal['Steam']['url'] }}" target="_blank" rel="noopener noreferrer" role="button" class="btn btn-steam d-flex align-items-center sale-btn">
                    <span class="svg-icon">
                        {!! File::get(resource_path('svg/logo_steam.svg')) !!}
                    </span>
                    @include('partials.price-number', ['deal' => $deal['Steam']])
                </a>
            @endif
        </div>
    @endif
    @php $found = false; @endphp
    @foreach ($deal as $key => $d)
        @if (isset($d['drm']))
            @foreach ($d['drm'] as $drm)
                @if (($drm['name'] ?? null) == "Steam" && isset($d['price']['amount']) && (!isset($deal['Steam']['price']['amount']) || $d['price']['amount'] < $deal['Steam']['price']['amount']))
                    @php $found = true; @endphp
                    @include('partials.price-steamkey', ['deal' => $d, 'name' => $key])
                    @break
                @endif
            @endforeach
        @endif
    @endforeach
    @if (!$found)
        <div class="d-flex flex-column" style="width: 100%;">
            <span role="button" class="btn btn-dark disabled d-flex align-items-center sale-btn">
                <span class="text-truncate text-nowrap flex-fill text-center">
                    No cheaper Steam Key found
                </span>
            </span>
        </div>
    @endif
</div>
