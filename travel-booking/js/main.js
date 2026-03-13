// ============================================
// TRAVELVN – MAIN JAVASCRIPT
// ============================================

// ── Mobile nav toggle ──
const navToggle = document.getElementById('navToggle');
const navMenu   = document.getElementById('navMenu');
if (navToggle) {
    navToggle.addEventListener('click', () => navMenu.classList.toggle('open'));
}

// ── Back to top ──
const backToTop = document.getElementById('backToTop');
if (backToTop) {
    window.addEventListener('scroll', () => {
        backToTop.style.display = window.scrollY > 400 ? 'flex' : 'none';
    });
    backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

// ── Booking price calculator ──
const numPeopleInput = document.getElementById('num_people');
const pricePerPerson = document.getElementById('price_per_person');
const totalDisplay   = document.getElementById('total_display');

if (numPeopleInput && pricePerPerson && totalDisplay) {
    function calcTotal() {
        const n     = parseInt(numPeopleInput.value) || 1;
        const price = parseInt(pricePerPerson.value)  || 0;
        const total = n * price;
        totalDisplay.textContent = total.toLocaleString('vi-VN') + ' VNĐ';
    }
    numPeopleInput.addEventListener('input', calcTotal);
    calcTotal();
}

// ── Filter chips on tours page ──
const chips = document.querySelectorAll('.chip[data-category]');
chips.forEach(chip => {
    chip.addEventListener('click', function() {
        chips.forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        const url = new URL(window.location);
        if (this.dataset.category === 'all') {
            url.searchParams.delete('category');
        } else {
            url.searchParams.set('category', this.dataset.category);
        }
        url.searchParams.delete('page');
        window.location = url.toString();
    });
});

// ── Sort select ──
const sortSelect = document.getElementById('sortSelect');
if (sortSelect) {
    sortSelect.addEventListener('change', function() {
        const url = new URL(window.location);
        url.searchParams.set('sort', this.value);
        url.searchParams.delete('page');
        window.location = url.toString();
    });
}

// ── Live search (debounce) ──
const searchLive = document.getElementById('searchLive');
let searchTimer;
if (searchLive) {
    searchLive.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            const url = new URL(window.location);
            if (this.value.trim()) {
                url.searchParams.set('search', this.value.trim());
            } else {
                url.searchParams.delete('search');
            }
            url.searchParams.delete('page');
            window.location = url.toString();
        }, 600);
    });
}

// ── Form validation ──
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    let valid = true;
    form.querySelectorAll('[required]').forEach(input => {
        const err = input.parentElement.querySelector('.field-error');
        input.classList.remove('error');
        if (err) err.textContent = '';

        if (!input.value.trim()) {
            valid = false;
            input.classList.add('error');
            if (err) err.textContent = 'Trường này không được để trống.';
        } else if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value)) {
            valid = false;
            input.classList.add('error');
            if (err) err.textContent = 'Email không hợp lệ.';
        } else if (input.type === 'tel' && !/^0[0-9]{9}$/.test(input.value)) {
            valid = false;
            input.classList.add('error');
            if (err) err.textContent = 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.';
        }
    });

    // Password match check
    const pass  = form.querySelector('#password');
    const pass2 = form.querySelector('#password_confirm');
    if (pass && pass2 && pass.value !== pass2.value) {
        valid = false;
        pass2.classList.add('error');
        const err2 = pass2.parentElement.querySelector('.field-error');
        if (err2) err2.textContent = 'Mật khẩu xác nhận không khớp.';
    }
    return valid;
}

// ── Smooth fade-in on scroll (Intersection Observer) ──
const fadeEls = document.querySelectorAll('.tour-card, .detail-card');
if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.style.opacity = '1';
                e.target.style.transform = 'translateY(0)';
                observer.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });

    fadeEls.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity .4s ease, transform .4s ease';
        observer.observe(el);
    });
}

// ── Auto-dismiss alerts ──
const alerts = document.querySelectorAll('.alert');
alerts.forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity .4s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 400);
    }, 4000);
});
