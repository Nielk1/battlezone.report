@php($activeNav = 'modding')
@extends(request()->query('ajax') ? 'layouts.ajax' : 'layouts.app')

@section('title', 'Modding')

@section('content')
<style>
    .logo-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-evenly;
        width: 100%;
        /*height: 100%;*/
        background: url('/images/background_space.jpg') center center / cover no-repeat;
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

    .multiplayer-properties {
        width: 100%;
        font-size: 1.2em;
    }
    .multiplayer-properties > li > * {
        width: 100%;
        height: 1.2em;
        position: relative;
    }
    .multiplayer-properties i.bi {
        font-size: 1.2em;
        top: -3px;
        position: absolute;
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
            background: url('/images/background_space.jpg') center center / cover no-repeat fixed;
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
    <div class="container">
        <div class="row modal-nav">
            <a title="Home"              data-nav="home"      data-ajaxnav="true" href="{{ route('home') }}"      class="modal-icon-box{{ ($activeNav ?? '') === 'home'      ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><svg><use xlink:href="#svg/logo_battlezone"></use></svg></div></a>
            <a title="Issue"             data-nav="issue"     data-ajaxnav="true" href="{{ route('issue') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'issue'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-newspaper"></i></div></a>
            <a title="Chronicles"        data-nav="chronicle" data-ajaxnav="true" href="{{ route('chronicle') }}" class="modal-icon-box{{ ($activeNav ?? '') === 'chronicle' ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-feather"></i></div></a>
            <a title="Session List"      data-nav="games"     data-ajaxnav="true" href="{{ route('games') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'games'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-controller"></i></div></a>
            <a title="Modding Resources" data-nav="modding"   data-ajaxnav="true" href="{{ route('modding') }}"   class="modal-icon-box{{ ($activeNav ?? '') === 'modding'   ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-tools"></i></div></a>
            <a title="Social"            data-nav="social"    data-ajaxnav="true" href="{{ route('social') }}"    class="modal-icon-box{{ ($activeNav ?? '') === 'social'    ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-chat-fill"></i></div></a>
            <a title="About"             data-nav="about"     data-ajaxnav="true" href="{{ route('about') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'about'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-info-circle-fill"></i></div></a>
            <a title="About"             data-nav="about"     data-ajaxnav="true" href="{{ route('about') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'about'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-info-circle-fill"></i></div></a>
            <a title="About"             data-nav="about"     data-ajaxnav="true" href="{{ route('about') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'about'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-info-circle-fill"></i></div></a>
            <a title="About"             data-nav="about"     data-ajaxnav="true" href="{{ route('about') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'about'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-info-circle-fill"></i></div></a>
            <a title="About"             data-nav="about"     data-ajaxnav="true" href="{{ route('about') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'about'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-info-circle-fill"></i></div></a>
            <a title="About"             data-nav="about"     data-ajaxnav="true" href="{{ route('about') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'about'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-info-circle-fill"></i></div></a>
            <a title="About"             data-nav="about"     data-ajaxnav="true" href="{{ route('about') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'about'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-info-circle-fill"></i></div></a>
            <a title="About"             data-nav="about"     data-ajaxnav="true" href="{{ route('about') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'about'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-info-circle-fill"></i></div></a>
            <a title="About"             data-nav="about"     data-ajaxnav="true" href="{{ route('about') }}"     class="modal-icon-box{{ ($activeNav ?? '') === 'about'     ? ' active' : '' }}"><div style="position:absolute;bottom:-2em;">Testing123</div><div class="modal-icon"><i class="bi bi-info-circle-fill"></i></div></a>
        </div>
    </div>
</div>

@endsection
