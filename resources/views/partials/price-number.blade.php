{{-- price-number.blade.php --}}
@php
    $is_best = $deal
        && isset($deal['price']['amountInt'], $deal['storeLow']['amountInt'])
        && $deal['price']['amountInt'] <= $deal['storeLow']['amountInt'];
@endphp

@if($deal['cut'] > 0)
    <span class="text-nowrap flex-fill text-end">
        <span class="text-body-tertiary text-decoration-line-through">
            ${{ number_format($deal['regular']['amount'], 2) }}
        </span>
        <span class="text-danger">
            ${{ number_format($deal['price']['amount'], 2) }}
        </span>
        @if($is_best)
            <span class="badge text-bg-primary" title="Best price at this store.">
                -{{ $deal['cut'] }}%
            </span>
        @else
            <span class="badge text-bg-success">
                -{{ $deal['cut'] }}%
            </span>
        @endif
    </span>
@else
    <span class="text-nowrap flex-fill text-end">
        <span class="text-body-secondary">
            ${{ number_format($deal['price']['amount'], 2) }}
        </span>
    </span>
@endif
