{{-- filepath: resources/views/hello.blade.php --}}
@php($activeNav = 'home')
@extends(request()->query('ajax') ? 'layouts.ajax' : 'layouts.app')

@section('title', 'Home')

@section('content')
<style>
.logo-container {
    min-height: 100vh;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-evenly;
    width: 100%;
    background: url('/images/background_space.jpg') center center / cover no-repeat;
    position: relative;
    padding: 50px 10px;
    overflow: auto;
}

.logo-img {
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
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
</style>

<div class="logo-container">
    <div class="container" style="height: calc(100% + 6rem);">{{-- A bit of a hack fix until I figured out the style issue eliminating padding when too short for all content --}}
        <div class="row logos-row" style="height: 100%;">
            <div class="d-flex justify-content-center col-12">
                <img src="/images/logo.png" alt="Logo" class="logo-img dark d-print-none">
            </div>
            <div class="d-flex justify-content-center col-12">
                <img src="/images/logo_print.png" alt="Logo" class="logo-img light d-none d-print-block">
            </div>
            <div class="col-12 col-lg-4 col-print-6 logos-container">
                <img src="/images/logo_bz98r_custom.png" alt="Logo" class="d-print-none logo-img">
                <span class="d-print-block d-none container-fluid text-center">Battlezone 98 Redux</span>
                @include('partials.price-cluster', ['code' => 'BZ98R', 'deal' => $prices['BZ98R']])
            </div>
            <div class="col-12 col-lg-4 col-print-6 logos-container">
                <img src="/images/logo_tror_custom.png" alt="Logo" class="d-print-none logo-img">
                <span class="d-print-block d-none container-fluid text-center">Battlezone: The Red Odyssey</span>
                @include('partials.price-cluster', ['code' => 'TROR', 'deal' => $prices['TROR']])
            </div>
            <hr class="d-print-block d-none col-12 invisible">
            <div class="col-12 col-lg-4 col-print-6 logos-container">
                <img src="/images/logo_bzcc_custom.png" alt="Logo" class="d-print-none logo-img">
                <span class="d-print-block d-none container-fluid text-center">Battlezone: Combat Commander</span>
                @include('partials.price-cluster', ['code' => 'BZCC', 'deal' => $prices['BZCC']])
            </div>
            <div class="col-12 col-lg-4 col-print-6 logos-container">
                <div class="d-print-none logo-img-pair">
                    <img src="/images/logo_bz98r_custom.png" alt="Logo" class="logo-img">
                    <h1>+</h1>
                    <img src="/images/logo_tror_custom.png" alt="Logo" class="logo-img">
                </div>
                <span class="d-print-block d-none container-fluid text-center">Battlezone 98 Redux + Battlezone: The Red Odyssey</span>
                @include('partials.price-cluster', ['code' => 'BZ98R|TROR', 'deal' => $prices['BZ98R|TROR']])
            </div>
            <hr class="d-print-block d-none col-12 invisible">
            <div class="col-12 col-lg-4 col-print-6 logos-container">
                <div class="d-print-none logo-img-pair">
                    <img src="/images/logo_bz98r_custom.png" alt="Logo" class="logo-img">
                    <h1>+</h1>
                    <img src="/images/logo_bzcc_custom.png" alt="Logo" class="logo-img">
                </div>
                <span class="d-print-block d-none container-fluid text-center">Battlezone 98 Redux + Battlezone: Combat Commander</span>
                @include('partials.price-cluster', ['code' => 'BZ98R|BZCC', 'deal' => $prices['BZ98R|BZCC']])
            </div>
            <div class="col-12 col-lg-4 col-print-6 logos-container">
                <div class="d-print-none logo-img-pair">
                    <img src="/images/logo_bzvr.png" alt="Logo" class="logo-img">
                </div>
                <span class="d-print-block d-none container-fluid text-center">Battlezone VR</span>
                @include('partials.price-cluster', ['code' => 'BZVR', 'deal' => $prices['BZVR']])
            </div>
        </div>
    </div>
</div>

@endsection
