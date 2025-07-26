import './bootstrap';
//import 'bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import { LoadGameListBZ98R } from '/resources/js/gamelist_bz98r.js';


var active_main_nav_code = null;
var last_nav_url = null;
var scroll_spy_hash_debounce = '';
var scroll_spy_content = null;


function toggleSidebar() {
    const layout = document.getElementById('main-layout');
    const isHidden = layout.classList.toggle('sidebar-hidden');
    if (isHidden) {
        setQueryParam('sbh', '1');
    } else {
        removeQueryParam('sbh');
    }
};

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

function getScrollableParent(element) {
    let parent = element.parentElement;
    while (parent) {
        const style = getComputedStyle(parent);
        const overflowY = style.overflowY;
        const overflowX = style.overflowX;
        const isScrollableY = (overflowY === 'auto' || overflowY === 'scroll') && parent.scrollHeight > parent.clientHeight;
        const isScrollableX = (overflowX === 'auto' || overflowX === 'scroll') && parent.scrollWidth > parent.clientWidth;
        if (isScrollableY || isScrollableX) {
            return parent;
        }
        parent = parent.parentElement;
    }
    return null; // No scrollable parent found
}

// Restore sidebar width from cookie
function getCookie(name) {
    let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) return match[2];
}
function setCookie(name, value, days = 365) {
    let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + value + expires + "; path=/";
}

// Size Dragger
function initSidebarResizer() {
    const resizer = document.getElementById('resizer');
    const sidebar = document.getElementById('sidebar2');
    if (!resizer || !sidebar) return;

    // Remove any previous mousedown handler to avoid duplicates
    resizer.onmousedown = null;

    resizer.onmousedown = function(e) {
        let startX = e.clientX;
        let startWidth = parseInt(document.defaultView.getComputedStyle(sidebar).width, 10);

        function doDrag(e) {
            let newWidth = startWidth + e.clientX - startX;
            newWidth = Math.max(250, Math.min(newWidth, window.innerWidth * 0.8));
            sidebar.style.width = newWidth + 'px';
        }

        function stopDrag(e) {
            setCookie('sidebar2_width', parseInt(sidebar.style.width, 10));
            document.documentElement.removeEventListener('mousemove', doDrag, false);
            document.documentElement.removeEventListener('mouseup', stopDrag, false);
        }

        document.documentElement.addEventListener('mousemove', doDrag, false);
        document.documentElement.addEventListener('mouseup', stopDrag, false);
    };
}

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
            let url = link.querySelector('.channel-link').getAttribute('href');
            if (!url) {
                link.classList.toggle('active', false);
                return;
            }
            if (url.endsWith('#top')) { url = url.slice(0, -4); }
            link.classList.toggle('active', url === window.location.pathname);
        });
    }

    // Scroll down to active subnav item on first load of this page
    if (freshNav == 1) {
        // level 1 for sure
        const activeNav = document.querySelector('.sidebar2-content .channel-list .active');
        if (activeNav) {
            let parentContainer = getScrollableParent(activeNav);
            if (!parentContainer) {
                // element is in the main document, check if it needs scrolling
                const rect = activeNav.getBoundingClientRect();
                if (
                    rect.top < 0 ||
                    rect.left < 0 ||
                    rect.bottom > (window.innerHeight || document.documentElement.clientHeight) ||
                    rect.right > (window.innerWidth || document.documentElement.clientWidth)
                ) {
                    activeNav.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                // element is in a scrollable container, check if it needs scrolling
                const elRect = activeNav.getBoundingClientRect();
                const containerRect = parentContainer.getBoundingClientRect();
                if (
                    elRect.top < containerRect.top ||
                    elRect.bottom > containerRect.bottom
                ) {
                    activeNav.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
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
                link.classList.toggle('spy', link.querySelector('.channel-link').getAttribute('href') === window.location.pathname + '#' + currentId);
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

    // Size Dragger
    {
        const sidebar = document.getElementById('sidebar2');
        if (sidebar) {
            // Restore width
            const savedWidth = getCookie('sidebar2_width');
            if (savedWidth) sidebar.style.width = savedWidth + 'px';
        }
    }

    // attach the resizer (and remove any existing event handlers just in case)
    initSidebarResizer();

    // sidebar toggle button
    {
        let sidebar_toggle = document.getElementById('sidebar-toggle');
        if (sidebar_toggle) {
            sidebar_toggle.removeEventListener('click', toggleSidebar); // Remove any existing handler
            sidebar_toggle.addEventListener('click', toggleSidebar); // Add the new handler
        }
    }

    if (window.location.pathname.startsWith('/games/bz98r')) {
        if (LoadGameListBZ98R) {
            LoadGameListBZ98R();
        }
    }
}

document.addEventListener('DOMContentLoaded', initPage);

// After AJAX navigation:
function ajaxNavigate(url, targetSelector, depth = 1, historyNavigation = false) {
    // Parse the URL and add ajax=1 to the query string
    const u = new URL(url, window.location.origin);

    // if the path is the same, abort the load attempt.
    // note that the caller, if the top level handler, currently filters these out and triggers normal event handling
    if (!historyNavigation && u.pathname == window.location.pathname && u.search == window.location.search) {
        // abort load
        document.querySelector(targetSelector).classList.remove('loading');
        //console.warn('Aborting AJAX navigation: URL is the same as current.');
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
                if (!historyNavigation) // noHistory is used when navigating existing history
                    window.history.pushState({}, '', cleanUrl.pathname + cleanUrl.search + cleanUrl.hash);
                initPage();
                //document.querySelector(targetSelector).style.opacity = 1;
                document.querySelector(targetSelector).classList.remove('loading');
            }
        });
}

var last_nav_url = window.location.toString(); // Initialize with the current path
window.addEventListener('popstate', function(e) {
    // Reload the content for the current URL
    //ajaxNavigate(window.location.pathname + window.location.search, '#main-content');

    const oldUrl = new URL(last_nav_url, window.location.origin);
    if (oldUrl.pathname == window.location.pathname && oldUrl.search == window.location.search)
    {
        // URLs are the same aside from maybe the hash
        let hash = window.location.hash;
        if (!hash || hash === '' || hash === '#') {
            hash = "#top"; // Default to top if no hash
        }
        // we have a hash so scroll
        // Remove the '#' and find the element
        const el = document.getElementById(hash.slice(1));
        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    } else {
        //window.location = window.location.toString(); // Reload the page with the current URL
        //ajaxNavigate(window.location.pathname + window.location.search + window.location.hash, '#main-content', 1, true);

        // Determine ajax depth and target
        const oldSegments = oldUrl.pathname.split('/').filter(Boolean);
        const newSegments = window.location.pathname.split('/').filter(Boolean);

        let depth = 1;
        let target = '#main-content';

        // If both start with 'issue' and have at least 3 segments, use depth 2 and #sub-content
        if (oldSegments[0] === newSegments[0]) {
            depth = 2;
            target = '#sub-content';
        }

        ajaxNavigate(window.location.pathname + window.location.search + window.location.hash, target, depth, true);
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
            let localNav = false;
            if (urlObj.pathname == window.location.pathname && urlObj.search == window.location.search && urlObj.hash)
            {
                // our URL is the same except for the hash, so this is a local nav
                localNav = true;
            }
            if (localNav) {
                e.preventDefault();
                // Remove the '#' and find the element
                const el = document.getElementById(urlObj.hash.slice(1));
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                window.history.pushState({}, '', urlObj.pathname + urlObj.search + urlObj.hash);
            } else {
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
            }
        }
    });
}
setupAjaxNavLinks();
