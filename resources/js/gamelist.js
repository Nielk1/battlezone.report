// local copy of list data
var ListData = {};

// parent data relations so we can walk up to the session when data updates come in
//var DataRefs = {};
export let DataRefs = {};

/*export*/ function isObject(item) {
    return (item && typeof item === 'object' && !Array.isArray(item));
}

// this function merges all the sources into the destination object, and returns the destination object
// this preserves the destination object's reference, and modifies it in place, but it is also returned so you can use it with a coalesce of a default object
/*export*/ function MergeIntoFirstObject(target, ...sources) {
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

/*export*/ function MergeReferences(target, parent_type, parent_id) {
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

/*export*/ function ExpandDataRefs($type, $id, memo) {
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

let debouncingDatums = false;
let debouncingSet = new Set();
let debouncingTimeout = -1;
export function UpdateSessionListWithDataFragments(CreateOrUpdateSessionDom, data, modified) {
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

//export function debounceDatums() {
//    // forget any pending datums from a prior refresh
//    debouncingDatums = false;
//    debouncingSet = new Set();
//    if (debouncingTimeout >= 0) {
//        clearTimeout(debouncingTimeout);
//        console.log("ABORT PENDING POOLING");
//    }
//    debouncingTimeout = -1;
//}

//export function clearDataRefs() {
//    DataRefs = {};
//}

var GetGamesAjax = null;
export function RefreshSessionList(CreateOrUpdateSessionDom, games, doneFn = null) {
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

    // Build the base URL
    const url = new URL('/api/games/sessions', window.location.origin);

    // Add games to request
    for (const game of games)
    {
        url.searchParams.append('game', game);
    }

    // Add any other query params from the current window location
    const windowParams = new URLSearchParams(window.location.search);
    for (const [key, value] of windowParams.entries()) {
        url.searchParams.append(key, value);
    }

    GetGamesAjax = new XMLHttpRequest();
    //GetGamesAjax.open("GET", '/api/games/sessions?game=bigboat:battlezone_98_redux' + windowSearch);
    GetGamesAjax.open("GET", url.toString());
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
            UpdateSessionListWithDataFragments(CreateOrUpdateSessionDom, ListData, UpdatedThisPass);
    };
    GetGamesAjax.onload = function () {
        if (doneFn) {
            doneFn();
        }
    }
    ListData = {};
    //GetGamesAjax.send();

    return GetGamesAjax;
}
