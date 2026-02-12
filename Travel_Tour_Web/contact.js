// ===== CONTACT FORM HANDLER =====
const contactForm = document.getElementById('contactForm');
const successMessage = document.getElementById('successMessage');

if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const fullName = document.getElementById('fullName').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value.trim();
        const agree = document.getElementById('agree').checked;
        
        // Validation
        if (!fullName || !email || !phone || !message) {
            alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            return;
        }
        
        if (!agree) {
            alert('Vui lòng đồng ý với điều khoản và chính sách!');
            return;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Email không hợp lệ!');
            return;
        }
        
        // Phone validation
        const phoneRegex = /^[0-9]{10,11}$/;
        if (!phoneRegex.test(phone)) {
            alert('Số điện thoại không hợp lệ! Vui lòng nhập 10-11 số.');
            return;
        }
        
        // Create contact data object
        const contactData = {
            fullName,
            email,
            phone,
            subject: subject || 'Khác',
            message,
            timestamp: new Date().toISOString()
        };
        
        // Save to localStorage (in real app, this would be sent to server)
        let contacts = JSON.parse(localStorage.getItem('contacts') || '[]');
        contacts.push(contactData);
        localStorage.setItem('contacts', JSON.stringify(contacts));
        
        // Show success message
        contactForm.style.display = 'none';
        successMessage.style.display = 'block';
        
        // Reset form after 3 seconds and show it again
        setTimeout(() => {
            contactForm.reset();
            contactForm.style.display = 'block';
            successMessage.style.display = 'none';
        }, 5000);
    });
}

// ===== FAQ ACCORDION =====
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', function() {
            // Close all other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                }
            });
            
            // Toggle current item
            item.classList.toggle('active');
        });
    });
});

// ===== CHECK FOR BOOKING INFO =====
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const isBooking = urlParams.get('booking');
    
    if (isBooking === 'true') {
        // Get pending booking info from localStorage
        const bookingInfo = JSON.parse(localStorage.getItem('pendingBooking') || 'null');
        
        if (bookingInfo) {
            // Pre-fill form
            const messageField = document.getElementById('message');
            const subjectField = document.getElementById('subject');
            
            if (subjectField) {
                subjectField.value = 'booking';
            }
            
            if (messageField) {
                const bookingMessage = `Đặt tour: ${bookingInfo.tourName}\n` +
                                     `Ngày khởi hành: ${bookingInfo.date}\n` +
                                     `Số người lớn: ${bookingInfo.adults}\n` +
                                     `Số trẻ em: ${bookingInfo.children}\n` +
                                     `Tổng giá: ${bookingInfo.totalPrice}\n\n` +
                                     `Vui lòng liên hệ tôi để xác nhận đặt tour.`;
                messageField.value = bookingMessage;
            }
            
            // Clear pending booking
            localStorage.removeItem('pendingBooking');
            
            // Scroll to form
            contactForm.scrollIntoView({ behavior: 'smooth' });
        }
    }
});

// ===== PHONE NUMBER FORMATTING =====
const phoneInput = document.getElementById('phone');
if (phoneInput) {
    phoneInput.addEventListener('input', function(e) {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Limit to 11 digits
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }
    });
}

// ===== REAL-TIME VALIDATION =====
const emailInput = document.getElementById('email');
if (emailInput) {
    emailInput.addEventListener('blur', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (this.value && !emailRegex.test(this.value)) {
            this.style.borderColor = '#ef4444';
        } else {
            this.style.borderColor = '';
        }
    });
}
