// ===== TAB FUNCTIONALITY =====
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and panes
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('active'));
            
            // Add active class to clicked button and corresponding pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
});

// ===== IMAGE GALLERY =====
function changeMainImage(imageSrc) {
    const mainImage = document.getElementById('mainImage');
    mainImage.style.opacity = '0';
    
    setTimeout(() => {
        mainImage.src = imageSrc;
        mainImage.style.opacity = '1';
    }, 200);
}

// ===== BOOKING FORM =====
const bookingForm = document.getElementById('bookingForm');
if (bookingForm) {
    const adultsSelect = document.getElementById('adults');
    const childrenSelect = document.getElementById('children');
    const totalPriceElement = document.getElementById('totalPrice');
    
    // Base price (will be loaded from URL or default)
    let basePrice = 2500000;
    
    // Calculate total price
    function calculateTotalPrice() {
        const adults = parseInt(adultsSelect.value) || 2;
        const children = parseInt(childrenSelect.value) || 0;
        
        // Adult price
        let total = basePrice * adults;
        
        // Children price (75% of adult price)
        total += (basePrice * 0.75) * children;
        
        totalPriceElement.textContent = formatCurrency(total);
    }
    
    // Update price when selection changes
    adultsSelect.addEventListener('change', calculateTotalPrice);
    childrenSelect.addEventListener('change', calculateTotalPrice);
    
    // Set minimum date for booking
    const bookingDateInput = document.getElementById('bookingDate');
    if (bookingDateInput) {
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        bookingDateInput.min = tomorrow.toISOString().split('T')[0];
    }
    
    // Handle form submission
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const bookingDate = document.getElementById('bookingDate').value;
        const adults = document.getElementById('adults').value;
        const children = document.getElementById('children').value;
        
        if (!bookingDate) {
            alert('Vui lòng chọn ngày khởi hành!');
            return;
        }
        
        // Get tour info
        const urlParams = new URLSearchParams(window.location.search);
        const tourId = urlParams.get('id') || 1;
        const tourName = document.getElementById('detailTourName').textContent;
        
        // Save booking info
        const bookingInfo = {
            tourId: tourId,
            tourName: tourName,
            date: bookingDate,
            adults: adults,
            children: children,
            totalPrice: totalPriceElement.textContent,
            bookingTime: new Date().toISOString()
        };
        
        // Save to localStorage
        localStorage.setItem('pendingBooking', JSON.stringify(bookingInfo));
        
        // Redirect to contact page with booking info
        window.location.href = 'contact.html?booking=true';
    });
}

// ===== LOAD TOUR DETAILS FROM URL =====
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tourId = urlParams.get('id');
    
    if (!tourId) return;
    
    // Sample tour data (in real application, this would come from a database)
    const tourDetails = {
        1: {
            name: 'Vịnh Hạ Long - Du Thuyền 2N1Đ',
            location: 'Quảng Ninh',
            price: 2500000,
            rating: 4.8,
            reviews: 156,
            description: 'Chuyến du lịch Vịnh Hạ Long 2 ngày 1 đêm mang đến cho bạn trải nghiệm tuyệt vời tại kỳ quan thiên nhiên thế giới...'
        },
        2: {
            name: 'Sa Pa - Fansipan 3N2Đ',
            location: 'Lào Cai',
            price: 3200000,
            rating: 4.9,
            reviews: 203,
            description: 'Chinh phục nóc nhà Đông Dương, khám phá bản làng dân tộc, thưởng thức ẩm thực núi rừng...'
        },
        3: {
            name: 'Đà Nẵng - Hội An - Bà Nà 4N3Đ',
            location: 'Đà Nẵng',
            price: 4500000,
            rating: 4.7,
            reviews: 187,
            description: 'Tham quan Cầu Vàng, phố cổ Hội An, tắm biển Mỹ Khê, check-in Bà Nà Hills...'
        },
        4: {
            name: 'Phú Quốc - Đảo Ngọc 4N3Đ',
            location: 'Kiên Giang',
            price: 5800000,
            rating: 4.9,
            reviews: 234,
            description: 'Nghỉ dưỡng tại đảo ngọc, tham quan VinWonders, lặn ngắm san hô, câu cá...'
        },
        5: {
            name: 'Nha Trang - Vinpearl 3N2Đ',
            location: 'Khánh Hòa',
            price: 3800000,
            rating: 4.6,
            reviews: 178,
            description: 'Tắm biển, tham quan đảo, vui chơi tại VinWonders, tắm bùn khoáng nóng...'
        },
        6: {
            name: 'Đà Lạt - Thành Phố Ngàn Hoa 3N2Đ',
            location: 'Lâm Đồng',
            price: 3500000,
            rating: 4.5,
            reviews: 145,
            description: 'Khám phá thành phố sương mù, tham quan thác Datanla, Crazy House, làng cù lần...'
        }
    };
    
    const tour = tourDetails[tourId];
    
    if (tour) {
        // Update page content
        document.getElementById('tourName').textContent = tour.name;
        document.getElementById('detailTourName').textContent = tour.name;
        document.getElementById('detailLocation').textContent = tour.location;
        document.getElementById('detailPrice').textContent = formatCurrency(tour.price);
        document.getElementById('detailRating').textContent = tour.rating;
        document.getElementById('detailReviews').textContent = tour.reviews;
        
        // Update description if element exists
        const descElement = document.getElementById('detailDescription');
        if (descElement && tour.description) {
            descElement.textContent = tour.description;
        }
        
        // Update base price for calculations
        basePrice = tour.price;
        
        // Calculate initial total price
        calculateTotalPrice();
        
        // Update page title
        document.title = tour.name + ' - VietTravel';
    }
});

// ===== FORMAT CURRENCY =====
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

// ===== SMOOTH SCROLL TO BOOKING =====
const bookNowButtons = document.querySelectorAll('[data-action="book-now"]');
bookNowButtons.forEach(button => {
    button.addEventListener('click', function() {
        const bookingBox = document.querySelector('.booking-box');
        if (bookingBox) {
            bookingBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
});

// ===== ADD TO WISHLIST =====
function addToWishlist() {
    const urlParams = new URLSearchParams(window.location.search);
    const tourId = urlParams.get('id');
    
    if (!tourId) return;
    
    let wishlist = JSON.parse(localStorage.getItem('wishlist') || '[]');
    
    if (!wishlist.includes(parseInt(tourId))) {
        wishlist.push(parseInt(tourId));
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        alert('Đã thêm vào danh sách yêu thích!');
    } else {
        alert('Tour này đã có trong danh sách yêu thích!');
    }
}

// ===== SHARE TOUR =====
function shareTour() {
    if (navigator.share) {
        const tourName = document.getElementById('detailTourName').textContent;
        navigator.share({
            title: tourName,
            text: 'Xem tour du lịch này: ' + tourName,
            url: window.location.href
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback: copy URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Đã sao chép link tour!');
        });
    }
}

// ===== PRINT TOUR INFO =====
function printTourInfo() {
    window.print();
}
