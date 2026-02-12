// ===== TOURS DATA =====
const toursData = [
    {
        id: 1,
        name: 'Vịnh Hạ Long - Du Thuyền 2N1Đ',
        destination: 'halongbay',
        type: 'beach',
        duration: '2 ngày 1 đêm',
        price: 2500000,
        oldPrice: 3000000,
        location: 'Quảng Ninh',
        image: 'images/halong-bay.jpg',
        description: 'Khám phá kỳ quan thiên nhiên thế giới với du thuyền sang trọng, tham quan động Thiên Cung, làng chài...',
        features: ['Bao gồm bữa ăn', 'Hướng dẫn viên', 'Vé tham quan'],
        badge: 'Nổi bật',
        rating: 4.8,
        reviews: 156
    },
    {
        id: 2,
        name: 'Sa Pa - Fansipan 3N2Đ',
        destination: 'sapa',
        type: 'mountain',
        duration: '3 ngày 2 đêm',
        price: 3200000,
        location: 'Lào Cai',
        image: 'images/sapa.jpg',
        description: 'Chinh phục nóc nhà Đông Dương, khám phá bản làng dân tộc, thưởng thức ẩm thực núi rừng...',
        features: ['Cáp treo Fansipan', 'Trekking', 'Homestay'],
        badge: 'Hot',
        rating: 4.9,
        reviews: 203
    },
    {
        id: 3,
        name: 'Đà Nẵng - Hội An - Bà Nà 4N3Đ',
        destination: 'danang',
        type: 'city',
        duration: '4 ngày 3 đêm',
        price: 4500000,
        location: 'Đà Nẵng',
        image: 'images/danang.jpg',
        description: 'Tham quan Cầu Vàng, phố cổ Hội An, tắm biển Mỹ Khê, check-in Bà Nà Hills...',
        features: ['Khách sạn 4 sao', 'Vé cáp treo', 'Tham quan'],
        badge: '',
        rating: 4.7,
        reviews: 187
    },
    {
        id: 4,
        name: 'Phú Quốc - Đảo Ngọc 4N3Đ',
        destination: 'phuquoc',
        type: 'beach',
        duration: '4 ngày 3 đêm',
        price: 5800000,
        oldPrice: 7000000,
        location: 'Kiên Giang',
        image: 'images/phuquoc.jpg',
        description: 'Nghỉ dưỡng tại đảo ngọc, tham quan VinWonders, lặn ngắm san hô, câu cá...',
        features: ['Resort 5 sao', 'VinWonders', 'Tour 4 đảo'],
        badge: 'Khuyến mãi',
        rating: 4.9,
        reviews: 234
    },
    {
        id: 5,
        name: 'Nha Trang - Vinpearl 3N2Đ',
        destination: 'nhatrang',
        type: 'beach',
        duration: '3 ngày 2 đêm',
        price: 3800000,
        location: 'Khánh Hòa',
        image: 'images/nhatrang.jpg',
        description: 'Tắm biển, tham quan đảo, vui chơi tại VinWonders, tắm bùn khoáng nóng...',
        features: ['VinWonders', 'Tắm bùn', 'Tour 4 đảo'],
        badge: '',
        rating: 4.6,
        reviews: 178
    },
    {
        id: 6,
        name: 'Đà Lạt - Thành Phố Ngàn Hoa 3N2Đ',
        destination: 'dalat',
        type: 'city',
        duration: '3 ngày 2 đêm',
        price: 3500000,
        location: 'Lâm Đồng',
        image: 'images/dalat.jpg',
        description: 'Khám phá thành phố sương mù, tham quan thác Datanla, Crazy House, làng cù lần...',
        features: ['Khách sạn 3 sao', 'Tham quan', 'Cafe view đẹp'],
        badge: '',
        rating: 4.5,
        reviews: 145
    },
    {
        id: 7,
        name: 'Hà Nội - Ninh Bình 2N1Đ',
        destination: 'hanoi',
        type: 'culture',
        duration: '2 ngày 1 đêm',
        price: 2200000,
        location: 'Hà Nội - Ninh Bình',
        image: 'images/hanoi.jpg',
        description: 'Khám phá thủ đô ngàn năm văn hiến, tham quan Tràng An, Tam Cốc, Bái Đính...',
        features: ['Hướng dẫn viên', 'Đi thuyền', 'Vé tham quan'],
        badge: 'Nổi bật',
        rating: 4.7,
        reviews: 167
    },
    {
        id: 8,
        name: 'Hội An - Cù Lao Chàm 2N1Đ',
        destination: 'hoian',
        type: 'culture',
        duration: '2 ngày 1 đêm',
        price: 2800000,
        location: 'Quảng Nam',
        image: 'images/hoian.jpg',
        description: 'Khám phá phố cổ, lặn biển Cù Lao Chàm, thả đèn lồng trên sông Hoài...',
        features: ['Tour đảo', 'Lặn biển', 'Phố cổ'],
        badge: '',
        rating: 4.8,
        reviews: 192
    },
    {
        id: 9,
        name: 'Mũi Né - Phan Thiết 3N2Đ',
        destination: 'phanThiet',
        type: 'beach',
        duration: '3 ngày 2 đêm',
        price: 3100000,
        location: 'Bình Thuận',
        image: 'images/muine.jpg',
        description: 'Đồi cát bay, suối tiên, làng chài, thưởng thức hải sản tươi ngon...',
        features: ['Resort view biển', 'Jeep tour', 'Lướt ván'],
        badge: '',
        rating: 4.4,
        reviews: 134
    },
    {
        id: 10,
        name: 'Quy Nhơn - Phú Yên 4N3Đ',
        destination: 'quyNhon',
        type: 'beach',
        duration: '4 ngày 3 đêm',
        price: 4200000,
        location: 'Bình Định - Phú Yên',
        image: 'images/quynhon.jpg',
        description: 'Ghềnh Đá Đĩa, Kỳ Co - Eo Gió, Gành Đá Đĩa, biển xanh cát trắng...',
        features: ['Khách sạn 4 sao', 'Tour đảo', 'Hải sản'],
        badge: 'Hot',
        rating: 4.7,
        reviews: 156
    },
    {
        id: 11,
        name: 'TP.HCM - Vũng Tàu 2N1Đ',
        destination: 'vungTau',
        type: 'beach',
        duration: '2 ngày 1 đêm',
        price: 1800000,
        location: 'Bà Rịa - Vũng Tàu',
        image: 'images/vungtau.jpg',
        description: 'Tham quan Tượng Chúa Kitô, Hải Đăng, tắm biển, thưởng thức hải sản...',
        features: ['Gần TP.HCM', 'Phù hợp cuối tuần', 'Giá tốt'],
        badge: '',
        rating: 4.3,
        reviews: 112
    },
    {
        id: 12,
        name: 'Cần Thơ - Miệt Vườn 2N1Đ',
        destination: 'canTho',
        type: 'culture',
        duration: '2 ngày 1 đêm',
        price: 2000000,
        location: 'Cần Thơ',
        image: 'images/cantho.jpg',
        description: 'Chợ nổi Cái Răng, vườn trái cây, ẩm thực miền Tây, đi ghe thăm làng nghề...',
        features: ['Chợ nổi', 'Vườn trái cây', 'Ẩm thực'],
        badge: 'Nổi bật',
        rating: 4.6,
        reviews: 143
    }
];

