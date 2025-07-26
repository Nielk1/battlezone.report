import { RefreshSessionList } from '/resources/js/gamelist.js';

export function LoadGameListGames() {

    var parent = document.querySelector('#lobbyList');

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

    function CreateOrUpdateSessionDom($id, data) {
        //const lobbyList = document.getElementById('lobbyList');
        //lobbyList.classList.remove('loading');

        //var session = data.session[$id];
        //console.log(session);
        //console.log(data);
        //for(let session of data.session) {
        //    console.log(session);
        //}

        let BZCC_Sessions = 0;
        let BZCC_Players = 0;
        let BZ98R_Sessions = 0;
        let BZ98R_Players = 0;

        if (data.session) {
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

        setSessionCountElem("bz98r-sessions", BZ98R_Sessions);
        setSessionCountElem("bz98r-players", BZ98R_Players);
        setSessionCountElem("bzcc-sessions", BZCC_Sessions);
        setSessionCountElem("bzcc-players", BZCC_Players);
    }

    //document.getElementById("btnRefresh").addEventListener('click', function (e) {
    //    e.preventDefault();
    {
        // TODO once we add filtering, we need super-minimal data
        let GetGamesAjax = RefreshSessionList(CreateOrUpdateSessionDom, ['bigboat:battlezone_98_redux','bigboat:battlezone_combat_commander'], () => {
            // all done
        });
        GetGamesAjax.send();
    //});
    }

    //document.getElementById("btnRefresh").click();
}
