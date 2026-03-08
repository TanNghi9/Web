// ============================================
// TravelViet – Main JavaScript
// ============================================

// ============================================
// 1. MOBILE MENU TOGGLE
// ============================================
function toggleMenu() {
    const navMenu = document.getElementById('navMenu');
    navMenu.classList.toggle('open');
}

document.addEventListener('click', function (e) {
    const navMenu = document.getElementById('navMenu');
    const hamburger = document.getElementById('hamburger');
    if (navMenu && hamburger && !navMenu.contains(e.target) && !hamburger.contains(e.target)) {
        navMenu.classList.remove('open');
    }
});

// ============================================
// 2. BACK TO TOP
// ============================================
const backToTopBtn = document.getElementById('backToTop');

window.addEventListener('scroll', function () {
    if (backToTopBtn) {
        backToTopBtn.style.display = window.pageYOffset > 300 ? 'flex' : 'none';
    }
});

// ============================================
// 3. SEARCH & FILTER (Tours Page)
// ============================================
function filterTours() {
    const searchVal  = (document.getElementById('searchInput')?.value || '').toLowerCase();
    const catVal     = document.getElementById('filterCategory')?.value || '';
    const minPrice   = parseInt(document.getElementById('minPrice')?.value) || 0;
    const maxPrice   = parseInt(document.getElementById('maxPrice')?.value) || Infinity;
    const sortVal    = document.getElementById('sortSelect')?.value || '';

    const cards = Array.from(document.querySelectorAll('.tour-card[data-category]'));

    let visible = cards.filter(function (card) {
        const title    = (card.dataset.title || '').toLowerCase();
        const dest     = (card.dataset.dest || '').toLowerCase();
        const category = card.dataset.category || '';
        const price    = parseInt(card.dataset.price) || 0;

        const matchSearch = !searchVal || title.includes(searchVal) || dest.includes(searchVal);
        const matchCat    = !catVal || category === catVal;
        const matchMin    = price >= minPrice;
        const matchMax    = price <= maxPrice;

        return matchSearch && matchCat && matchMin && matchMax;
    });

    // Sort
    if (sortVal === 'price_asc') {
        visible.sort((a, b) => parseInt(a.dataset.price) - parseInt(b.dataset.price));
    } else if (sortVal === 'price_desc') {
        visible.sort((a, b) => parseInt(b.dataset.price) - parseInt(a.dataset.price));
    } else if (sortVal === 'name_asc') {
        visible.sort((a, b) => (a.dataset.title || '').localeCompare(b.dataset.title || ''));
    }

    // Show/hide
    cards.forEach(c => c.style.display = 'none');
    visible.forEach(c => c.style.display = '');

    // Re-append in sorted order
    const grid = document.getElementById('toursGrid');
    if (grid) visible.forEach(c => grid.appendChild(c));

    // Update result count
    const countEl = document.getElementById('resultCount');
    if (countEl) countEl.textContent = visible.length;
}

// Real-time search
const searchInput = document.getElementById('searchInput');
if (searchInput) searchInput.addEventListener('input', filterTours);

// ============================================
// 4. CATEGORY TABS
// ============================================
function selectCategory(cat, el) {
    document.querySelectorAll('.tab-chip').forEach(c => c.classList.remove('active'));
    if (el) el.classList.add('active');

    const filterCat = document.getElementById('filterCategory');
    if (filterCat) filterCat.value = cat;
    filterTours();
}

// ============================================
// 5. BOOKING PRICE CALCULATOR
// ============================================
function calcTotal() {
    const numPeople     = parseInt(document.getElementById('numPeople')?.value) || 1;
    const pricePerPerson = parseInt(document.getElementById('pricePerPerson')?.value) || 0;
    const total         = numPeople * pricePerPerson;

    const display = document.getElementById('totalDisplay');
    if (display) display.textContent = 'Tổng: ' + formatMoney(total) + ' ₫';
}

function formatMoney(n) {
    return n.toLocaleString('vi-VN');
}

const numPeopleInput = document.getElementById('numPeople');
if (numPeopleInput) numPeopleInput.addEventListener('input', calcTotal);

// ============================================
// 6. FORM VALIDATION
// ============================================
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    // Clear previous
    form.querySelectorAll('.error-msg').forEach(el => el.remove());
    form.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-error'));

    let valid = true;

    // Required fields
    form.querySelectorAll('[required]').forEach(function (field) {
        if (!field.value.trim()) {
            markError(field, 'Trường này không được để trống');
            valid = false;
        }
    });

    // Email
    const emailField = form.querySelector('input[type="email"]');
    if (emailField && emailField.value && !isValidEmail(emailField.value)) {
        markError(emailField, 'Email không hợp lệ');
        valid = false;
    }

    // Password min length
    const passField = form.querySelector('#password');
    if (passField && passField.value && passField.value.length < 6) {
        markError(passField, 'Mật khẩu tối thiểu 6 ký tự');
        valid = false;
    }

    // Password confirm
    const passConfirm = form.querySelector('#passwordConfirm');
    if (passField && passConfirm && passField.value !== passConfirm.value) {
        markError(passConfirm, 'Mật khẩu xác nhận không khớp');
        valid = false;
    }

    // Booking date – must be future
    const dateField = form.querySelector('#tourDate');
    if (dateField && dateField.value) {
        const today = new Date().toISOString().split('T')[0];
        if (dateField.value <= today) {
            markError(dateField, 'Ngày đi phải là ngày trong tương lai');
            valid = false;
        }
    }

    // Number of people >= 1
    const numField = form.querySelector('#numPeople');
    if (numField && parseInt(numField.value) < 1) {
        markError(numField, 'Số người phải ít nhất là 1');
        valid = false;
    }

    return valid;
}

