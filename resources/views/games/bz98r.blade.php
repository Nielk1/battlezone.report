{{-- filepath: resources/views/hello.blade.php --}}
@php($activeNav = 'games')
@extends(request()->query('ajax') ? 'layouts.ajax' : 'layouts.app')

@section('title', 'Battlezone 98 Redux - Multiplayer Session List')

@section('content')
    <style>
        :root {
            --section-icons-count: 5;
            --section-icons-width: calc((25px * var(--section-icons-count)) + (4px * (var(--section-icons-count) - 1)));

            --section-mode-width: 200px;

            --section-mode-extra-left: calc(var(--section-icons-width) + var(--section-mode-width) + ((var(--section-icons-count) - 1) * 4px))
        }

        @media screen and (max-width: 768px) {
            :root {
                --section-mode-width: 25px;
            }
        }

        /*
         * Custom Icon: Rifle
         * Formed by cropping the soldier glyph to only show the rifle portion, then rotating it 45 deg.
         */
        .fa-cust-rifle {
            --fa: "\e54e";
        }
            .fa-cust-rifle.fa-brands:before,
            .fa-cust-rifle.fa-regular:before,
            .fa-cust-rifle.fa-solid:before,
            .fa-cust-rifle.fa:before,
            .fa-cust-rifle.fab:before,
            .fa-cust-rifle.far:before,
            .fa-cust-rifle.fas:before {
                width: 10px;
                overflow: hidden;
                display: inline-block;
                direction: rtl;
                transform: rotate(45deg);
                font-size: x-large;
                margin-top: 1px;
            }

        .chamfer {
            --chamfer-size-inner: var(--chamfer-size, 3px);
            clip-path: polygon(var(--chamfer-size-inner) 0, calc(100% - var(--chamfer-size-inner)) 0, 100% var(--chamfer-size-inner), 100% calc(100% - var(--chamfer-size-inner)), calc(100% - var(--chamfer-size-inner)) 100%, var(--chamfer-size-inner) 100%, 0 calc(100% - var(--chamfer-size-inner)), 0 var(--chamfer-size-inner));
        }

        .icon {
            font-size: small;

            height: 25px;
            width: 25px;
            min-height: 25px;
            min-width: 25px;

            display: block;
        }
            .icon i {
                width: 25px;
                line-height: 25px;
                font-size: 1.2em;
                text-align: center;
                display: block;
            }

        .icon_lock {
            color: #000;
            background-color: #fe5b5b;
        }
        .icon_sync_lock {
            color: #000;
            background-color: #fe5b5b;
        }

        .icon_password,
        .icon_mod {
            color: #fff;
            background-color: #7f00ff;
        }
        /*.icon_password i {
            transform: rotate(45deg);
        }*/

        .icon_sync_join {
            color: #000;
            background-color: #fe5b5b;
        }
        .icon_sync_script {
            color: #fff;
            background-color: #7f00ff;
            /*color: #000;
            background-color: #4dad54;*/
        }


        .icon_state_pregame {
            color: #fff;
            background-color: #7f00ff;
        }
        .icon_state_ingame {
            color: #000;
            background-color: #4dad54;
        }
        .icon_state_postgame {
            color: #000;
            background-color: #fe5b5b;
        }


        .icon_leader {
            color: #fff;
            background-color: #222;
        }

        .icon_host {
            color: #fff;
            background-color: #222;
        }

        .icon_gog {
            color: #fff;
            background-color: #222;
        }

            .icon_gog img {
                width: 25px;
                line-height: 25px;
                text-align: center;
                display: block;
                padding: 3px;
                filter: invert(1);
            }
            /*.icon_gog img.color {
                fill: #fff;
            }*/

        .icon_steam {
            color: #fff;
            background-color: #222;
        }
            .icon_steam > i {
                left: -2px;
                position: relative;
                font-size: x-large;
            }

        .game_mode_icon {
            height: 25px;
            width: 25px;
            min-height: 25px;
            min-width: 25px;
            background-size: cover;
            /*display: inline-block;*/
            /*clip-path: polygon(10% 0, 90% 0, 100% 10%, 100% 90%, 90% 100%, 10% 100%, 0 90%, 0 10%);*/
        }
        .nub {
            height: 25px;
            width: 10px;
            min-height: 25px;
            min-width: 10px;
        }
        .game_mode {
            height: 25px;
            background-position: left;
            background-size: 1000000%;
            width: var(--section-mode-width);
            /*clip-path: polygon(2.5px 0, calc(100% - 2.5px) 0, 100% 2.5px, 100% calc(100% - 2.5px), calc(100% - 2.5px) 100%, 2.5px 100%, 0 calc(100% - 2.5px), 0 2.5px);*/
        }

        .game_mode_cell,
        .map_cell {
            width: var(--section-mode-width);
            flex: 0 0 var(--section-mode-width);
        }

        .map_name {
            width: 100%;
            /*margin: 2px auto 1px auto;*/
            /*margin: .25rem auto 0 auto;*/
            margin: 0 auto 0 auto;
            height: 25px;
            line-height: 1.1em;
        }

        .game_mode_text {
            font-family: "Orbitron", sans-serif;

            /*filter: invert(1) grayscale(1) contrast(9);*/
            filter: brightness(0.8) invert(1) grayscale(1) contrast(1000);
            color: transparent;
            background: inherit;
            background-clip: text;
            font-weight: 800;
            font-variant: small-caps;
            /*font-size: 0.875em;
            text-transform: capitalize;
            line-height: 25px;*/
        }
            .game_mode_text .smallcap {
                font-weight: 1000;
            }
            .game_mode_text .bigcap {
                font-weight: 600;
            }
            /* only good for counts under 20, otherwise we don't add this class */
            .player_number {
                width: 1.5em;
                display: inline-block;
                text-align: center;
            }

        /*.orbitron-bz98r {
            font-family: "Orbitron", sans-serif;
            font-optical-sizing: auto;
            font-weight: <weight>;
            font-style: normal;
        }*/
        /*body {*/
        .sidebar3 > * {
            font-family: "Orbitron", sans-serif;
            font-optical-sizing: auto;
            /*font-weight: <weight>;*/
            font-style: normal;
            /*font-variant: small-caps;*/
        }

        /*tbody, td, tfoot, th, thead, tr {
            border-style: none;
        }*/
        /*.table>:not(caption)>*>* {
            padding: .2rem .2rem;
        }*/
        /*.gamelist.table>:not(caption)>*>* {*/
        /*.gamelist>:not(caption)>*>* {
            color: #00ff00;
        }*/
        #sessionList>:not(caption)>* {
            color: #00ff00;
        }


        .player_info_box {
            /*color: #000000;*/
            /*background: #4dad54;*/
            background: #003f00;
        }

        .player_flex_box {
            --bs-gutter-y: 0;
            flex: 0 1 25%;
            min-width: 0;
        }
        .team-col .player_flex_box {
            flex: 0 1 50%;
            min-width: 0;
        }
        .team-col {
            flex: 0 0 auto;
            width: 50%;
            margin-top:0;
        }
        .team-hr {
            display: none;
        }
        @media screen and (max-width: 1600px) {
            .player_flex_box {
                flex: 0 1 50%;
            }
            .team-col .player_flex_box {
                flex: 0 1 100%;
                min-width: 0;
            }
        }
        @media screen and (max-width: 992px) {
            .player_flex_box {
                flex: 0 1 100%;
            }

            .team-col {
                flex: 0 0 auto;
                width: 100%;
            }

            .team-hr {
                display: block;
            }
        }


        .team_name_1 {
            writing-mode: vertical-lr;
            text-align: center;
        }
        .team_name_2 {
            writing-mode: vertical-lr;
            transform: rotate(180deg);
            text-align: center;
        }

        .player_boxes {
            width: 100%;
        }

        .player_box {
            height: 80px;
            width: 80px;
            max-width: inherit;
            /*margin: 0px 2px 4px 2px;*/
            margin: 2px;
        }

            .player_box img {
                padding: .25rem;
                /*background-color: var(--bs-body-bg);
                border: var(--bs-border-width) solid var(--bs-border-color);
                border-radius: var(--bs-border-radius);*/
                /*max-width: 100%;
                height: auto;*/
                height:100%;
                width:100%;
            }

        .session_extra_panel {
            border-bottom: solid 1px;
            border-right: solid 1px;
            /*border-left: solid 1px;*/
            /*border-radius: 0 0 12px 12px;
            padding: 0 4px 4px 4px;*/
            /*border-radius: 12px;*/

            border-radius: 0 12px 12px 0;
            /*padding: 36px 4px 4px 295px;*/
            padding: 36px 4px 4px var(--section-mode-extra-left);
            margin-top: -36px;
            /*margin-left: -295px;*/
            margin-left: calc(0px - var(--section-mode-extra-left));
            /*position: relative;
            min-height: 277px;*/
            /*pmin-height: 280px;*/
            min-height: 269px;
            /*margin-left: -300px;
            padding-left: 304px;*/
            /*pointer-events: none;*/
            width: 100%;
        }

        .status_flags,
        .rules_list {
            /*flex: 0 0 83px;
            width: 83px;*/
            flex: 0 0 var(--section-icons-width);
            width: var(--section-icons-width);
            /*flex: 0 0 89px;
            width: 89px;*/
        }

            .rules_list > div,
            .rules_list > a {
                height: 25px;
                color: #000;
                background: gray;
                /*clip-path: polygon(2.5px 0, calc(100% - 2.5px) 0, 100% 2.5px, 100% calc(100% - 2.5px), calc(100% - 2.5px) 100%, 2.5px 100%, 0 calc(100% - 2.5px), 0 2.5px);*/
                /*text-align: center;*/
                /*font-size: small;
                line-height: 2em;*/
                font-weight: 1000;
                /*padding-left: 0.2rem;
                padding-right: 0.2rem;*/
                padding-left: 0.1rem;
                padding-right: 0.1rem;
                /*box-sizing: content-box;*/

                /*white-space: pre;*/
                flex: 0 1 100%;
                width: var(--section-icons-width);
            }

            .rules_list div.half,
            .rules_list a.half {
                flex: 0 1 calc(50% - 2px);
                width: calc((var(--section-icons-width) / 2) - 2px);
            }

            .rules_list div.on {
                color: #000000;
                background: #4dad54;
            }

            .rules_list div.off {
                color: #000;
                background: #fe5b5b;
            }

            .rules_list div.disabled {
                color: #fff;
                /*background: gray;*/
                opacity: 0.333;
            }

        /*.rules_list div.balance {
                color: #fff;
                background: rgb(0, 71, 207);
            }*/


        /*.flex-row {
            border: solid blue 1px;
        }
            .flex-row > div {
                border: solid yellow 1px;
            }
        .flex-column {
            border: solid red 1px;
        }
            .flex-column > div {
                border: solid yellow 1px;
            }*/

        #sessionList:not(.show_extra) .extra_row {
            display: none !important;
        }
        .session_name_box {
            display: none;
        }

        @media screen and (max-width: 768px) {
            /*.session_extra_panel {
                padding-left: 120px;
                margin-left: -120px;
            }*/

            /*.game_mode {
                width: 39px;
                clip-path: polygon(5px 0, 34px 0, 39px 5px, 39px 20px, 20px 39px, 5px 25px, 0 20px, 0 5px);
            }*/
            .game_mode {
                width: 25px;
                /*clip-path: polygon(2.5px 0, calc(100% - 2.5px) 0, 100% 2.5px, 100% calc(100% - 2.5px), calc(100% - 2.5px) 100%, 2.5px 100%, 0 calc(100% - 2.5px), 0 2.5px);*/
            }

            .nub {
                display: none;
            }

            .map_name {
                writing-mode: vertical-lr;
                /*height: 240px;*/
                height: 234px;
                /*margin: 0 !important;*/
                margin: 0;
                /*margin: 0 0 4px 0;*/
                width: 25px;
                line-height: 100%;
                transform: rotate(180deg);
            }

            .map_image {
                display: none;
            }

            /*.game_mode_cell,
            .map_cell {
                width: var(--section-mode-width-mini);
                flex: 0 0 var(--section-mode-width-mini);
            }*/

            .map_cell > .map_name {
                flex: 1 0 0;
            }
        }

        @media screen and (max-width: 992px) {
            /* if we're too thin and extra is open, hide the map name in the first row */
            #sessionList.show_extra .session_name_cell {
                display: none !important;
            }
            /* if we're too thin and extra is open, show the map name in the info section */
            #sessionList.show_extra .session_name_box {
                display: block;
            }
        }

        #sessionListLoading {
            transition: opacity 0.3s;
            will-change: opacity;
            opacity: 1;
        }
        #sessionListHeader:not(.loading) #sessionListLoading {
            opacity: 0;
            transition-delay: 0.5s; /* Delayed fade-out */
        }
    </style>

