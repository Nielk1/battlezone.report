/*
// Garbage Collector
for (let parent in DataRefs_children) {
    let existing_children = DataRefs_children[parent];
    for (let child in existing_children) {
        let existingSet = DataRefs_parents[child];
        if (existingSet) {
            existingSet.delete(parent_key);
        }
    }
}
for (let type in ListData) {
    if (type == "root") continue;
    for (let id in ListData[type]) {
        if (!DataRefs_parents[`${type}\t${id}`]) {
            if (Date.now() - DataRefs_last_touched[`${type}\t${id}`] > 5000) {
                console.log([type, id]); // items have no cached parent associations and are old enough to not be fresh
            }
        }
    }
}
for (let ref in DataRefs_last_touched) {
    let parts = ref.split('\t');
    let type = parts[0];
    let id = parts[1];
    if (ListData[type]?.[id] == null) {
        console.log(parts); // item to delete from time cache as the data is gone somehows
    }
}
*/

// local copy of list data
var ListData = {};

// parent data relations so we can walk up to the session when data updates come in
export let DataRefs_parents = {};
export let DataRefs_children = {};
export let DataRefs_last_touched = {};

// Utility to get mode from query string
function getStreamMode() {
    const params = new URLSearchParams(window.location.search);
    const mode = params.get('mode');
    if (mode === 'chunked') return 'chunked';
    return 'event'; // default
}

/*export*/ function isObject(item) {
    return (item && typeof item === 'object' && !Array.isArray(item));
}

