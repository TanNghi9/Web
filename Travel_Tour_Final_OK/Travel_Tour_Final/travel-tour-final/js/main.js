// ============================================================
// js/main.js – VietNam Travel Website
// ============================================================

// ── Navbar scroll effect ──
window.addEventListener('scroll', function () {
    var navbar = document.getElementById('navbar');
    if (navbar) {
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    }

    // Back to top
    var btn = document.getElementById('backToTop');
    if (btn) {
        if (window.scrollY > 400) btn.classList.add('visible');
        else btn.classList.remove('visible');
    }
});

// ── Back to top ──
var backToTopBtn = document.getElementById('backToTop');
if (backToTopBtn) {
    backToTopBtn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}


// ── Homepage search redirect ──
function doSearch() {
    var kw  = document.getElementById('searchInput');
    var cat = document.getElementById('categoryFilter');
    var dur = document.getElementById('durationFilter');
    var url = 'tours.php?';
    if (kw  && kw.value.trim())  url += 'search='   + encodeURIComponent(kw.value.trim()) + '&';
    if (cat && cat.value)        url += 'category=' + encodeURIComponent(cat.value) + '&';
    if (dur && dur.value)        url += 'duration=' + encodeURIComponent(dur.value);
    window.location.href = url;
}

var searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') doSearch();
    });
}
