# 🌏 VietTravel - Website Du Lịch Việt Nam

## ✨ Thiết Kế Mới - Modern & Beautiful

Website du lịch VietTravel đã được thiết kế lại hoàn toàn với:
- **Gradient hiện đại**: Purple → Violet, Pink → Red, Blue → Cyan
- **UI/UX chuyên nghiệp**: Glassmorphism, smooth animations, modern typography
- **Màu sắc hài hòa**: Palette màu được chọn lọc kỹ càng
- **Responsive**: Hoạt động tốt trên mọi thiết bị

## 📁 Cấu Trúc File

```
viettravel/
│
├── index.html              # Trang chủ
├── tours.html              # Danh sách tours
├── tour-detail.html        # Chi tiết tour
├── destinations.html       # Điểm đến
├── about.html              # Giới thiệu
├── contact.html            # Liên hệ
│
├── style.css               # CSS chính với gradient đẹp
├── tour-detail.css         # CSS cho trang chi tiết tour
├── contact.css             # CSS cho trang liên hệ
│
├── main.js                 # JavaScript chính
├── tours.js                # Logic trang tours
├── tour-detail.js          # Logic trang chi tiết
├── contact.js              # Logic trang liên hệ
│
└── README.md               # File này
```

## 🎨 Tính Năng Nổi Bật

### 1. **Header với Glassmorphism**
- Backdrop blur effect
- Sticky navigation
- Gradient text logo
- Smooth hover animations

### 2. **Hero Section**
- Background gradient overlay
- Animated content (fade in up)
- Call-to-action button với ripple effect

### 3. **Search Box**
- Glass morphism design
- Smooth focus transitions
- Modern form controls

### 4. **Tour Cards**
- Hover zoom effect (scale 1.15)
- Gradient badges
- Price with gradient text
- Smooth shadow transitions

### 5. **Feature Cards**
- Top gradient border on hover
- Smooth lift effect
- Icon animations

### 6. **Newsletter Section**
- Full gradient background
- Radial overlay effect
- Modern input styling

### 7. **Footer**
- Dark gradient background
- Social media icons với hover effects
- Organized link structure

### 8. **Interactive Elements**
- Scroll-to-top button với gradient
- Toast notifications system
- Loading overlay
- Smooth scroll behavior
- Animation on scroll (intersection observer)

## 🚀 Cách Sử Dụng

### Bước 1: Mở File
Mở file `index.html` trong trình duyệt web (Chrome, Firefox, Safari, Edge).

### Bước 2: Thêm Hình Ảnh (Tùy chọn)
Hiện tại website sử dụng ảnh placeholder từ Unsplash. Để thêm ảnh của bạn:

1. Tạo thư mục `images/` cùng cấp với các file HTML
2. Thêm ảnh vào thư mục:
   - `halong-bay.jpg`
   - `sapa.jpg`
   - `danang.jpg`
   - `phuquoc.jpg`
   - `nhatrang.jpg`
   - `dalat.jpg`
   - v.v.

3. Cập nhật đường dẫn trong code (hoặc giữ nguyên để dùng ảnh Unsplash)

### Bước 3: Tùy Chỉnh

#### Thay Đổi Màu Sắc
Trong file `style.css`, tìm phần `:root` và thay đổi các biến:

```css
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --warm-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    /* ... */
}
```

#### Thay Đổi Font
File đã import font Inter từ Google Fonts. Để đổi font:

```html
<!-- Trong <head> của file HTML -->
<link href="https://fonts.googleapis.com/css2?family=TenFontBanChon:wght@400;500;600;700;800&display=swap" rel="stylesheet">
```

```css
/* Trong style.css */
body {
    font-family: 'TenFontBanChon', sans-serif;
}
```

## 🎯 Các Trang Web

### 1. **Trang Chủ (index.html)**
- Hero section với gradient
- Search box
- Featured tours
- Điểm đến phổ biến
- Testimonials
- Newsletter signup

### 2. **Tours (tours.html)**
- Sidebar filters (điểm đến, loại tour, giá, thời gian)
- Sắp xếp tours
- Grid layout responsive
- Pagination

### 3. **Chi Tiết Tour (tour-detail.html)**
- Thông tin chi tiết tour
- Lịch trình
- Bao gồm / Không bao gồm
- Form đặt tour
- Tours liên quan

### 4. **Điểm Đến (destinations.html)**
- Grid các điểm đến
- Hover effects đẹp mắt
- Thông tin tours cho mỗi điểm

### 5. **Giới Thiệu (about.html)**
- Thông tin công ty
- Đội ngũ
- Giá trị cốt lõi

### 6. **Liên Hệ (contact.html)**
- Form liên hệ
- Thông tin liên lạc
- Bản đồ (có thể thêm Google Maps)

## 📱 Responsive Design

Website tự động điều chỉnh cho:
- **Desktop**: >768px - Full layout
- **Tablet**: 768px - 480px - Adjusted grid
- **Mobile**: <480px - Single column, hamburger menu

## 🔧 JavaScript Features

### main.js
- Mobile menu toggle
- Smooth scrolling
- Scroll-to-top button
- Notification system
- Animation on scroll
- Loading overlay

### tours.js
- Tour filtering
- Tour sorting
- Dynamic tour cards generation
- Pagination

### tour-detail.js
- Tour booking form
- Gallery lightbox
- Related tours

### contact.js
- Contact form validation
- Form submission handling

## 🌟 Tips & Tricks

### 1. **Tối Ưu Hiệu Suất**
- Sử dụng lazy loading cho ảnh
- Minify CSS và JS trước khi deploy
- Optimize images (WebP format)

### 2. **SEO**
- Thêm meta tags trong `<head>`
- Thêm alt text cho images
- Tạo sitemap.xml

### 3. **Tùy Chỉnh Thêm**
- Thêm animations với AOS library
- Tích hợp Google Analytics
- Thêm chatbot support

## 📞 Liên Hệ & Hỗ Trợ

Nếu có thắc mắc về website:
- Email: info@viettravel.vn
- Phone: 0123 456 789
- Địa chỉ: Đường 3/2, Ninh Kiều, Cần Thơ

## 📝 License

© 2026 VietTravel. All rights reserved.
Designed with modern gradients & beautiful UI ✨

---

**Chúc bạn thành công với website VietTravel! 🚀**
