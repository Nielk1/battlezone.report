{{-- filepath: resources/views/hello.blade.php --}}
@php($activeNav = 'games')
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

        font-family: "Orbitron", sans-serif;
        font-optical-sizing: auto;
        font-style: normal;
        /* font-variant: small-caps; */
    }

    .logos-container {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        /* Aligns children to the top/left */
        max-width: 600px;
    }

    .logo-img {
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 100%;
        /*z-index: 1;*/
        position: relative;
    }

    .logo-img-wrapper {
        display: flex;
        flex-direction: row;
        gap: 10px;
        width: 100%;
        justify-content: center;
        align-items: center;
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

    .all-sessions-button {
        padding-left: 40px;
        padding-right: 40px;
        position: relative;
    }

    .all-sessions-button i {
        position: absolute;
        right: 8px;
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
    <div class="row row-cols-1 row-cols-md-2 logos-row">
        <div class="logos-container">
            <a href="{{ route('games_bz98r') }}" data-ajaxnav="true" class="logo-img-wrapper"><img src="/images/logo_bz98r_custom.png" alt="Logo" class="logo-img"></a>
            <a href="{{ route('games_bz98r') }}" data-ajaxnav="true" class="btn btn-primary mx-auto mb-3 all-sessions-button">See All Sessions</a>
            <ul class="list-unstyled" style="width:100%">
                <li class="d-flex align-items-center mb-2">
                    <span class="fw-bold text-end pe-3 border-end" style="width:100%">Sessions</span>
                    <span class="ps-3 flex-fill" style="width:100%">X</span>
                </li>
                <li class="d-flex align-items-center mb-2">
                    <span class="fw-bold text-end pe-3 border-end" style="width:100%">Players</span>
                    <span class="ps-3 flex-fill" style="width:100%">Y</span>
                </li>
            </ul>
        </div>
        <div class="logos-container">
            <a href="{{ route('games_bzcc') }}" data-ajaxnav="true" class="logo-img-wrapper"><img src="/images/logo_bzcc_custom.png" alt="Logo" class="logo-img"></a>
            <a href="{{ route('games_bzcc') }}" data-ajaxnav="true" class="btn btn-primary mx-auto mb-3 all-sessions-button">See All Sessions<i class="bi bi-box-arrow-up-right"></i></a>
            <ul class="list-unstyled" style="width:100%">
                <li class="d-flex align-items-center mb-2">
                    <span class="fw-bold text-end pe-3 border-end" style="width:100%">Sessions</span>
                    <span class="ps-3 flex-fill" style="width:100%">X</span>
                </li>
                <li class="d-flex align-items-center mb-2">
                    <span class="fw-bold text-end pe-3 border-end" style="width:100%">Players</span>
                    <span class="ps-3 flex-fill" style="width:100%">Y</span>
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection
