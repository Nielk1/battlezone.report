@php($activeNav = 'modding')
@extends(request()->query('ajax') ? 'layouts.ajax' : 'layouts.app')

@section('title', 'Modding')

@section('content')
<style>
    .page-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        /*justify-content: space-evenly;*/
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
        <div class="row">
            <div class="col-3 col-md-2 col-xl-1 p-2 mb-1 d-block d-md-none d-xl-block">
                <div class="d-flex flex-column justify-content-between h-100 multi-chamf gap-1">
                    <a title="Battlezone"                  href="#" data-game-tag="bz1" class="modal-toggle d-flex align-items-center small" role="button"><span class="m-auto">BZ1</span></a>
                    <a title="Battlezone 2"                href="#" data-game-tag="bz2" class="modal-toggle d-flex align-items-center small" role="button"><span class="m-auto">BZ2</span></a>
                </div>
            </div>
            <div class="col-3 col-md-2 col-xl-1 p-2 mb-1 d-block d-md-none d-xl-block">
                <div class="d-flex flex-column justify-content-between h-100 multi-chamf gap-1">
                    <a title="Battlezone 98 Redux"         href="#" data-game-tag="bz98r" class="modal-toggle d-flex align-items-center small" role="button"><span class="m-auto">BZ98R</span></a>
                    <a title="Battlezone Combat Commander" href="#" data-game-tag="bzcc"  class="modal-toggle d-flex align-items-center small" role="button"><span class="m-auto">BZCC</span></a>
                </div>
            </div>
            <div class="col-3 col-md-2 col-xl-1 p-2 mb-1 d-none d-md-block d-xl-none">
                <div class="d-flex flex-column justify-content-between h-100 multi-chamf gap-1">
                    <a title="Battlezone"                  href="#" data-game-tag="bz1"   class="modal-toggle d-flex align-items-center small" role="button"><span class="m-auto">BZ1</span></a>
                    <a title="Battlezone 2"                href="#" data-game-tag="bz2"   class="modal-toggle d-flex align-items-center small" role="button"><span class="m-auto">BZ2</span></a>
                    <a title="Battlezone 98 Redux"         href="#" data-game-tag="bz98r" class="modal-toggle d-flex align-items-center small" role="button"><span class="m-auto">BZ98R</span></a>
                    <a title="Battlezone Combat Commander" href="#" data-game-tag="bzcc"  class="modal-toggle d-flex align-items-center small" role="button"><span class="m-auto">BZCC</span></a>
                </div>
            </div>
            <div class="col-3 col-md-2 col-xl-1 p-2 mb-1">
                <a title="Battlezone 98 Redux: Mission API ScriptUtils"
                   href="#"
                   data-tag="lua"
                   class="modal-toggle d-flex gap-1 align-items-center"
                   role="button">
                    <div class="modal-icon">
                        <svg><use xlink:href="#svg/glyph/brand/lua_a"></use></svg>
                        <svg class="color2"><use xlink:href="#svg/glyph/brand/lua_b"></use></svg>
                    </div>
                </a>
            </div>
            <div class="col-12"><hr class="my-2"></div>
            <div class="col-12 col-md-6 p-2 mb-1">
                <a title="Battlezone 98 Redux: Mission API ScriptUtils"
                   data-ajaxnav="true"
                   href="{{ route('apidoc', ['api' => 'bz98r']) }}"
                   data-game-tags="bz1,bz98r"
                   data-tags="lua"
                   class="modal-button d-flex gap-1 align-items-center"
                   role="button">
                    <div class="modal-icon">
                        <svg><use xlink:href="#svg/glyph/brand/lua_a"></use></svg>
                        <svg class="color2"><use xlink:href="#svg/glyph/brand/lua_b"></use></svg>
                    </div>
                    <div class="flex-fill"><span class="text-nowrap">Mission API ScriptUtils</span> <span class="text-nowrap">(Game Engine Built-in)</span></div>
                    <div class="tags">
                        <div class="badge">BZ1</div>
                        <div class="badge">BZ98R</div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 p-2 mb-1">
                <a title="Battlezone 98 Redux: Extended Mission API"
                   data-ajaxnav="true"
                   href="{{ route('apidoc', ['api' => 'bz98r_api']) }}"
                   data-game-tags="bz98r"
                   data-tags="lua"
                   class="modal-button d-flex gap-1 align-items-center"
                   role="button">
                    <div class="modal-icon">
                        <svg><use xlink:href="#svg/glyph/brand/lua_a"></use></svg>
                        <svg class="color2"><use xlink:href="#svg/glyph/brand/lua_b"></use></svg>
                    </div>
                    <div class="flex-fill"><span class="text-nowrap">Extended Mission API</span> <span class="text-nowrap">(Mod by Nielk1)</span></div>
                    <div class="tags">
                        <div class="badge">BZ98R</div>
                    </div>
                </a>
            </div>
            <div class="col-12 col-md-6 p-2 mb-1">
                <span class="disabled modal-button d-flex gap-1 align-items-center" data-game-tags="bz98r" data-tags="lua" role="button">
                    <div class="modal-icon">
                        <svg><use xlink:href="#svg/glyph/brand/lua_a"></use></svg>
                        <svg class="color2"><use xlink:href="#svg/glyph/brand/lua_b"></use></svg>
                    </div>
                    <div class="flex-fill"><span class="text-nowrap">Extra Utilities</span> <span class="text-nowrap">(Mod by VTrider)</span></div>
                    <div class="tags">
                        <div class="badge">BZ98R</div>
                    </div>
                </span>
            </div>
            <div class="col-12 col-md-6 p-2 mb-1">
                <span class="disabled modal-button d-flex gap-1 align-items-center" data-game-tags="bzcc" data-tags="lua" role="button">
                    <div class="modal-icon">
                        <svg><use xlink:href="#svg/glyph/brand/lua_a"></use></svg>
                        <svg class="color2"><use xlink:href="#svg/glyph/brand/lua_b"></use></svg>
                    </div>
                    <div class="flex-fill"><span class="text-nowrap">Mission API ScriptUtils</span> <span class="text-nowrap">(Game Engine Built-in)</span></div>
                    <div class="tags">
                        <div class="badge">BZCC</div>
                    </div>
                </span>
            </div>
            <div class="col-12 col-md-6 p-2 mb-1">
                <span class="disabled modal-button d-flex gap-1 align-items-center" data-game-tags="bzcc" data-tags="lua" role="button">
                    <div class="modal-icon">
                        <svg><use xlink:href="#svg/glyph/brand/lua_a"></use></svg>
                        <svg class="color2"><use xlink:href="#svg/glyph/brand/lua_b"></use></svg>
                    </div>
                    <div class="flex-fill"><span class="text-nowrap">Extended Mission API</span> <span class="text-nowrap">(Mod by Nielk1)</span></div>
                    <div class="tags">
                        <div class="badge">BZCC</div>
                    </div>
                </span>
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
