import './bootstrap';
//import 'bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

var active_main_nav_code = null;
function setMainNav(code) {
    const sidebar = document.querySelector('#sidebar');
    if (!sidebar) return;
    sidebar.querySelectorAll('[data-nav].active').forEach(item => item.classList.remove('active'));
    const newActiveItem = sidebar.querySelector(`[data-nav="${code}"]`);
    if (newActiveItem) newActiveItem.classList.add('active');
    active_main_nav_code = code;
}

function navDepthChange(prevPath, currPath) {
    if (prevPath == null) return 1;
    const prev = prevPath.split('/').filter(Boolean);
    const curr = currPath.split('/').filter(Boolean);
    const len = Math.max(prev.length, curr.length);
    for (let i = 0; i < len; i++) {
        if (prev[i] !== curr[i]) {
            return i + 1; // 1-based index for depth of change
        }
    }
    return 0; // No change
}

var last_nav_url = null;
var scroll_spy_hash_debounce = '';
var scroll_spy_content = null;
function initPage() {
    // initialization for the page
    // this function will get called on first load and any ajax nav so be sure to not duplicate event listeners or other work

    // might be worth doing a fresh-nav array for URL depths, since we might only want to auto-scroll if the length 1 change occured
    let freshNav = last_nav_url != window.location.pathname;
    if (freshNav)
        freshNav = navDepthChange(last_nav_url, window.location.pathname);
    last_nav_url = window.location.pathname;

    // page nav data
    {
        const pageData = document.getElementById('page-data');
        if (pageData) {
            const activeNav = pageData.getAttribute('data-active-nav');
            setMainNav(activeNav);
        }
    }

    // main page price update
    {
        // Find all price-row elements
        document.querySelectorAll('.price-row[id^="price-cluster-"]').forEach(function(row) {
            const id = row.id; // e.g., "price-cluster-BZCC"
            const code = id.replace('price-cluster-', '');

            fetch(`/price-cluster/${encodeURIComponent(code)}`)
                .then(response => response.text())
                .then(html => {
                    // Create a temporary container to parse the HTML
                    const temp = document.createElement('div');
                    temp.innerHTML = html.trim();
                    const newRow = temp.querySelector('.price-row');
                    if (newRow) {
                        row.replaceWith(newRow);
                    }
                })
                .catch(err => {
                    // Optionally handle errors (e.g., network issues)
                    console.error(`Failed to update price cluster for ${code}:`, err);
                });
        });
    }

    // correct active subnav item on any subitem load
    if (freshNav > 0) {
        // level 2 or higher
        let navLinks = document.querySelectorAll('.channel-item');
        navLinks.forEach(link => {
            link.classList.toggle('active', link.getAttribute('href') === window.location.pathname
                                         || link.getAttribute('href') === window.location.pathname + "#issue_mast"); // todo: do this better, maybe a data attribute
        });
    }

    // Scroll down to active subnav item on first load of this page
    if (freshNav == 1) {
        // level 1 for sure
        const activeNav = document.querySelector('.sidebar2-content .channel-list .active');
        if (activeNav) {
            activeNav.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Scroll Spy Section
    {
        function scrollHandler() {
            let sections = scroll_spy_content.querySelectorAll('[data-spy="section"]');
            let navLinks = document.querySelectorAll('.channel-item');
            let scrollPos = scroll_spy_content.scrollY || scroll_spy_content.scrollTop;

            let currentId = '';
            sections.forEach(section => {
                if (section.offsetTop <= scrollPos + 100) { // 100px offset for header
                    currentId = section.id;
                }
            });

            // Special case: if at (or near) the bottom, force last section active
            if (scroll_spy_content.scrollTop + scroll_spy_content.clientHeight >= scroll_spy_content.scrollHeight - 2) {
                if (sections.length > 0) {
                    currentId = sections[sections.length - 1].id;
                }
            }

            navLinks.forEach(link => {
                link.classList.toggle('spy', link.getAttribute('href') === window.location.pathname + '#' + currentId);
            });

            // Update URL hash without navigation
            if (currentId && currentId !== scroll_spy_hash_debounce) {
                history.replaceState(null, '', '#' + currentId);
                scroll_spy_hash_debounce = currentId;
            }
        }

        let old_scroll_spy_content = scroll_spy_content;
        scroll_spy_content = document.getElementById('main-scrollable-content');
        if (old_scroll_spy_content && old_scroll_spy_content !== scroll_spy_content) {
            old_scroll_spy_content.removeEventListener('scroll', scrollHandler);
        }
        if (scroll_spy_content) {
            scroll_spy_content.addEventListener('scroll', scrollHandler);
        }
    }
}

document.addEventListener('DOMContentLoaded', initPage);

// After AJAX navigation:
function ajaxNavigate(url, targetSelector, depth = 1,) {
    // Parse the URL and add ajax=1 to the query string
    const u = new URL(url, window.location.origin);

    // if the path is the same, abort the load attempt.
    // note that the caller, if the top level handler, currently filters these out and triggers normal event handling
    if (u.pathname == window.location.pathname && u.search == window.location.search) {
        // abort load
        document.querySelector(targetSelector).classList.remove('loading');
        return;
    }

    u.searchParams.set('ajax', depth.toString());
    //document.querySelector(targetSelector).style.opacity = 0;
    document.querySelector(targetSelector).classList.add('loading');

    //fetch(u.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    fetch(u.toString())
        .then(r => {
            const contentType = r.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return r.json().then(data => ({json: data}));
            }
            return r.text().then(text => ({text}));
        })
        .then(result => {
            if (result.json && result.json.redirect) {
                ajaxNavigate(result.json.redirect, targetSelector, depth);
            } else if (result.text) {
                document.querySelector(targetSelector).innerHTML = result.text;
                if (u.hash) {
                    // Remove the '#' and find the element
                    const el = document.getElementById(u.hash.slice(1));
                    if (el) {
                        //el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        el.scrollIntoView({ behavior: 'auto', block: 'start' });
                    }
                }
                // Push the URL **without** the ajax param for history
                const cleanUrl = u;
                cleanUrl.searchParams.delete('ajax');
                window.history.pushState({}, '', cleanUrl.pathname + cleanUrl.search + cleanUrl.hash);
                initPage();
                //document.querySelector(targetSelector).style.opacity = 1;
                document.querySelector(targetSelector).classList.remove('loading');
            }
        });
}

var last_nav_url = window.location.toString(); // Initialize with the current path
// let's just make history navigation always do full page loads
window.addEventListener('popstate', function(e) {
    // Reload the content for the current URL
    //ajaxNavigate(window.location.pathname + window.location.search, '#main-content');

    const oldUrl = new URL(last_nav_url, window.location.origin);
    if (oldUrl.pathname == window.location.pathname && oldUrl.search == window.location.search)
    {
        // URLs are the same aside from maybe the hash
        if (oldUrl.hash) {
            // we have a hash so scroll
            // Remove the '#' and find the element
            const el = document.getElementById(oldUrl.hash.slice(1));
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }else{
        window.location = window.location.toString(); // Reload the page with the current URL
    }

    last_nav_url = window.location.toString();
});

function setupAjaxNavLinks() {
    document.body.addEventListener('click', function(e) {
        // Find the closest ancestor with data-ajaxnav (handles clicks on child elements)
        const link = e.target.closest('a[data-ajaxnav]');
        if (link) {
            const url = link.getAttribute('href');
            const urlObj = new URL(url, window.location.origin);
            if (urlObj.pathname !== window.location.pathname && urlObj.search == window.location.search)
            {
                e.preventDefault();
                let level = link.getAttribute('data-ajaxnav');
                if (level === 'true') {
                    level = 1;
                } else if (level === 'false') {
                    level = 0;
                } else {
                    level = parseInt(level, 10) || 1; // default to 1 if not a number
                }
                const target = link.getAttribute('data-ajaxnav-target') || '#main-content'; // fallback selector
                ajaxNavigate(url, target, level);
            } else {
                if (urlObj.hash) {
                    e.preventDefault();
                    // Remove the '#' and find the element
                    const el = document.getElementById(urlObj.hash.slice(1));
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                    window.history.pushState({}, '', urlObj.pathname + urlObj.search + urlObj.hash);
                }
            }
        }
    });
}
setupAjaxNavLinks();