function markError(field, msg) {
    field.classList.add('is-error');
    const span = document.createElement('span');
    span.className = 'error-msg';
    span.textContent = msg;
    field.parentNode.appendChild(span);
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// ============================================
// 7. STAR RATING INPUT
// ============================================
function initStarRating() {
    const labels = document.querySelectorAll('.star-input-group label');
    const inputs = document.querySelectorAll('.star-input-group input');

    labels.forEach(function (label) {
        label.addEventListener('mouseover', function () {
            // in flex-row-reverse, hover highlights this + all after
            labels.forEach(function (l) { l.style.color = '#ccc'; });
            let hit = false;
            labels.forEach(function (l) {
                if (l === label) hit = true;
                if (hit) l.style.color = '#f4a42a';
            });
        });

        label.addEventListener('mouseleave', function () {
            const checked = document.querySelector('.star-input-group input:checked');
            labels.forEach(function (l) { l.style.color = '#ccc'; });
            if (checked) {
                let hit = false;
                labels.forEach(function (l) {
                    if (l.getAttribute('for') === checked.id) hit = true;
                    if (hit) l.style.color = '#f4a42a';
                });
            }
        });
    });
}

// ============================================
// 8. NEWSLETTER POPUP
// ============================================
function initPopup() {
    const popup   = document.getElementById('newsletterPopup');
    if (!popup) return;

    const closed      = localStorage.getItem('newsletter-closed');
    const subscribed  = localStorage.getItem('newsletter-subscribed');

    if (!closed && !subscribed) {
        setTimeout(function () {
            popup.style.display = 'flex';
        }, 5000);
    }
}

function closePopup() {
    const popup = document.getElementById('newsletterPopup');
    if (popup) popup.style.display = 'none';
    localStorage.setItem('newsletter-closed', 'true');
}

function subscribeNewsletter() {
    const email = document.getElementById('popupEmailInput')?.value;
    if (!email || !isValidEmail(email)) {
        alert('Vui lòng nhập email hợp lệ');
        return;
    }
    localStorage.setItem('newsletter-subscribed', 'true');
    closePopup();
    alert('🎉 Cảm ơn bạn đã đăng ký nhận tin!');
}

// ============================================
// 9. VIEW COUNTER
// ============================================
function initViewCounters() {
    document.querySelectorAll('.tour-card[data-id]').forEach(function (card) {
        const id = card.dataset.id;
        const viewEl = card.querySelector('.view-count');
        if (!viewEl) return;

        const views = parseInt(localStorage.getItem('tour-views-' + id) || viewEl.dataset.base || 0);
        viewEl.textContent = views;

        card.addEventListener('click', function () {
            const current = parseInt(viewEl.textContent) || 0;
            const next = current + 1;
            viewEl.textContent = next;
            localStorage.setItem('tour-views-' + id, next);
        });
    });
}

// ============================================
// 10. SMOOTH SCROLL (for anchor links)
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(function (link) {
    link.addEventListener('click', function (e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});

// ============================================
// 11. PAGINATION (Client-side)
// ============================================
let currentPage = 1;
const itemsPerPage = 6;

function initPagination() {
    const allCards = Array.from(document.querySelectorAll('.tour-card[data-id]'));
    if (allCards.length === 0) return;

    const totalPages = Math.ceil(allCards.length / itemsPerPage);
    showPage(1, allCards, totalPages);
}

function showPage(page, allCards, totalPages) {
    currentPage = page;
    const start = (page - 1) * itemsPerPage;
    const end   = start + itemsPerPage;

    allCards.forEach(function (card, i) {
        card.style.display = (i >= start && i < end) ? '' : 'none';
    });

    renderPagination(page, totalPages, allCards);
}

function renderPagination(page, totalPages, allCards) {
    const paginationEl = document.getElementById('pagination');
    if (!paginationEl || totalPages <= 1) return;

    let html = '';
    html += `<span class="${page === 1 ? 'pg-disabled' : ''}" onclick="${page > 1 ? 'showPage(' + (page-1) + ', getTourCards(), getTotalPages())' : ''}">←</span>`;

    for (let i = 1; i <= totalPages; i++) {
        html += `<span class="${i === page ? 'pg-active' : ''}" onclick="showPage(${i}, getTourCards(), getTotalPages())">${i}</span>`;
    }

    html += `<span class="${page === totalPages ? 'pg-disabled' : ''}" onclick="${page < totalPages ? 'showPage(' + (page+1) + ', getTourCards(), getTotalPages())' : ''}">→</span>`;

    paginationEl.innerHTML = html;
}

function getTourCards() { return Array.from(document.querySelectorAll('.tour-card[data-id]')); }
function getTotalPages() { return Math.ceil(getTourCards().length / itemsPerPage); }

// ============================================
// 12. DARK MODE TOGGLE
// ============================================
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const btn = document.getElementById('darkModeToggle');
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('darkMode', 'enabled');
        if (btn) btn.textContent = '☀️';
    } else {
        localStorage.removeItem('darkMode');
        if (btn) btn.textContent = '🌙';
    }
}

// ============================================
// INIT
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    initStarRating();
    initPopup();
    initViewCounters();

    if (document.getElementById('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }

    // Set min date for tour booking
    const dateInput = document.getElementById('tourDate');
    if (dateInput) {
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        dateInput.min = tomorrow.toISOString().split('T')[0];
    }
});
