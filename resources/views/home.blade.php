{{-- filepath: resources/views/hello.blade.php --}}
@php($activeNav = 'home')
@extends('layouts.app')

@section('title', 'Home')

@section('content')
<style>
.logo-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-evenly;
    width: 100%;
    /*height: 100%;*/
    background: url('/images/logo_back.jpg') center center / cover no-repeat;
    position: relative;
    padding: 50px 10px;
    overflow: auto;
}

.logos-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start; /* Aligns children to the top/left */
    max-width: 430px;
    margin-top: 20px;
}

.logo-img {
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 100%;
    /*z-index: 1;*/
    position: relative;
    margin-bottom: 5px;
}
.logo-img-pair {
    display: flex;
    flex-direction: row;
    gap: 10px;
    width: 100%;
    justify-content: center;
    align-items: center;
}
.logo-img-pair .logo-img {
    width: 45%;
    height: auto;
}

.logos-row {
    width: 100%;
    /*max-width: 1280px;*/
    margin: 0 auto;
    justify-content: center;
}

.logos-row .col {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding: 10px;
    /*min-width: 430px;*/
}

.logos-row .col .logo-container {
    max-width: 430px;
}

.price-row {
    width: 100%;
    gap: 4px;
    display: flex;
    flex-direction: column;
}

.price-row > .flex-row {
    gap: 4px;
}

.sale-btn {
    flex: 1;
    padding: 0.4em 0.3em !important;
    font-size: small;
}

.screen {
    display: inherit;
}
.print {
    display: none;
}

:root,
[data-bs-theme=light] {
    .logo-container {
        background: none;
    }
    .dark {
        display: none;
    }
    .light {
        display: inherit;
    }
}

[data-bs-theme=dark] {
    .logo-container {
        background: url('/images/logo_back.jpg') center center / cover no-repeat fixed;
    }
    .dark {
        display: inherit;
    }
    .light {
        display: none;
    }
}

@media print {
    .screen {
        display: none;
    }
    .print {
        display: block;
    }
}
</style>

