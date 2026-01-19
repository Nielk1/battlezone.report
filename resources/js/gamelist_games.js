import { RefreshSessionList } from '/resources/js/gamelist.js';

export function LoadGameListGames() {

    let GotData = false;

    function setSessionCountElem(elemId, value, showSpinner = false) {
        const elem = document.getElementById(elemId);
        elem.innerHTML = '';
        if (showSpinner) {
            const spinner = document.createElement('i');
            spinner.className = 'fas fa-spinner fa-spin';
            elem.appendChild(spinner);
            return;
        }
        if (value >= 0 && value <= 9) {
            const icon = document.createElement('i');
            if (value === 0) {
                icon.className = 'bi bi-0-circle';
            } else {
                icon.className = `bi bi-${value}-square-fill`;
            }
            elem.appendChild(icon);
        } else {
            elem.innerText = value;
        }
    }

    function FillFromData(data) {
        GotData = true;
        let BZCC_Sessions = 0;
        let BZCC_Players = 0;
        let BZ98R_Sessions = 0;
        let BZ98R_Players = 0;

        if (data?.session) {
            for (const [key, value] of Object.entries(data.session)) {
                if (key.startsWith('bigboat:battlezone_98_redux')) {
                    let players = (value.player_count?.player ?? 0) + (value.player_count?.spectator ?? 0);
                    if (players > 0) {
                        BZ98R_Sessions++;
                        BZ98R_Players += players;
                    }
                }
                if (key.startsWith('bigboat:battlezone_combat_commander')) {
                    let players = (value.player_count?.player ?? 0) + (value.player_count?.spectator ?? 0);
                    if (players > 0) {
                        BZCC_Sessions++;
                        BZCC_Players += players;
                    }
                }
            }
        }

        setSessionCountElem("bz98r-sessions", BZ98R_Sessions);
        setSessionCountElem("bz98r-players", BZ98R_Players);
        setSessionCountElem("bzcc-sessions", BZCC_Sessions);
        setSessionCountElem("bzcc-players", BZCC_Players);
    }

    function UpdateDatumDom(updates, data) {
        const seen = new Set(); // prevent refiring updates we already did in this batch
        for (const [datumKey, affectedSet] of updates.entries()) {
            // type - Datum type that caused this update
            // id - Datum id that caused this update
            let [type, id] = datumKey.split('\t');

            for (const affected of affectedSet) {
                // affectedType - Datum type that is influenced by this update
                // affectedId - Datum id that is influenced by this update
                let [affectedType, affectedId] = datumKey.split('\t');
                const key = `${affectedType}\t${affectedId}`;
                if (seen.has(key)) continue;
                seen.add(key);

                console.log(affectedType, affectedId);

                //if (affectedType === 'source') {
                //    CreateOrUpdateSourceDom?.(affectedId, data);
                //}
                if (affectedType === 'session') {
                    if (type == 'source') {
                        // source nodes aren't refleccted in Session dom so ignore them even if they triggered an update
                    } else {
                        FillFromData(data);
                        //CreateOrUpdateSessionDom(affectedId, data);
                    }
                }
                //if (affectedType === 'lobby') {
                //    if (type == 'source') {
                //        // source nodes aren't refleccted in Session dom so ignore them even if they triggered an update
                //    } else {
                //        CreateOrUpdateLobbyDom(affectedId, data);
                //    }
                //}
            }
        }
    }

    function showAllSpinners() {
        GotData = false;
        setSessionCountElem("bz98r-sessions", null, true);
        setSessionCountElem("bz98r-players", null, true);
        setSessionCountElem("bzcc-sessions", null, true);
        setSessionCountElem("bzcc-players", null, true);
    }

    function showAllZeros() {
        setSessionCountElem("bz98r-sessions", 0);
        setSessionCountElem("bz98r-players", 0);
        setSessionCountElem("bzcc-sessions", 0);
        setSessionCountElem("bzcc-players", 0);
    }

    document.getElementById("gamelist-games-reload").addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector("#gamelist-games-reload i")?.classList.add('fa-spin');
        showAllSpinners();
        let GetGamesAjax = RefreshSessionList({
            process: UpdateDatumDom,
            done: () => {
                if (!GotData) {
                    showAllZeros();
                }
                document.querySelector("#gamelist-games-reload i")?.classList.remove('fa-spin');
            },
            fail: () => { // there is no fail fn?
                showAllZeros();
                document.querySelector("#gamelist-games-reload i")?.classList.remove('fa-spin');
            }
        }, ['bigboat:battlezone_98_redux','bigboat:battlezone_combat_commander']);
    });

    // On load, show spinners and trigger initial load
    showAllSpinners();
    document.querySelector("#gamelist-games-reload i").click();
}