<div class="page-container">
    <div class="sidebar3" style="background-color: black !important;">

        <div id="sessionListHeader" class="header-bar header-bar-menu-pad">
            <div class="flex-grow-1 text-truncate">Battlezone 98 Redux - Multiplayer Session List</div>
            <div class="d-flex gap-1 float-end">
                <i id="sessionListLoading" class="text-primary fa fa-circle-o-notch fa-spin fa-2x fa-fw"></i>
                <button id="btnExtra" type="button" data-bs-toggle="button" aria-pressed="false" class="btn btn-sm btn-outline-primary">Thin</button>
                <button id="btnRefresh" type="button" class="btn btn-sm btn-outline-primary">Refresh</button>
            </div>
        </div>

        <div class="sidebar3-content" style="display: flex; flex-direction: column;">
            <div id="sessionList" class="container-fluid mt-2 flex-fill show_extra"></div>

            <footer class="border-top footer text-muted">
                <div class="container-fluid">
                    &copy; {{ date('Y') }} - MultiplayerSessionList
                </div>
            </footer>
        </div>
    </div>
</div>

    {{--<script src="/lib/jquery/dist/jquery.min.js"></script>--}}
    <!--<script src="/lib/bootstrap/dist/js/bootstrap.bundle.min.js"></script>-->
    {{--<script src="//cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>--}}
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>--}}

@endsection
