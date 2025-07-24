{{-- filepath: resources/views/hello.blade.php --}}
@php($activeNav = 'home')
@extends(request()->query('ajax') ? 'layouts.ajax' : 'layouts.app')

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

<div class="logo-container">
    <img src="/images/logo.png" alt="Logo" class="logo-img dark screen">
    <img src="/images/logo_print.png" alt="Logo" class="logo-img light print">

    <div class="row row-cols-1 row-cols-md-3 mt-4 logos-row">
        <div class="logos-container">
            <img src="/images/logo_bz98r_custom.png" alt="Logo" class="logo-img">
            @include('partials.price-cluster', ['code' => 'BZ98R', 'deal' => $prices['BZ98R']])
        </div>
        <div class="logos-container">
            <img src="/images/logo_tror_custom.png" alt="Logo" class="logo-img">
            @include('partials.price-cluster', ['code' => 'TROR', 'deal' => $prices['TROR']])
        </div>
        <div class="logos-container">
            <img src="/images/logo_bzcc_custom.png" alt="Logo" class="logo-img">
            @include('partials.price-cluster', ['code' => 'BZCC', 'deal' => $prices['BZCC']])
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 mt-4 logos-row">
        <div class="logos-container">
            <div class="logo-img-pair">
                <img src="/images/logo_bz98r_custom.png" alt="Logo" class="logo-img">
                <h1>+</h1>
                <img src="/images/logo_tror_custom.png" alt="Logo" class="logo-img">
            </div>
            @include('partials.price-cluster', ['code' => 'BZ98R|TROR', 'deal' => $prices['BZ98R|TROR']])
        </div>
        <div class="logos-container">
            <div class="logo-img-pair">
                <img src="/images/logo_bz98r_custom.png" alt="Logo" class="logo-img">
                <h1>+</h1>
                <img src="/images/logo_bzcc_custom.png" alt="Logo" class="logo-img">
            </div>
            @include('partials.price-cluster', ['code' => 'BZ98R|BZCC', 'deal' => $prices['BZ98R|BZCC']])
        </div>
        <div class="logos-container">
            <div class="logo-img-pair">
                <img src="/images/logo_bzvr.png" alt="Logo" class="logo-img">
            </div>
            @include('partials.price-cluster', ['code' => 'BZVR', 'deal' => $prices['BZVR']])
        </div>
    </div>
</div>

@endsection