// ===== GLOBAL VARIABLES =====
let filteredTours = [...toursData];
let currentSort = 'popular';

// ===== RENDER TOURS =====
function renderTours(tours) {
    const toursList = document.getElementById('toursList');
    const tourCount = document.getElementById('tourCount');
    
    if (!toursList) return;
    
    tourCount.textContent = tours.length;
    
    if (tours.length === 0) {
        toursList.innerHTML = `
            <div style="text-align: center; padding: 60px 20px; grid-column: 1/-1;">
                <h3 style="color: #64748b; margin-bottom: 10px;">Không tìm thấy tour phù hợp</h3>
                <p style="color: #94a3b8;">Vui lòng thử lại với bộ lọc khác</p>
            </div>
        `;
        return;
    }
    
    toursList.innerHTML = tours.map(tour => `
        <div class="tour-item">
            <div class="tour-item-image">
                <img src="${tour.image}" alt="${tour.name}">
                ${tour.badge ? `<span class="tour-item-badge ${tour.badge.toLowerCase()}">${tour.badge}</span>` : ''}
            </div>
            <div class="tour-item-content">
                <div class="tour-item-header">
                    <h3>${tour.name}</h3>
                    <div class="tour-item-meta">
                        <span>⏱️ ${tour.duration}</span>
                        <span>📍 ${tour.location}</span>
                        <span>⭐ ${tour.rating} (${tour.reviews} đánh giá)</span>
                    </div>
                </div>
                <p class="tour-item-description">${tour.description}</p>
                <div class="tour-item-features">
                    ${tour.features.map(f => `<span class="feature-tag">${f}</span>`).join('')}
                </div>
                <div class="tour-item-footer">
                    <div class="tour-item-price">
                        <span class="price-label">Giá từ</span>
                        <div>
                            <span class="price-value">${formatPrice(tour.price)}</span>
                            ${tour.oldPrice ? `<span class="price-old">${formatPrice(tour.oldPrice)}</span>` : ''}
                        </div>
                    </div>
                    <div class="tour-item-actions">
                        <button class="btn-icon" onclick="addToWishlist(${tour.id})" title="Yêu thích">
                            ❤️
                        </button>
                        <a href="tour-detail.html?id=${tour.id}" class="btn btn-small">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// ===== FORMAT PRICE =====
function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(price);
}

// ===== FILTER TOURS =====
function applyFilters() {
    const destinationFilters = Array.from(document.querySelectorAll('input[name="destination"]:checked'))
        .map(cb => cb.value);
    const typeFilters = Array.from(document.querySelectorAll('input[name="type"]:checked'))
        .map(cb => cb.value);
    const priceFilters = Array.from(document.querySelectorAll('input[name="price"]:checked'))
        .map(cb => cb.value);
    const durationFilters = Array.from(document.querySelectorAll('input[name="duration"]:checked'))
        .map(cb => cb.value);
    
    filteredTours = toursData.filter(tour => {
        // Check destination
        if (destinationFilters.length > 0 && !destinationFilters.includes(tour.destination)) {
            return false;
        }
        
        // Check type
        if (typeFilters.length > 0 && !typeFilters.includes(tour.type)) {
            return false;
        }
        
        // Check price range
        if (priceFilters.length > 0) {
            const inPriceRange = priceFilters.some(range => {
                if (range === '0-3') return tour.price < 3000000;
                if (range === '3-5') return tour.price >= 3000000 && tour.price < 5000000;
                if (range === '5-10') return tour.price >= 5000000 && tour.price < 10000000;
                if (range === '10+') return tour.price >= 10000000;
                return false;
            });
            if (!inPriceRange) return false;
        }
        
        // Check duration
        if (durationFilters.length > 0) {
            const days = parseInt(tour.duration);
            const inDurationRange = durationFilters.some(range => {
                if (range === '1-3') return days >= 1 && days <= 3;
                if (range === '4-7') return days >= 4 && days <= 7;
                if (range === '7+') return days > 7;
                return false;
            });
            if (!inDurationRange) return false;
        }
        
        return true;
    });
    
    sortTours(currentSort);
    renderTours(filteredTours);
}

// ===== SORT TOURS =====
function sortTours(sortType) {
    currentSort = sortType;
    
    switch(sortType) {
        case 'price-low':
            filteredTours.sort((a, b) => a.price - b.price);
            break;
        case 'price-high':
            filteredTours.sort((a, b) => b.price - a.price);
            break;
        case 'duration':
            filteredTours.sort((a, b) => {
                const daysA = parseInt(a.duration);
                const daysB = parseInt(b.duration);
                return daysA - daysB;
            });
            break;
        case 'newest':
            filteredTours.sort((a, b) => b.id - a.id);
            break;
        default: // popular
            filteredTours.sort((a, b) => b.rating - a.rating);
    }
}

// ===== ADD TO WISHLIST =====
function addToWishlist(tourId) {
    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    
    if (!wishlist.includes(tourId)) {
        wishlist.push(tourId);
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        alert('Đã thêm vào danh sách yêu thích!');
    } else {
        alert('Tour này đã có trong danh sách yêu thích!');
    }
}

// ===== CLEAR FILTERS =====
function clearFilters() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });
    filteredTours = [...toursData];
    renderTours(filteredTours);
}

// ===== INITIALIZE =====
document.addEventListener('DOMContentLoaded', function() {
    // Render initial tours
    renderTours(filteredTours);
    
    // Apply filters button
    const applyFiltersBtn = document.getElementById('applyFilters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', applyFilters);
    }
    
    // Clear filters button
    const clearFiltersBtn = document.getElementById('clearFilters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearFilters);
    }
    
    // Sort select
    const sortSelect = document.getElementById('sortBy');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortTours(this.value);
            renderTours(filteredTours);
        });
    }
    
    // Check URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const destination = urlParams.get('destination');
    const type = urlParams.get('type');
    const duration = urlParams.get('duration');
    
    if (destination) {
        const checkbox = document.querySelector(`input[name="destination"][value="${destination}"]`);
        if (checkbox) checkbox.checked = true;
    }
    
    if (type) {
        const checkbox = document.querySelector(`input[name="type"][value="${type}"]`);
        if (checkbox) checkbox.checked = true;
    }
    
    if (duration) {
        const checkbox = document.querySelector(`input[name="duration"][value="${duration}"]`);
        if (checkbox) checkbox.checked = true;
    }
    
    // Apply filters if URL has parameters
    if (destination || type || duration) {
        applyFilters();
    }
});