<?php
    function preparePriceFragment($deal) {
        $is_best = $deal
                && isset($deal['price']['amountInt'], $deal['storeLow']['amountInt'])
                && $deal['price']['amountInt'] <= $deal['storeLow']['amountInt'];
        ?>

        @if($deal['cut'] > 0)
            <span class="text-nowrap flex-fill text-end">
                {{-- On sale: show old price struck through, new price, and sale % --}}
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
            {{-- Not on sale: show regular price --}}
            <span class="text-nowrap flex-fill text-end">
                <span class="text-body-secondary">
                    ${{ number_format($deal['price']['amount'], 2) }}
                </span>
            </span>
        @endif

        <?php
    }

    function preparePrice($deal, $name, $must_be_under = 999999) {
        if (!isset($deal)) return false;
        if (!isset($deal['price']['amount'])) return false;
        if ($deal['price']['amount'] >= $must_be_under) return false;
?>
        {{-- {{$name}}: {{ $deal['price']['amount'] }}<br/> --}}

        <a href="{{ $deal['url'] }}" target="_blank" rel="noopener noreferrer" role="button" class="btn btn-dark d-flex align-items-center sale-btn">
            <span class="svg-icon" style="height: 1em; width: auto; display: inline-flex; align-items: center; margin-right: 0.5em;">
                {!! File::get(resource_path('svg/logo_steam.svg')) !!}
            </span>
            <span class="text-truncate" title="{{ $name ?? $deal['shop']['name'] }}">
                {{ $name ?? $deal['shop']['name'] }}
            </span>
            <?php preparePriceFragment($deal) ?>
        </a>
<?php
        return true;
    }

    function preparePrices($deal) {
        if (!isset($deal)) return;
        $price_gog = $price_steam = $deal['GOG']['price']['amount'] ?? null;
        $price_steam = $price_steam = $deal['Steam']['price']['amount'] ?? null;
        ?>
            <div class="price-row">
                @if (isset($deal['GOG']) || isset($deal['Steam']))
                    <div class="d-flex flex-row flex-md-column flex-xl-row flex-row" role="group">
                        @if (isset($deal['GOG']))
                            <a href="{{ $deal['GOG']['url'] }}" target="_blank" rel="noopener noreferrer" role="button" class="btn btn-primary d-flex align-items-center sale-btn" style="
                                --bs-btn-border-color: #292253;
                                --bs-btn-bg: #292253;
                                --bs-btn-hover-bg: #431f93;
                                --bs-btn-hover-border-color: #431f93;
                                --bs-btn-active-bg: #6e45ff;
                                --bs-btn-active-border-color: #6e45ff;">
                                <span class="svg-icon" style="height: 1em; width: auto; display: inline-flex; align-items: center; margin-right: 0.5em;">
                                    {!! File::get(resource_path('svg/logo_gog.svg')) !!}
                                </span>
                                <?php preparePriceFragment($deal['GOG']) ?>
                            </a>
                        @endif
                        @if (isset($deal['Steam']))
                            <a href="{{ $deal['Steam']['url'] }}" target="_blank" rel="noopener noreferrer" role="button" class="btn btn-dark d-flex align-items-center sale-btn">
                                <span class="svg-icon" style="height: 1em; width: auto; display: inline-flex; align-items: center; margin-right: 0.5em;">
                                    {!! File::get(resource_path('svg/logo_steam.svg')) !!}
                                </span>
                                <?php preparePriceFragment($deal['Steam']) ?>
                            </a>
                        @endif
                    </div>
                @endif
                <div class="d-flex flex-column" style="width: 100%;">
                <?php
                    $found = false;
                    foreach ($deal as $key => $d) {
                        foreach ($d['drm'] ?? [] as $drm) {
                            if (($drm['name'] ?? null) == "Steam") {
                                $found |= preparePrice($d, $key, $price_steam);
                                break;
                            }
                        }
                    }
                    if (!$found) {
                        ?>
                        <div class="d-flex flex-column" style="width: 100%;">
                            <span role="button" class="btn btn-dark disabled d-flex align-items-center sale-btn">
                                <span class="text-truncate text-nowrap flex-fill text-center">
                                    No cheaper Steam Key found
                                </span>
                            </span>
                        </div>
                        <?php
                    }
                ?>
                </div>
            </div>
        <?php
    }
?>

<div class="logo-container">
    <img src="/images/logo.png" alt="Logo" class="logo-img dark screen">
    <img src="/images/logo_print.png" alt="Logo" class="logo-img light print">

    <div class="row row-cols-1 row-cols-md-3 mt-4 logos-row">
        <div class="logos-container">
            <img src="/images/logo_bz98r_custom.png" alt="Logo" class="logo-img">
            <?php preparePrices($prices['BZ98R']); ?>
        </div>
        <div class="logos-container">
            <img src="/images/logo_tror_custom.png" alt="Logo" class="logo-img">
            <?php preparePrices($prices['TROR']); ?>
        </div>
        <div class="logos-container">
            <img src="/images/logo_bzcc_custom.png" alt="Logo" class="logo-img">
            <?php preparePrices($prices['BZCC']); ?>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 mt-4 logos-row">
        <div class="logos-container">
            <div class="logo-img-pair">
                <img src="/images/logo_bz98r_custom.png" alt="Logo" class="logo-img">
                <h1>+</h1>
                <img src="/images/logo_tror_custom.png" alt="Logo" class="logo-img">
            </div>
            <?php preparePrices($prices['BZ98R|TROR']); ?>
        </div>
        <div class="logos-container">
            <div class="logo-img-pair">
                <img src="/images/logo_bz98r_custom.png" alt="Logo" class="logo-img">
                <h1>+</h1>
                <img src="/images/logo_bzcc_custom.png" alt="Logo" class="logo-img">
            </div>
            <?php preparePrices($prices['BZ98R|BZCC']); ?>
        </div>
        <div class="logos-container">
            <div class="logo-img-pair">
                <img src="/images/logo_bzvr.png" alt="Logo" class="logo-img">
            </div>
            <?php preparePrices($prices['BZVR']); ?>
        </div>
    </div>

</div>

@endsection
