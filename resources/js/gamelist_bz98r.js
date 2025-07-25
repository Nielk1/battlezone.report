export function LoadGameListBZ98R() {
    function encodeAttr(text) {
        const elem = document.createElement('p');
        elem.setAttribute('title', text);
        const elemHtml = elem.outerHTML; // <p title="encodedText"> or maybe <p title='encodedText'>
        // Find out whether the browser used single or double quotes before encodedText
        const quote = elemHtml[elemHtml.search(/['"]/)];
        // Split up the generated HTML using the quote character; take item 1
        return elemHtml.split(new RegExp(quote))[1];
    }

    function escapeHtml(unsafe) {
        if (!unsafe)
            return unsafe;
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // local copy of list data
    var ListData = {};

    // parent data relations so we can walk up to the session when data updates come in
    var DataRefs = {};

    function isObject(item) {
        return (item && typeof item === 'object' && !Array.isArray(item));
    }

    // this function merges all the sources into the destination object, and returns the destination object
    // this preserves the destination object's reference, and modifies it in place, but it is also returned so you can use it with a coalesce of a default object
    function MergeIntoFirstObject(target, ...sources) {
        const source = sources.shift();
        if (source === undefined) return MergeIntoFirstObject(target, ...sources); // next source
        if (isObject(target) && isObject(source)) {
            for (const key in source) {
                if (isObject(source[key])) {
                    if (!target[key]) Object.assign(target, { [key]: {} });
                    MergeIntoFirstObject(target[key], source[key]);
                } else if (Array.isArray(source[key])) {
                    // assign the array, but then iterate it to look for those references
                    Object.assign(target, { [key]: source[key] });
                    for (var i = 0; i < source[key].length; i++) {
                        if (isObject(source[key][i])) {
                            MergeIntoFirstObject(target[key][i], source[key][i]);
                        }
                    }
                } else {
                    Object.assign(target, { [key]: source[key] });
                }
            }
        }

        // no sources left to merge, clean up references
        if (sources.length == 0)
            return target;

        return MergeIntoFirstObject(target, ...sources); // next source
    }

    function MergeReferences(target, parent_type, parent_id) {
        if (!isObject(target))
            return target;

        for (const key in target) {
            // Detect Object or Array
            if (isObject(target[key])) {
                if (target[key].$ref) {
                    // we're a ref object, so forget the destination entirely and just use the source's reference
                    var split = target[key].$ref.split('/');
                    let frag_type = split[1].replace('~1', '/').replace('~0', '~');
                    let frag_id = split[2].replace('~1', '/').replace('~0', '~');
                    target[key] = ListData[frag_type][frag_id];
                    if (!DataRefs[`${frag_type}\t${frag_id}`])
                        DataRefs[`${frag_type}\t${frag_id}`] = new Set();
                    DataRefs[`${frag_type}\t${frag_id}`].add(`${target.$type || parent_type}\t${target.$id || parent_id}`);
                } else {
                    MergeReferences(target[key], target.$type || parent_type, target.$id || parent_id);
                }
            } else if (Array.isArray(target[key])) {
                for (var i = 0; i < target[key].length; i++) {
                    if (isObject(target[key][i])) {
                        if (target[key][i].$ref) {
                            // we're a ref object, so forget the destination entirely and just use the source's reference
                            var split = target[key][i].$ref.split('/');
                            let frag_type = split[1].replace('~1', '/').replace('~0', '~');
                            let frag_id = split[2].replace('~1', '/').replace('~0', '~');
                            target[key][i] = ListData[frag_type][frag_id];
                            if (!DataRefs[`${frag_type}\t${frag_id}`])
                                DataRefs[`${frag_type}\t${frag_id}`] = new Set();
                            DataRefs[`${frag_type}\t${frag_id}`].add(`${target.$type || parent_type}\t${target.$id || parent_id}`);
                        } else {
                            MergeReferences(target[key][i], target.$type || parent_type, target.$id || parent_id);
                        }
                    }
                }
            }
        }
        return target;
    }

    function ExpandDataRefs($type, $id, memo) {
        let local_memo = memo || new Set();

        // if we already have this one end this recursion path
        if (local_memo.has(`${$type}\t${$id}`))
            return local_memo;

        // add ourself to the memo
        local_memo.add(`${$type}\t${$id}`)

        if (DataRefs[`${$type}\t${$id}`]) {
            for (let v of DataRefs[`${$type}\t${$id}`]) {
                let tmp = v.split('\t');
                ExpandDataRefs(tmp[0], tmp[1], local_memo);
            }
        }

        // return the memo as it's all our unique keys
        return local_memo;
    }

    var GetGamesAjax = null;
    document.getElementById("btnExtra").addEventListener('click', function (e) {
        e.preventDefault();
        const btnExtra = document.getElementById('btnExtra');
        const lobbyList = document.getElementById('lobbyList');
        if (btnExtra.classList.contains('active')) {
            lobbyList.classList.remove('show_extra');
        } else {
            lobbyList.classList.add('show_extra');
        }
    });
    document.getElementById("btnRefresh").addEventListener('click', function (e) {
        e.preventDefault();

        const lobbyList = document.getElementById('lobbyList');
        lobbyList.innerHTML = '';

        if (GetGamesAjax != null) {
            GetGamesAjax.abort();
        }

        // forget any pending datums from a prior refresh
        debouncingDatums = false;
        debouncingSet = new Set();
        if (debouncingTimeout >= 0) {
            clearTimeout(debouncingTimeout);
            console.log("ABORT PENDING POOLING");
        }
        debouncingTimeout = -1;

        DataRefs = {};

        var windowSearch = window.location.search;
        if (windowSearch.length > 0)
            windowSearch = '&' + windowSearch.substring(1);

        // what ugly shit this is
        var keys = Array.from(document.querySelectorAll('#dropdownMenuContainer .dropdown-item.active'))
            .map(function(elem) { return 'game=' + elem.dataset.value; })
            .join('&');

        GetGamesAjax = new XMLHttpRequest();
        GetGamesAjax.open("GET", '/api/games/sessions?game=bigboat:battlezone_98_redux' + windowSearch);
        var last_index = 0;
        GetGamesAjax.onprogress = function () {
            var UpdatedThisPass = new Set();

            var end = 0;
            while ((end = GetGamesAjax.responseText.indexOf('\n', last_index)) > -1) {
                var s = GetGamesAjax.responseText.substring(last_index, end + 1);

                var data = JSON.parse(s);
                if (data.$type == 'debug') {
                    console.log(data.$data);
                } else {
                    ListData[data.$type] = ListData[data.$type] || {};
                    if (data.$type == 'default') {
                        // defaults are wonky, no need to copy their ID and Type back into them as it would just break things reading from them
                        ListData[data.$type][data.$id] = MergeReferences(MergeIntoFirstObject(ListData[data.$type][data.$id] || {}, data.$data));
                    } else {
                        // for everything that isn't a default or a debug copy the $type and $id into it so we have an easier time
                        ListData[data.$type][data.$id] = MergeReferences(MergeIntoFirstObject(ListData[data.$type][data.$id] || {}, { $id: data.$id, $type: data.$type }, (ListData['default'] || {})[data.$type], data.$data));
                    }
                    switch (data.$type) {
                        // tokens we don't care about, even if they updated the composite data (we aren't ever rendering them)
                        //case 'source':
                        //    break;
                        default:
                            UpdatedThisPass.add(`${data.$type}\t${data.$id}`);
                            break;
                    }
                }

                last_index = end + 1;
            }
            if (UpdatedThisPass.size > 0)
                UpdateSessionListWithDataFragments(ListData, UpdatedThisPass);
        };
        GetGamesAjax.onload = function () {

        }
        ListData = {};
        GetGamesAjax.send();
    });

    var parent = document.querySelector('#lobbyList');

    function GenerateHtml_IdentitySteam(identity) {
        let platform_name = identity.nickname || identity.$id;
        let platform_profile = identity.profile_url;
        if (platform_profile) {
            return `<a href="${platform_profile}" title="${encodeAttr(platform_name)}" target="_blank" rel="noopener noreferrer" class="chamfer icon icon_steam text-decoration-none me-1" data-id="${encodeAttr(identity.$id)}" data-type="${encodeAttr(identity.$type)}"><i class="fa-brands fa-steam-symbol"></i></a>`;
        } else {
            return `<div title="Steam" class="chamfer icon icon_steam me-1" data-id="${encodeAttr(identity.$id)}" data-type="${encodeAttr(identity.$type)}"><i class="fa-brands fa-steam-symbol"></i></div>`;
        }
    }

    function GenerateHtml_IdentityGog(identity) {
        let platform_name = identity.username || identity.$id;
        let platform_profile = identity.profile_url;
        if (platform_profile) {
            //return `<a href="${platform_profile}" title="${encodeAttr(platform_name)}" target="_blank" rel="noopener noreferrer" class="chamfer icon icon_gog text-decoration-none me-1" data-id="${encodeAttr(identity.$id)}" data-type="${encodeAttr(identity.$type)}"><i class="icon icon-gog" aria-hidden="true" title="GOG"></i></a>`;
            return `<a href="${platform_profile}" title="${encodeAttr(platform_name)}" target="_blank" rel="noopener noreferrer" class="chamfer icon icon_gog text-decoration-none me-1" data-id="${encodeAttr(identity.$id)}" data-type="${encodeAttr(identity.$type)}"><img src="/img/gog-icon.svg" class="icon" aria-hidden="true" title="GOG"></img></a>`;
        } else {
            //return `<div title="Steam" class="chamfer icon icon_gog me-1" data-id="${encodeAttr(identity.$id)}" data-type="${encodeAttr(identity.$type)}"><i class="icon icon-gog" aria-hidden="true" title="GOG"></i></div>`;
            return `<div title="Steam" class="chamfer icon icon_gog me-1" data-id="${encodeAttr(identity.$id)}" data-type="${encodeAttr(identity.$type)}"><img src="/img/gog-icon.svg" class="icon" aria-hidden="true" title="GOG"></img></div>`;
        }
    }

    function GenerateHtml_Player(player, is_leader, is_over_limit, teamed, game_type_id) {
        let plaform_avatar = '';
        let player_avatar_source = '';
        let player_avatar_id = '';
        for (let id in player.ids) {
            if (id == "steam") {
                plaform_avatar = (player.ids[id].identity || {}).avatar_url || plaform_avatar;
                player_avatar_id = player.ids[id].identity.$id;
                player_avatar_source = id;
                break;
            }
            if (id == "gog") {
                plaform_avatar = (player.ids[id].identity || {}).avatar_url || plaform_avatar;
                player_avatar_id = player.ids[id].identity.$id;
                player_avatar_source = id;
                break;
            }
        }

        let playerHtmlEntries = '';
        //<div class="chamfer d-flex" style="background:black;flex:100% 0 0;">
        playerHtmlEntries += `
    <div data-path="player" data-type="player" data-id="${encodeAttr(player.$id ?? "")}" data-extra-leader="${is_leader ? true : false}" data-extra-overlimit="${is_over_limit ? true : false}">
    <div class="player_info_box chamfer d-flex flex-row flex-nowrap ${teamed ? '' : 'mb-1'}" style="padding:2px;">
        <div class="chamfer d-flex" style="background:black;flex:100% 0 0;min-width:0;">
            <div class="d-flex m-1" style="flex: 0 0 50px;width:50px;">
                <img data-path="avatar" data-source="${encodeAttr(player_avatar_source)}" data-id="${encodeAttr(player_avatar_id)}" src="${encodeAttr(plaform_avatar)}" width="150" height="150" onerror="this.src = '/images/no_steam_pfp.jpg'" class="img-fluid chamfer">
            </div>
            <div class="d-flex flex-column" style="flex: 0 1 100%;min-width:0;">
                <div class="text-truncate">${escapeHtml(player.name)}</div>`;

        playerHtmlEntries += `
            <div class="d-flex flex-row">
                ${is_leader ? '<div title="Leader" class="chamfer icon icon_leader me-1"><i class="fa-solid fa-crown"></i></div>' : ''}
                ${player.is_host ? '<div title="Host" class="chamfer icon icon_host me-1"><i class="fa-solid fa-server"></i></div>' : ''}`;

        for (let id in player.ids) {
            switch (id) {
                case "slot":
                    //{
                    //    let platform_id = '' + player.ids[id].id;
                    //    playerHtmlEntries += `<div class="text-truncate"><i class="icon icon-hash" aria-hidden="true" title="Slot"></i> ${escapeHtml(platform_id)}</a></div>`;
                    //}
                    break;
                case "bzr_net":
                    //{
                    //    let platform_id = '' + player.ids[id].id;
                    //    playerHtmlEntries += `<div class="text-truncate"><i class="fa fa-gamepad" aria-hidden="true" title="BZR-Net"></i> ${escapeHtml(platform_id)}</a></div>`;
                    //}
                    break;
                case "steam":
                    {
                        playerHtmlEntries += GenerateHtml_IdentitySteam(player.ids[id].identity);
                    }
                    break;
                case "gog":
                    {
                        playerHtmlEntries += GenerateHtml_IdentityGog(player.ids[id].identity);
                    }
                    break;
                default:
                    break;
            }
        }

        playerHtmlEntries += `</div>`;

        //if (player.hero) {
        //    playerHtmlEntries += `<div class="text-truncate"><i class="fa fa-user-circle-o" aria-hidden="true" title="Hero"></i> <span data-path="hero_name" data-type="hero" data-id="${encodeAttr(player.hero.$id)}">${escapeHtml(player.hero.name || player.hero.$id)}</span></div>`;
        //}

        //if (player.stats)
        //    playerHtmlEntries += GenerateHtml_MicroList("Stats", player.stats, true);
        //if (player.other)
        //    playerHtmlEntries += GenerateHtml_MicroList("Other", player.other, true);

        playerHtmlEntries += `</div>`

        if (player.hero) {
            if (game_type_id != null && game_type_id.split(':').at(-1) == "STRAT") {
                // it's a strat, so show faction instead
                let faction = player.hero.faction;
                if (faction) {
                    playerHtmlEntries += `
                    <div class="d-flex m-1" style="flex: 0 0 50px;width:50px;">
                        <img data-path="faction_name" data-type="faction" data-id="${encodeAttr(faction.$id)}" title="${encodeAttr(faction.name || faction.$id)}" src="${encodeAttr(faction.block) }" width="150" height="150" onerror="this.src = '/images/no_steam_pfp.jpg'" class="img-fluid chamfer">
                    </div>`;
                }  else {
                    playerHtmlEntries += `<div class="d-flex m-1 d-block chamfer" style="flex: 0 0 50px;width:50px;background:green;"></div>`;
                }
            } else {
                playerHtmlEntries += `
                <div class="d-flex m-1" style="flex: 0 0 50px;width:50px;">
                    <img data-path="hero_name" data-type="hero" data-id="${encodeAttr(player.hero.$id)}" title="${encodeAttr(player.hero.name || player.hero.$id)}" src="/images/no_steam_pfp.jpg" width="150" height="150" onerror="this.src = '/images/no_steam_pfp.jpg'" class="img-fluid chamfer">
                </div>`;
            }
        } else {
            playerHtmlEntries += `<div class="d-flex m-1 d-block chamfer" style="flex: 0 0 50px;width:50px;background:green;"></div>`;
        }

        playerHtmlEntries += `
    </div>
    </div>
    </div>`;
        return playerHtmlEntries;
    }

    function CreateOrUpdateSessionDom($id, data) {
        var session = data.session[$id];
        var sessionDom = parent.querySelector(`#\\/session\\/${CSS.escape($id)}`);
        var sessionDom2 = parent.querySelector(`#\\/session\\/${CSS.escape($id)}\\/row2`);

        if (!sessionDom) {
            parent.insertAdjacentHTML('beforeend', `<div class="mb-1 d-flex flex-row flex-nowrap" id="/session/${encodeAttr($id)}">
                <div class="me-1 status_flags d-flex flex-row justify-content-between">
                    <div>
                        <div data-path="session.status.is_locked_off" class="icon"></div>
                        <div title="Locked" data-path="session.status.is_locked" class="chamfer icon icon_lock" style="display:none;"><i class="fa-solid fa-lock"></i></div>
                        <div title="Effectively Locked via Sync Join" data-path="session.status.other.sync_too_late" class="chamfer icon icon_sync_lock" style="display:none;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    </div>
                    <div>
                        <div data-path="session.status.has_password_off" class="icon"></div>
                        <div title="Password" data-path="session.status.has_password" class="chamfer icon icon_password" style="display:none;"><i class="fa-solid fa-key"></i></div>
                    </div>
                    <div>
                        <div data-path="session.other.sync_join_off" class="icon"></div>
                        <div title="Sync Join" data-path="session.other.sync_join" class="chamfer icon icon_sync_join" style="display:none;"><i class="fa-solid fa-chain"></i></div>
                        <div title="Scripted Sync" data-path="session.other.sync_script" class="chamfer icon icon_sync_script" style="display:none;"><i class="fa-solid fa-handshake-angle"></i></div>
                    </div>
                    <div>
                        <div data-path="session.game.mod.off" class="icon"></div>
                        <a title="Modded" data-path="session.game.mod" target="_blank" rel="noopener noreferrer" class="chamfer icon icon_mod text-decoration-none" style="display:none;"><i class="fa-solid fa-screwdriver-wrench"></i></a>
                    </div>
                    <div>
                        <div data-path="session.status.state.none" class="icon"></div>
                        <div title="Pre-Game"  data-path="session.status.state.pre_game"  class="chamfer icon icon_state_pregame"  style="display:none;"><i class="fa-solid fa-hourglass"></i></div>
                        <div title="In Game"   data-path="session.status.state.in_game"   class="chamfer icon icon_state_ingame"   style="display:none;"><i class="fa-solid fa-play"></i></div>
                        <div title="Post-Game" data-path="session.status.state.post_game" class="chamfer icon icon_state_postgame" style="display:none;"><i class="fa-solid fa-flag-checkered"></i></div>
                    </div>
                </div>
                <div class="me-1 d-flex game_mode_cell">
                    <div class="d-flex flex-row">
                        <div class="chamfer game_mode d-flex flex-row" data-path="session.level.game_mode">
                            <div class="nub" style="border-right:solid black 2px; box-sizing: content-box;" data-path="session.level.game_type"></div>
                            <div class="game_mode_icon" data-path="session.level.game_mode.icon"></div>
                            <span class="game_mode_text ps-1 pe-1 text-truncate" data-path="session.level.game_mode.text"></span>
                        </div>
                    </div>
                </div>
                <div style="flex:0 1 50%;" class="me-1 d-flex flex-grow-1 text-truncate"><span class="text-truncate" data-path="session.level.map"></span></div>
                <div style="flex:0 1 50%;" class="me-1 d-flex flex-grow-1 text-truncate session_name_cell"><span class="text-truncate" data-path="session.name"></span></div>
                <div style="flex:0 0 0;" class="me-1 text-nowrap bg-transparent text-end" data-path="session.player_count"></div>
            </div>
            <div class="mb-2 d-flex flex-row flex-nowrap extra_row" id="/session/${encodeAttr($id)}/row2">
                <div class="me-1 rules_list d-flex align-content-start justify-content-between flex-wrap" data-path="session.level.rules"></div>
                <div class="map_cell d-flex me-1 flex-column text-center">
                    <div class="map_image chamfer bg-secondary mb-1">
                        <div style="background: black; left: 1px; top: 1px; height: calc(100% - 2px); width: calc(100% - 2px); position: relative;" class="chamfer">
                            <div class="ratio ratio-1x1" style="--bs-aspect-ratio: calc(100% + 2px);top: -1px;">
                                <img class="chamfer" data-path="session.level.map.image" width="186" length="186" src onerror="this.src='/images/no_steam_pfp.jpg'" class="img-thumbnail" style="width:calc(100% - 12px);height:auto;margin:auto;bottom:0;top:0;left:0;right:0;">
                            </div>
                        </div>
                    </div>
                    <div class="map_name mb-1 p-1 bg-body-tertiary chamfer text-truncate small">
                        <small data-path="session.level.map.map_file" class="card-subtitle text-body-secondary" style="white-space:pre;"></small>
                    </div>
                </div>
                <div class="session_extra_panel" style="flex:3 0 0;overflow-x:hidden;">
                    <div class="session_name_box text-truncate"><span class="text-truncate" data-path="session.name"></span></div>
                    <div data-path="players" class="mt-0 d-flex flex-row flex-wrap justify-content-around row g-1"></div>
                </div>
            </div>`);
            //<div data-path="players" class="row g-0" style="margin-right: -.25rem !important;"></div>
            //<div class="icon" style="border-right:solid black 2px; box-sizing: content-box;" data-path="session.level.game_type"></div>
            sessionDom2 = parent.lastElementChild;
            sessionDom = sessionDom2.previousElementSibling;
            //sessionDom = parent.lastElementChild;
        }

        // Map Image
        sessionDom2.querySelector('[data-path="session.level.map.image"]').setAttribute("src", session.level?.map?.image ?? "");

        // Map Filename
        sessionDom2.querySelector('[data-path="session.level.map.map_file"]').textContent = session.level?.map?.map_file ?? " ";

        // Map Name
        //let map_name_elem = sessionDom.querySelector('[data-path="session.level.map.name"]');
        //map_name_elem.setAttribute('title', session.level?.map?.map_file);
        //map_name_elem.textContent = session.level?.map?.name ?? session.level?.map?.map_file ?? '';

        // Game Version
        //sessionDom.querySelector('[data-path="session.game.version"]').textContent = session.game?.version || " ";

        // Session Name
        let sessionNameElem = sessionDom.querySelector('[data-path="session.name"]');
        let sessionNameElem2 = sessionDom2.querySelector('[data-path="session.name"]');
        if (session.name && session.name.trim().length > 0) {
            sessionNameElem.textContent = session.name;
            sessionNameElem.style.fontStyle = null;
            sessionNameElem2.textContent = session.name;
            sessionNameElem2.style.fontStyle = null;
        } else {
            sessionNameElem.textContent = "<INTENTIONALLY BLANK>";
            sessionNameElem.style.fontStyle = "italic";
            sessionNameElem2.textContent = "<INTENTIONALLY BLANK>";
            sessionNameElem2.style.fontStyle = "italic";
        }

        // Map Name
        let sessionMapNameElem = sessionDom.querySelector('[data-path="session.level.map"]');
        if (session?.level?.map?.name) {
            sessionMapNameElem.textContent = session?.level?.map?.name;
            sessionMapNameElem.style.fontStyle = null;
        } else {
            sessionMapNameElem.textContent = session?.level?.map?.map_file;
            sessionMapNameElem.style.fontStyle = "italic";
        }

        // Session Message
        //sessionDom.querySelector('[data-path="session.message"]').textContent = session.message || " ";

        // Locked
        if (session?.status?.other?.sync_too_late ?? false) {
            sessionDom.querySelector('[data-path="session.status.is_locked_off"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.status.is_locked"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.status.other.sync_too_late"]').style.display = "block";
        }
        else if (session?.status?.is_locked ?? false) {
            sessionDom.querySelector('[data-path="session.status.is_locked_off"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.status.other.sync_too_late"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.status.is_locked"]').style.display = "block";
        }
        else
        {
            sessionDom.querySelector('[data-path="session.status.is_locked"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.status.other.sync_too_late"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.status.is_locked_off"]').style.display = "block";
        }

        // Password
        if (session?.status?.has_password ?? false) {
            sessionDom.querySelector('[data-path="session.status.has_password_off"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.status.has_password"]').style.display = "block";
        }
        else
        {
            sessionDom.querySelector('[data-path="session.status.has_password"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.status.has_password_off"]').style.display = "block";
        }

        // Sync Join
        if (session?.other?.sync_join ?? false) {
            sessionDom.querySelector('[data-path="session.other.sync_join_off"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.other.sync_join"]').style.display = "block";
            sessionDom.querySelector('[data-path="session.other.sync_script"]').style.display = "none";
        }
        else if (session?.other?.sync_script ?? false) {
            sessionDom.querySelector('[data-path="session.other.sync_join_off"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.other.sync_join"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.other.sync_script"]').style.display = "block";
        }
        else
        {
            sessionDom.querySelector('[data-path="session.other.sync_join_off"]').style.display = "block";
            sessionDom.querySelector('[data-path="session.other.sync_join"]').style.display = "none";
            sessionDom.querySelector('[data-path="session.other.sync_script"]').style.display = "none";
        }

        // State
        switch (session.status.state) {
            case "pre_game":
                sessionDom.querySelector('[data-path="session.status.state.none"]').style.display = "none";
                sessionDom.querySelector('[data-path="session.status.state.pre_game"]').style.display = "block";
                sessionDom.querySelector('[data-path="session.status.state.in_game"]').style.display = "none";
                sessionDom.querySelector('[data-path="session.status.state.post_game"]').style.display = "none";
                break;
            case "in_game":
                sessionDom.querySelector('[data-path="session.status.state.none"]').style.display = "none";
                sessionDom.querySelector('[data-path="session.status.state.pre_game"]').style.display = "none";
                sessionDom.querySelector('[data-path="session.status.state.in_game"]').style.display = "block";
                sessionDom.querySelector('[data-path="session.status.state.post_game"]').style.display = "none";
                break;
            case "post_game":
                sessionDom.querySelector('[data-path="session.status.state.none"]').style.display = "none";
                sessionDom.querySelector('[data-path="session.status.state.pre_game"]').style.display = "none";
                sessionDom.querySelector('[data-path="session.status.state.in_game"]').style.display = "none";
                sessionDom.querySelector('[data-path="session.status.state.post_game"]').style.display = "block";
                break;
            default:
                sessionDom.querySelector('[data-path="session.status.state.none"]').style.display = "block";
                sessionDom.querySelector('[data-path="session.status.state.pre_game"]').style.display = "none";
                sessionDom.querySelector('[data-path="session.status.state.in_game"]').style.display = "none";
                sessionDom.querySelector('[data-path="session.status.state.post_game"]').style.display = "none";
                break;
        }

        let game_mods = [];
        if (session.game?.mod || (session.game?.mods?.length ?? 0) > 0) {
            if (session.game?.mod) {
                game_mods.push(session.game.mod);
            }
            else {
                game_mods = session.game.mods;
            }
        }
        if (game_mods.length > 0) {
            sessionDom.querySelector('[data-path="session.game.mod.off"]').style.display = "none";
            let modIconDom = sessionDom.querySelector('[data-path="session.game.mod"]');
            modIconDom.style.display = "block";
            modIconDom.setAttribute('title', game_mods[0].name);
            modIconDom.setAttribute('href', game_mods[0].url);
        }
        else
        {
            sessionDom.querySelector('[data-path="session.game.mod.off"]').style.display = "block";
            sessionDom.querySelector('[data-path="session.game.mod"]').style.display = "none";
        }


        // Session Game Type
        let game_type_element = sessionDom.querySelector(`[data-path="session.level.game_type"]`);
        let game_type_id =
            session.level?.rules?.game_type?.$id ??
            session.level?.map?.game_type?.$id ??
            session.level?.game_type?.$id
        game_type_element.setAttribute("title",
            session.level?.rules?.game_type?.name ?? session.level?.rules?.game_type?.$id ??
            session.level?.map?.game_type?.name ?? session.level?.map?.game_type?.$id ??
            session.level?.game_type?.name ?? session.level?.game_type?.$id ?? "Unknown");
        let game_type_icon =
            session.level?.rules?.game_type?.icon ??
            session.level?.map?.game_type?.icon ??
            session.level?.game_type?.icon ?? null;
        let game_type_color =
            session.level?.rules?.game_type?.color ??
            session.level?.map?.game_type?.color ??
            session.level?.game_type?.color ?? null;
        if (game_type_color) {
            game_type_element.style.backgroundColor = game_type_color;
        } else {
            game_type_element.style.backgroundColor = "#101010";
        }
        //if (game_type_icon) {
        //    game_type_element.style.backgroundImage = `url("${game_type_icon}")`;
        //} else {
        //    game_type_element.style.backgroundImage = 'url("//gamelistassets.iondriver.com/bz98r/resources/icon_unknown.png")';
        //}

        // Session Game Mode
        let game_mode_element = sessionDom.querySelector(`[data-path="session.level.game_mode"]`);
        let game_mode_icon_element = sessionDom.querySelector(`[data-path="session.level.game_mode.icon"]`);
        let game_mode_text_element = sessionDom.querySelector(`[data-path="session.level.game_mode.text"]`);
        game_mode_element.setAttribute("title",
            session.level?.rules?.game_mode?.name ?? session.level?.rules?.game_mode?.$id ??
            session.level?.map?.game_mode?.name ?? session.level?.map?.game_mode?.$id ??
            session.level?.game_mode?.name ?? session.level?.game_mode?.$id ?? "Unknown");
        let game_mode_text_string =
            session.level?.rules?.game_mode?.name ?? session.level?.rules?.game_mode?.$id ??
            session.level?.map?.game_mode?.name ?? session.level?.map?.game_mode?.$id ??
            session.level?.game_mode?.name ?? session.level?.game_mode?.$id ?? "Unknown";
        game_mode_text_element.innerHTML = game_mode_text_string.split(/(?<=[A-Z])(?=[a-z ])|(?<=[a-z ])(?=[A-Z])/).map(i => `<span class="${i == i.toLowerCase() ? 'smallcap' : 'bigcap'}">${escapeHtml(i)}</span>`).join('');
        let game_mode_icon =
            session.level?.rules?.game_mode?.icon ??
            session.level?.map?.game_mode?.icon ??
            session.level?.game_mode?.icon ?? null;
        let game_mode_color =
            session.level?.rules?.game_mode?.color ??
            session.level?.map?.game_mode?.color ??
            session.level?.game_mode?.color ?? null;
        if (game_mode_color) {
            game_mode_element.style.backgroundColor = game_mode_color;
            game_mode_element.style.backgroundImage = "";
        } else if (game_mode_icon) {
            game_mode_element.style.backgroundImage = `url("${game_mode_icon}")`; // do coloring via icon image
            game_mode_element.style.backgroundColor = "";
        } else {
            game_mode_element.style.backgroundImage = "";
            game_mode_element.style.backgroundColor = "#101010";
        }
        if (game_mode_icon) {
            game_mode_icon_element.style.backgroundImage = `url("${game_mode_icon}")`;
        } else {
            game_mode_icon_element.style.backgroundImage = 'url("//gamelistassets.iondriver.com/bz98r/resources/icon_unknown.png")';
        }

        // Session Level Rules
        // +---------------+
        // |    Version    |
        // +---------------+
        // |    Balance    |
        // +---------------+
        // |      Mod      |
        // +-------+-------+
        // | Lives | Kills |
        // +-------+-------+
        // |     Time      |
        // +-------+-------+
        // |  Barr |  Sat  |
        // +-------+-------+
        // |  Snip | Splnt |
        // +-------+-------+
        let sessionRulesElem = sessionDom2.querySelector(`[data-path="session.level.rules"]`);
        if (session.level.rules) {
            let htmlEntries = '';

            // Game Version
            {
                let game_version = session.game?.version;
                if (game_version != null)
                    htmlEntries += `<div class="rule_game_version chamfer d-flex flex-row mb-1" style="white-space:pre;" title="Game Version ${encodeAttr(game_version)}"><div class="icon"><i class="fa-solid fa-gamepad"></i></div><div class="flex-grow-1 text-center">${escapeHtml(game_version)}</div></div>`;
            }

            // Session Game Balance
            {
                let game_balance_data =
                    session.level?.rules?.game_balance ??
                    session.level?.map?.game_balance ??
                    session.level?.game_balance ??
                    session.game?.game_balance;
                if (game_balance_data != null) {
                    let game_balance_display_text = game_balance_data?.name ?? game_balance_data?.$id;
                    if (game_balance_data?.abbr != null && game_balance_display_text.length > 7) {
                        if (game_balance_data.abbr.length <= 7)
                            game_balance_display_text = game_balance_data.abbr;
                    }

                    htmlEntries += `<div class="rule_balance chamfer d-flex flex-row mb-1" style="white-space:pre;" title="${encodeAttr(game_balance_data.note ?? `Balance: ${game_balance_data?.name ?? game_balance_data?.abbr ?? game_balance_data?.$id}`)}"><div class="icon"><i class="fa-solid fa-scale-balanced"></i></div><div class="flex-grow-1 text-center">${escapeHtml(game_balance_display_text)}</div></div>`;
                } else {
                    htmlEntries += `<div class="rule_balance chamfer d-flex flex-row mb-1 disabled" style="white-space:pre;"></div>`;
                }
            }

            // game mod(s)
            {
                for (let mod of game_mods) {
                    htmlEntries += `<a href="${encodeAttr(mod.url)}" target="_blank" rel="noopener noreferrer" class="text-decoration-none rule_game_mod chamfer d-flex flex-row mb-1" style="white-space:pre;" title="${encodeAttr(mod?.abbr ?? mod?.name ?? mod?.$id)}"><div class="icon"><i class="fa-solid fa-screwdriver-wrench"></i></div><div class="flex-grow-1 text-center text-truncate"><span class="text-truncate" data-path="session.name">${escapeHtml(mod?.abbr ?? mod?.name ?? mod?.$id)}</span></div></a>`;
                }
            }

            // lives and kill_limit
            {
                let rule_lives = session.level?.rules?.["lives"];
                let rule_kill_limit = session.level?.rules?.["kill_limit"];
                if (rule_lives != null || rule_kill_limit != null) {
                    if (rule_lives != null) {
                        htmlEntries += `<div class="chamfer d-flex flex-row mb-1 half" title="${encodeAttr('' + rule_lives)} Lives"><div class="icon"><i class="fa-solid fa-user-group"></i></div><div class="flex-grow-1 text-center">${escapeHtml('' + rule_lives)}</div></div>`;
                    } else {
                        htmlEntries += `<div class="chamfer d-flex flex-row mb-1 half disabled"></div>`;
                    }
                    if (rule_kill_limit != null) {
                        htmlEntries += `<div class="chamfer d-flex flex-row mb-1 half" title="${encodeAttr('' + rule_kill_limit)} Kills"><div class="icon"><i class="fa-solid fa-skull"></i></div><div class="flex-grow-1 text-center">${escapeHtml('' + rule_kill_limit)}</div></div>`;
                    } else {
                        htmlEntries += `<div class="chamfer d-flex flex-row mb-1 half disabled"></div>`;
                    }
                }
            }

            // time_limit
            {
                let rule_time_limit = session.level?.rules?.["time_limit"];
                if (rule_time_limit != null) {
                    htmlEntries += `<div class="chamfer d-flex flex-row mb-1" title="Time Limit ${rule_time_limit} Minutes"><div class="icon"><i class="fa-solid fa-stopwatch" ></i></div><div class="flex-grow-1 text-center">${escapeHtml(`${Math.floor(rule_time_limit / 60)}:${String(rule_time_limit % 60).padStart(2, '0')}`)}</div></div>`;
                } else {
                    htmlEntries += `<div class="chamfer d-flex flex-row mb-1 disabled"></div>`;
                }
            }

            // barracks and satellite
            {
                let rule_satellite = session.level?.rules?.["satellite"];
                let rule_barracks = session.level?.rules?.["barracks"];
                if (rule_satellite != null || rule_barracks != null) {
                    if (rule_satellite != null) {
                        htmlEntries += `<div class="${rule_satellite ? "on" : "off"} chamfer d-flex flex-row mb-1 half" title="Satellite ${rule_satellite ? "On" : "Off"}"><div class="icon"><i class="fa-solid fa-satellite" ></i></div><div class="flex-grow-1 text-center">${rule_satellite ? 'ON' : 'OFF'}</div></div>`;
                    } else {
                        htmlEntries += `<div class="chamfer d-flex flex-row mb-1 half disabled"></div>`;
                    }
                    if (rule_barracks != null) {
                        htmlEntries += `<div class="${rule_barracks ? "on" : "off"} chamfer d-flex flex-row mb-1 half" title="Barracks ${rule_barracks ? "On" : "Off"}"><div class="icon"><i class="fa-solid fa-tent"></i></div><div class="flex-grow-1 text-center">${rule_barracks ? 'ON' : 'OFF'}</div></div>`;
                    } else {
                        htmlEntries += `<div class="chamfer d-flex flex-row mb-1 half disabled"></div>`;
                    }
                }
            }

            // sniper and splinter
            {
                let rule_sniper = session.level?.rules?.["sniper"];
                let rule_splinter = session.level?.rules?.["splinter"];
                if (rule_sniper != null || rule_splinter != null) {
                    if (rule_sniper != null) {
                        htmlEntries += `<div class="${rule_sniper ? "on" : "off"} chamfer d-flex flex-row mb-1 half" title="Sniper ${rule_sniper ? "On" : "Off"}"><div class="icon"><i class="fa-solid fa-cust-rifle"></i></div><div class="flex-grow-1 text-center">${rule_sniper ? 'ON' : 'OFF'}</div></div>`;
                    } else {
                        htmlEntries += `<div class="chamfer d-flex flex-row mb-1 half disabled"></div>`;
                    }
                    if (rule_splinter != null) {
                        htmlEntries += `<div class="${rule_splinter ? "on" : "off"} chamfer d-flex flex-row mb-1 half" title="Splinter ${rule_splinter ? "On" : "Off"}"><div class="icon"><i class="fa-solid fa-explosion" ></i></div><div class="flex-grow-1 text-center">${rule_splinter ? 'ON' : 'OFF'}</div></div>`;
                    } else {
                        htmlEntries += `<div class="chamfer d-flex flex-row mb-1 half disabled"></div>`;
                    }
                }
            }

            // other rules we missed
            for (let prop in session.level.rules) {
                if (session.level.rules[prop] != null) {
                    switch (prop) {
                        case 'lives':
                        case 'kill_limit':
                        case 'time_limit':
                        case 'satellite':
                        case 'barracks':
                        case 'sniper':
                        case 'splinter':
                            break;
                        default:
                            htmlEntries += `<div class="text-truncate text-center chamfer d-flex flex-row mb-1" title="${encodeAttr(prop)}">${escapeHtml('' + session.level.rules[prop])}</div>`;
                            break;
                    }
                }
            }

            sessionRulesElem.innerHTML = htmlEntries;

        } else {
            sessionRulesElem.innerHTML = '';
        }


        // Session Time
        //if (session.time) {
        //    let timeElem = sessionDom.querySelector(`[data-path="session.time"]`);
        //
        //    // minute resolutions
        //    if (session.time.resolution == 60) {
        //        timeElem.textContent = `(${session.time.context ? `${session.time.context} for ` : ''}${session.time.seconds / 60}${session.time.max ? '+' : ''} minutes)`;
        //    } else {
        //        timeElem.textContent = `(${session.time.context ? `${session.time.context} for ` : ''}${session.time.seconds}${session.time.max ? '+' : ''} seconds)`;
        //    }
        //}

        // Session Status
        //if (session.status?.state) {
        //    let timeElem = sessionDom.querySelector(`[data-path="session.status.state"]`);
        //    timeElem.textContent = session.status.state;
        //}

        // Player Count Text
        if (session.player_types != null) {
            let player_count_string = '';
            for (let k = 0; k < session.player_types.length; k++) {
                if (k > 0 && playerTypePad)
                    player_count_string += ',';
                for (let L = 0; L < session.player_types[k].types.length; L++) {
                    let playerType = session.player_types[k].types[L];
                    let playerTypeHtml = null;
                    let playerTypePad = false;
                    switch (playerType) {
                        case "player": playerTypeHtml = '<i title="Players" class="bi bi-person-fill"></i>'; break;
                        case "spectator": playerTypeHtml = '<i title="Spectators" class="bi bi-camera-video-fill"></i>'; break;
                        case "audience": playerTypeHtml = '<i title="Audience" class="fa fa-users" aria-hidden="true"></i>'; break;
                        default: playerTypeHtml = ' ' + playerType; playerTypePad = true; break;
                    }
                    if (L > 0 && playerTypePad)
                        player_count_string += ' ';
                    let count = session.player_count[playerType] + 0;
                    if (count > 19) {
                        player_count_string += `<span>${count}</span>${playerTypeHtml}`;
                    } else {
                        player_count_string += `<span class="player_number">${count}</span>${playerTypeHtml}`;
                    }
                }
                let count_max = session.player_types[k].max;
                if (count_max) {
                    if (count_max > 19) {
                        player_count_string += `/<span>${session.player_types[k].max}</span>`;
                    } else {
                        player_count_string += `/<span class="player_number">${session.player_types[k].max}</span>`;
                    }
                }
            }
            sessionDom.querySelector(`[data-path="session.player_count"]`).innerHTML = player_count_string;
        }

        // Players
        {
            function microSort(a, b) {
                if (a < b) return -1;
                if (a > b) return 1;
                return 0;
            }
            let Teams = new Set();
            for (var i = 0; i < session.players.length; i++) {
                if (session.players[i].team) {
                    Teams.add(session.players[i].team.id);
                } else {
                    Teams.add(null);
                }
            }
            if (session.teams) {
                for (var t in session.teams) {
                    Teams.add(t);
                }
            }
            if (session.level?.map?.teams) {
                for (var t in session.level.map.teams) {
                    Teams.add(t);
                }
            }
            Teams = Array.from(Teams).sort();
            let team_balance = -1;
            let max_players = -1;
            let total_players = session.player_count?.player ?? -1;
            if (session.player_types) {
                let team_count = Teams.filter(dr => dr != null && !(session.teams?.[dr]?.computer === true && !session.teams?.[dr]?.human)).length;
                if (team_count > 1) {
                    let matchingTypes = session.player_types.filter(dr => dr.types.indexOf('player') >= 0);
                    if (matchingTypes.length > 0 && matchingTypes[0].max) {
                        max_players = matchingTypes[0].max;
                        team_balance = Math.floor(max_players / team_count);
                    }
                }
            }
            let playerListDom = sessionDom2.querySelector('[data-path="players"]');
            playerListDom.innerHTML = '';
            let wrappedPlayers = session.players.map(p => {
                let retVal = { player: p, team: null, leader: false };

                if (!retVal.team && p.team) {
                    retVal.team = p.team.id;
                    retVal.leader = p.team.leader;
                }

                return retVal;
            });
            let teamCounter = 0;
            for (let team of Teams) {
                let in_team = team != null;
                let teamName = session.teams?.[team]?.name || session.level?.map?.teams?.[team]?.name || `Team ${team}`;
                let teamPlayers = wrappedPlayers
                    .filter(p => p.team == team)
                    .sort((a, b) => {
                        let c = microSort(a.player?.team?.index ?? -1, b.player?.team?.index ?? -1);
                        if (c != 0) return c;
                        return microSort(a.player?.index ?? -1, b.player?.index ?? -1);
                    });

                let playerHtmlEntries = '';

                var player_count = teamPlayers.length;
                if (session.teams?.[team])
                    if (max_players > -1 && total_players > -1 && session.teams[team].max) {
                        player_count = Math.max(Math.min(max_players - (total_players - teamPlayers.length), session.teams[team].max), teamPlayers.length);
                    } else {
                        player_count = Math.max(session.teams[team].max ?? player_count, teamPlayers.length, team_balance);
                    }
                for (var i = 0; i < player_count; i++) {
                    if (i < teamPlayers.length) {
                        let player = teamPlayers[i];

                        let is_over_limit = session.teams?.[team]?.max && i > session.teams[team].max;
                        let is_leader = null;

                        playerHtmlEntries += '<div class="player_flex_box">';
                        if (team)
                            is_leader = player.leader; // can't have a team leader without a team (I guess some games will be 1 team?)
                        playerHtmlEntries += GenerateHtml_Player(player.player, is_leader, is_over_limit, in_team, game_type_id);
                        playerHtmlEntries += '</div>';

                    } else {
                        let is_beyond_balance = team_balance >= 0 && i >= team_balance;
                    }
                }
                if (!session.teams?.[team]?.human && session.teams?.[team]?.computer === true) {
    //                        playerHtmlEntries += `
    //<div class="col-6 col-md-4">
    //    <div class="d-block p-2 rounded border border-danger text-danger ps-3 text-bg-danger bg-opacity-10 d-flex align-items-center h-100">
    //        <span class="text-nowrap overflow-hidden align-middle">
    //            <i class="fa fa-desktop" aria-hidden="true"></i> Computer
    //        </span>
    //    </div>
    //</div>`;
                }

                if (team) {
                    if (!session.teams?.[team]?.human && session.teams?.[team]?.computer === true) {
    //                            playerListDom.insertAdjacentHTML('beforeend', `
    //<div class="col-6 col-md-4">
    //    <div class="card h-100 border-danger" style="--bs-border-opacity: .35;">
    //        <div data-path="team_name" data-id-team="${encodeAttr(team)}" data-id-map="${session.level?.map?.$id ? encodeAttr(session.level.map.$id) : ''}" class="small card-header d-flex justify-content-center text-bg-danger">${escapeHtml(teamName)}</div>
    //        <div class="row g-0">${playerHtmlEntries}</div>
    //    </div>
    //</div>`);
                    } else {
                        // team is not null and has no computer members
                        //let slots_left = player_count - total_players;
                        playerListDom.insertAdjacentHTML('beforeend', `
    ${(Teams.length > 1 && teamCounter > 0) ? '<hr class="mb-2 mt-2 col-12 team-hr" style="border-color: #00ff00!important;opacity: 100%;right: -4px;position: relative;">' : ''}
    <div class="team-col">
    <div class="player_info_box chamfer team_box">
    <div data-path="team_name" data-id-team="${encodeAttr(team)}" data-id-map="${session.level?.map?.$id ? encodeAttr(session.level.map.$id) : ''}" class="small card-header d-flex justify-content-center chamfer" style="background:black;margin: 0 2px 2px 2px;top: 2px;position: relative;">${escapeHtml(teamName)}</div>
    <div class="d-flex flex-row flex-wrap justify-content-around row g-0" style="padding-top: 2px;">${playerHtmlEntries}</div>
    </div>
    </div>`);
                    }
                } else {
                    // team is null
                    playerListDom.insertAdjacentHTML('beforeend', `
    ${(Teams.length > 1 && team == null) ? '<hr class="mb-2 mt-2 col-12" style="border-color: #00ff00!important;opacity: 100%;right: -4px;position: relative;">' : ''}
    <div class="col-12">
    <div class="d-flex flex-row flex-wrap justify-content-around row g-1">${playerHtmlEntries}</div>
    </div>`);
                }

                teamCounter++;
            }
        }

        // sync animations
        {
            let b = -1;
            for (let a of document.getAnimations()) {
                if (a.animationName != "placeHolderShimmer")
                    continue;
                if (b >= 0) {
                    b = a.startTime;
                } else {
                    a.startTime = b;
                }
            }
        }
    }

    let debouncingDatums = false;
    let debouncingSet = new Set();
    let debouncingTimeout = -1;
    function UpdateSessionListWithDataFragments(data, modified) {
        for (const mod of modified) {
            var $parts = mod.split('\t', 2);
            var $type = $parts[0];
            var $id = $parts[1];

            // set of all affected items by the incoming datum
            let affected_set = ExpandDataRefs($type, $id);

            if (affected_set) {
                for (let v of affected_set) {
                    //console.log(v);
                    let tmp = v.split('\t');
                    debouncingSet.add(`${tmp[0]}\t${tmp[1]}`)
                }
                if (!debouncingDatums) {
                    debouncingDatums = true;
                    debouncingTimeout = setTimeout(() => {
                        console.log("START PENDING POOLING");
                        for (const affected of debouncingSet) {
                            let tmp = affected.split('\t');
                            console.log("Pending Datum Triggered", tmp[0], tmp[1])
                            if (tmp[0] == 'session') {
                                CreateOrUpdateSessionDom(tmp[1], data);
                            }
                        }
                        debouncingSet = new Set();
                        debouncingDatums = false;
                        debouncingTimeout = -1;
                        console.log("END PENDING POOLING");
                    }, 250);
                }
            }
        }
    }

    document.getElementById("btnRefresh").click();
}
