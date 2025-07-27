import { RefreshSessionList } from '/resources/js/gamelist.js';

export function LoadGameListGames() {

    let lastData = null;

    function setSessionCountElem(elemId, value) {
        const elem = document.getElementById(elemId);
        elem.innerHTML = ''; // Clear contents
        if (value >= 0 && value <= 9) {
            const icon = document.createElement('i');
            if (value === 0) {
                icon.className = 'bi bi-0-circle';
            }
            else
            {
                icon.className = `bi bi-${value}-square-fill`;
            }
            elem.appendChild(icon);
        } else {
            elem.innerText = value;
        }
    }

    function FillFromData(data, allowZero = false) {

        let BZCC_Sessions = 0;
        let BZCC_Players = 0;
        let BZ98R_Sessions = 0;
        let BZ98R_Players = 0;

        if (data?.session) {
            for (const [key, value] of Object.entries(data.session)) {
                if (key.startsWith('bigboat:battlezone_98_redux')) {
                    BZ98R_Sessions++;
                    if (value.players) {
                        BZ98R_Players += value.players.length;
                    }
                }
                if (key.startsWith('bigboat:battlezone_combat_commander')) {
                    BZCC_Sessions++;
                    if (value.players) {
                        BZCC_Players += value.players.length;
                    }
                }
            }
        }

        if (allowZero || BZ98R_Sessions > 0) setSessionCountElem("bz98r-sessions", BZ98R_Sessions);
        if (allowZero || BZ98R_Players > 0) setSessionCountElem("bz98r-players", BZ98R_Players);
        if (allowZero || BZCC_Sessions > 0) setSessionCountElem("bzcc-sessions", BZCC_Sessions);
        if (allowZero || BZCC_Players > 0) setSessionCountElem("bzcc-players", BZCC_Players);

    }

    function CreateOrUpdateSessionDom($id, data) {
        //const lobbyList = document.getElementById('lobbyList');
        //lobbyList.classList.remove('loading');

        FillFromData(data);
        lastData = data;

        //var session = data.session[$id];
        //console.log(session);
        //console.log(data);
        //for(let session of data.session) {
        //    console.log(session);
        //}
    }

    document.getElementById("gamelist-games-reload").addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector("#gamelist-games-reload i")?.classList.add('fa-spin');
        // TODO once we add filtering, we need super-minimal data
        let GetGamesAjax = RefreshSessionList(CreateOrUpdateSessionDom, ['bigboat:battlezone_98_redux','bigboat:battlezone_combat_commander'], () => {
            // all done
            FillFromData(lastData, true);
            document.querySelector("#gamelist-games-reload i")?.classList.remove('fa-spin');
        });
        GetGamesAjax.send();
    });

    //document.getElementById("btnRefresh").click();
    document.querySelector("#gamelist-games-reload i").click();
}