// this function merges all the sources into the destination object, and returns the destination object
// this preserves the destination object's reference, and modifies it in place, but it is also returned so you can use it with a coalesce of a default object
function MergeIntoFirstObject(target, ...sources) {
    let source = sources.shift();
    if (source === undefined) return MergeIntoFirstObject(target, ...sources); // next source
    while (source !== undefined) {
        if (isObject(target) && isObject(source)) {
            for (const key in source) {
                if (source[key] === null) {
                    // Explicitly remove property if value is null
                    delete target[key];
                } else if (isObject(source[key])) {
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

        //return MergeIntoFirstObject(target, ...sources); // next source
        source = sources.shift();
    }
}

function MergeReferences(target, parent_type, parent_id) {
    if (!isObject(target))
        return target;

    // clear old ref tracking
    let parent_key = `${target.$type || parent_type}\t${target.$id || parent_id}`;
    let existing_children = DataRefs_children[parent_key];
    if (existing_children) {
        // take the existing known children
        // unlink those children from the immediate parent, since we're about to re-parse it we might just relink them

        for (let child in existing_children) {
            let existingSet = DataRefs_parents[child];
            if (existingSet) {
                existingSet.delete(parent_key);
            }
        }

        existing_children.clear();
    }

    for (const key in target) {
        // Detect Object or Array
        if (isObject(target[key])) {
            if (target[key].$ref) {
                // we're a ref object, so forget the destination entirely and just use the source's reference
                var split = target[key].$ref.split('/');
                let frag_type = split[1].replace('~1', '/').replace('~0', '~');
                let frag_id = split[2].replace('~1', '/').replace('~0', '~');
                if (ListData[frag_type] === undefined)
                    ListData[frag_type] = {};
                let val = ListData[frag_type][frag_id];
                if (val == null) {
                    val = {};
                    ListData[frag_type][frag_id] = val;
                }
                target[key] = val;

                let child_key = `${frag_type}\t${frag_id}`;
                if (!DataRefs_parents[child_key])
                    DataRefs_parents[child_key] = new Set();
                DataRefs_parents[child_key].add(parent_key);

                if (!DataRefs_children[parent_key])
                    DataRefs_children[parent_key] = new Set();
                DataRefs_children[parent_key].add(child_key);
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
                        if (ListData[frag_type] === undefined)
                            ListData[frag_type] = {};
                        let val = ListData[frag_type][frag_id];
                        if (val == null) {
                            val = {};
                            ListData[frag_type][frag_id] = val;
                        }
                        target[key][i] = val;

                        let child_key = `${frag_type}\t${frag_id}`;
                        if (!DataRefs_parents[child_key])
                            DataRefs_parents[child_key] = new Set();
                        DataRefs_parents[child_key].add(parent_key);

                        if (!DataRefs_children[parent_key])
                            DataRefs_children[parent_key] = new Set();
                        DataRefs_children[parent_key].add(child_key);
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

    if (DataRefs_parents[`${$type}\t${$id}`]) {
        for (let v of DataRefs_parents[`${$type}\t${$id}`]) {
            let tmp = v.split('\t');
            ExpandDataRefs(tmp[0], tmp[1], local_memo);
        }
    }

    // return the memo as it's all our unique keys
    return local_memo;
}

// Shared queue and debouncer
let incomingDataQueue = [];
let incomingDataDebounceTimeout = null;
const INCOMING_DATA_DEBOUNCE_MS = 100;

function processIncomingDataDebounced(functions, nonce) {
    if (incomingDataDebounceTimeout !== null) return;
    incomingDataDebounceTimeout = setTimeout(() => {
        let UpdatedThisPass = new Set();
        while (incomingDataQueue.length > 0) {
            let data = incomingDataQueue.shift();
            if (data.$type == 'debug') {
                console.log(data.$type, data.$id, data.$data);
            } else if (data.$type == 'mark') {
                if (data.mark == "end" && data.nonce == nonce) {
                    functions.done?.();
                }
            } else {
                ListData[data.$type] = ListData[data.$type] || {};
                DataRefs_last_touched[`${data.$type}\t${data.$id}`] = Date.now();
                ListData[data.$type][data.$id] = MergeReferences(
                    MergeIntoFirstObject(
                        ListData[data.$type][data.$id] || {},
                        { $id: data.$id, $type: data.$type },
                        data.$data
                    )
                );
                UpdatedThisPass.add(`${data.$type}\t${data.$id}`);
            }
        }
        if (UpdatedThisPass.size > 0)
            UpdateSessionListWithDataFragments(functions, ListData, UpdatedThisPass);
        incomingDataDebounceTimeout = null;
    }, INCOMING_DATA_DEBOUNCE_MS);
}

let debouncingMap = new Map(); // Map: datumKey -> Set of affected keys
let debouncingInterval = null;
const DEBOUNCE_PULSE_MS = 250;

function startDebouncePulse(functions, data) {
    if (debouncingInterval !== null) return; // Already running

    debouncingInterval = setInterval(() => {
        if (debouncingMap.size === 0) return; // Nothing to process

        // Copy and clear the map for this pulse
        const currentMap = new Map(debouncingMap);
        debouncingMap.clear();

        console.log("START PENDING POOLING");
        functions.process?.(currentMap, data);
        console.log("END PENDING POOLING");
    }, DEBOUNCE_PULSE_MS);
}

function stopDebouncePulse() {
    if (debouncingInterval !== null) {
        clearInterval(debouncingInterval);
        debouncingInterval = null;
    }
}

function UpdateSessionListWithDataFragments(functions, data, modified) {
    for (const mod of modified) {
        // For each modified datum, get its full parent chain (including itself)
        let affectedSet = ExpandDataRefs(...mod.split('\t'));
        if (affectedSet) {
            debouncingMap.set(mod, new Set(affectedSet));
            startDebouncePulse(functions, data);
        }
    }
    // Optionally, call stopDebouncePulse() when you want to flush and stop (e.g., on navigation)
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

function randomString(length = 16) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

var GetGamesAjax = null;
export function RefreshSessionList(functions, games) {
    if (GetGamesAjax != null) {
        if (GetGamesAjax instanceof EventSource) {
            GetGamesAjax.close();
        } else if (GetGamesAjax instanceof XMLHttpRequest) {
            GetGamesAjax.abort();
        }
    }

    // if functions is not an object with at least one function in it return early
    if (!functions)
        return;

    if (!isObject(functions))
        return;

    // forget any pending datums from a prior refresh
    //debouncingDatums = false;
    //debouncingSet = new Set();
    //debouncingMap = new Map();
    //if (debouncingTimeout >= 0) {
    //    clearTimeout(debouncingTimeout);
    //    console.log("ABORT PENDING POOLING");
    //}
    //debouncingTimeout = -1;
    stopDebouncePulse();

    DataRefs_parents = {};
    DataRefs_children = {};
    DataRefs_last_touched = {};

    var windowSearch = window.location.search;
    if (windowSearch.length > 0)
        windowSearch = '&' + windowSearch.substring(1);

    // Build the base URL
    const url = new URL('api/games/sessions', window.location.origin);

    // Add games to request
    for (const game of games) {
        url.searchParams.append('game', game);
    }
    const mode = getStreamMode();

    // Add any other query params from the current window location
    const windowParams = new URLSearchParams(window.location.search);
    for (const [key, value] of windowParams.entries()) {
        url.searchParams.append(key, value);
    }

    ListData = {};

    // Clear the shared queue and debounce timer
    //incomingDataQueue = [];
    //if (incomingDataDebounceTimeout !== null) {
    //    clearTimeout(incomingDataDebounceTimeout);
    //    incomingDataDebounceTimeout = null;
    //}
    stopDebouncePulse();

    if (mode === 'event') {
        // Use EventSource for SSE
        url.searchParams.append("mode", "event");

        let nonce = randomString(12);
        url.searchParams.append("nonce", nonce);

        var eventSource = new EventSource(url);
        eventSource.onmessage = function (event) {
            var s = event.data + "\n";
            functions.raw?.(s);
            //document.getElementById('codeRawJsonLines').appendChild(document.createTextNode(s));
            var data = JSON.parse(event.data);
            incomingDataQueue.push(data);
            processIncomingDataDebounced(functions, nonce);
        };
        eventSource.onerror = function () {
            eventSource.close();
            functions.done?.();
        };
        GetGamesAjax = eventSource;
    } else {
        // Use XMLHttpRequest for chunked NDJson
        url.searchParams.append("mode", "chunked");
        GetGamesAjax = new XMLHttpRequest();
        GetGamesAjax.open("GET", url);
        var last_index = 0;
        GetGamesAjax.onprogress = function () {
            var end = 0;
            while ((end = GetGamesAjax.responseText.indexOf('\n', last_index)) > -1) {
                var s = GetGamesAjax.responseText.substring(last_index, end + 1);
                functions.raw?.(s);
                //document.getElementById('codeRawJsonLines').appendChild(document.createTextNode(s));
                var data = JSON.parse(s);
                incomingDataQueue.push(data);
                last_index = end + 1;
            }
            processIncomingDataDebounced(functions);
        };
        GetGamesAjax.onload = function () { functions.done?.(); }
        GetGamesAjax.send();
    }
    ListData = {};
    //GetGamesAjax.send();

    return GetGamesAjax;
}
