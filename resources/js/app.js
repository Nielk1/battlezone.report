import './bootstrap';
//import 'bootstrap';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

let active_main_nav_code = null;
function setMainNav(code) {
    const sidebar = document.querySelector('#sidebar');
    if (!sidebar) return;
    sidebar.querySelectorAll('[data-nav].active').forEach(item => item.classList.remove('active'));
    const newActiveItem = sidebar.querySelector(`[data-nav="${code}"]`);
    if (newActiveItem) newActiveItem.classList.add('active');
    active_main_nav_code = code;
}

let last_nav_url = null;
let scroll_spy_hash_debounce = '';
let scroll_spy_content = null;
function initPage() {
    // initialization for the page
    // this function will get called on first load and any ajax nav so be sure to not duplicate event listeners or other work

    // might be worth doing a fresh-nav array for URL depths, since we might only want to auto-scroll if the length 1 change occured
    let freshNav = last_nav_url?.pathname != window.location.pathname;
    last_nav_url = window.location;

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

    // Scroll down to active subnav item on first load of this page
    if (freshNav) {
        const activeNav = document.querySelector('.sidebar2-content .channel-list .active');
        if (activeNav) {
            activeNav.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Scroll Spy Section
    {
        let old_scroll_spy_content = scroll_spy_content;
        scroll_spy_content = document.getElementById('main-scrollable-content');
        if (old_scroll_spy_content && old_scroll_spy_content !== scroll_spy_content) {
            old_scroll_spy_content.removeEventListener('scroll', scroll_spy_content.scrollHandler);
        }
        if (scroll_spy_content) {
            scroll_spy_content.addEventListener('scroll', function() {
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
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', initPage);

// After AJAX navigation:
function ajaxNavigate(url, targetSelector) {
    // Parse the URL and add ajax=1 to the query string
    const u = new URL(url, window.location.origin);
    u.searchParams.set('ajax', '1');

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
                ajaxNavigate(result.json.redirect, targetSelector);
            } else if (result.text) {
                document.querySelector(targetSelector).innerHTML = result.text;
                // Push the URL **without** the ajax param for history
                const cleanUrl = u;
                cleanUrl.searchParams.delete('ajax');
                window.history.pushState({}, '', cleanUrl.pathname + cleanUrl.search);
                initPage();
            }
        });
}

// let's just make history navigation always do full page loads
window.addEventListener('popstate', function(e) {
    // Reload the content for the current URL
    //ajaxNavigate(window.location.pathname + window.location.search, '.main-content');
    window.location = window.location.href; // This will reload the page
});

function setupAjaxNavLinks() {
    document.body.addEventListener('click', function(e) {
        // Find the closest ancestor with data-ajaxnav (handles clicks on child elements)
        const link = e.target.closest('a[data-ajaxnav]');
        if (link) {
            e.preventDefault();
            //endPage(); // so we can clean up any event handlers we created
            const url = link.getAttribute('href');
            const target = link.getAttribute('data-ajaxnav-target') || '.main-content'; // fallback selector
            ajaxNavigate(url, target);
        }
    });
}
setupAjaxNavLinks();


