@php($activeNav = 'modding')
@extends(request()->query('ajax') ? 'layouts.ajax' : 'layouts.app')

@section('title', 'Modding')

@section('content')
<style>
    .page-container {
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

        /*font-family: "Orbitron", sans-serif;
        font-optical-sizing: auto;
        font-style: normal;*/
        /* font-variant: small-caps; */
    }
</style>

<div class="page-container">
    <div class="container">
        <div class="row modal-nav">
            <div class="col-12">
                <div class="modal-block align-items-center">
                    <div class="modal-icon-box">
                        <div class="modal-icon">
                            {{--{!! File::get(resource_path('svg/glyph/brand/lua.svg')) !!}--}}
                            <svg><use xlink:href="#svg/glyph/brand/lua_a"></use></svg>
                            <svg class="color2"><use xlink:href="#svg/glyph/brand/lua_b"></use></svg>
                        </div>
                    </div>
                    <div class="d-flex flex-fill flex-column flex-md-row gap-1">
                        <div class="flex-grow-1">
                            <div class="d-flex flex-column flex-grow gap-1">
                                <a title="BZ98R Bare ScriptUtils"
                                   data-ajaxnav="true"
                                   href="{{ route('apidoc', ['api' => 'bz98r']) }}"
                                   class="modal-button x4btn"
                                   role="button">BZ98R Bare ScriptUtils</a>

                                <a title="BZ98R _api Wrapper"
                                   data-ajaxnav="true"
                                   href="{{ route('apidoc', ['api' => 'bz98r_api']) }}"
                                   class="modal-button x4btn"
                                   role="button">BZ98R "_api" Wrapper</a>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-column flex-grow gap-1">
                                <span class="modal-button x4btn disabled" href="#" role="button">BZCC Bare ScriptUtils</span>
                                <span class="modal-button x4btn disabled" href="#" role="button">BZCC "_api" Wrapper</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--
            <div class="col-lg-6 col-12">
                <div class="modal-block align-items-center">
                    <div class="modal-icon-box align-items-center">
                        <div class="modal-icon">
                            <svg><use xlink:href="#svg/glyph/brand/lua_a"></use></svg>
                            <svg class="color2"><use xlink:href="#svg/glyph/brand/lua_b"></use></svg>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-column flex-grow gap-1">
                            <a class="modal-button" href="#" role="button">BZ98R Bare ScriptUtils</a>
                            <a class="modal-button" href="#" role="button">BZCC Bare ScriptUtils</a>
                            <a class="modal-button" href="#" role="button">BZ98R _api Wrapper</a>
                            <a class="modal-button" href="#" role="button">BZ9CC _api Wrapper</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                    <div class="modal-block align-items-center">
                    <div class="modal-icon-box">
                        <div class="modal-icon">
                            <svg><use xlink:href="#svg/glyph/brand/lua_a"></use></svg>
                            <svg class="color2"><use xlink:href="#svg/glyph/brand/lua_b"></use></svg>
                        </div>
                    </div>
                    <div>
                        <h5>Battlezone CC - Lua API</h5>
                        <p>ScriptUtils and mod APIs</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="modal-block">
                    <div class="modal-icon-box">
                        <div class="modal-icon">
                            <svg><use xlink:href="#svg/logo_battlezone"></use></svg>
                        </div>
                    </div>
                    <div>
                        BODY
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="modal-block">
                    <div class="modal-icon-box">
                        <div class="modal-icon">
                            <svg><use xlink:href="#svg/logo_battlezone"></use></svg>
                        </div>
                    </div>
                    <div>
                        BODY
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12">
                <div class="modal-block">
                    <div class="modal-icon-box">
                        <div class="modal-icon">
                            <svg><use xlink:href="#svg/logo_battlezone"></use></svg>
                        </div>
                    </div>
                    <div>
                        BODY
                    </div>
                </div>
            </div>--}}
        </div>
    </div>
</div>

@endsection
