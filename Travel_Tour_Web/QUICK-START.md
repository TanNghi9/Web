# 🚀 QUICK START - VietTravel Website

## Bắt Đầu Nhanh trong 3 Bước

### Bước 1: Mở File
```
Mở file sitemap.html để xem tất cả các trang
HOẶC
Mở file index.html để bắt đầu từ trang chủ
```

### Bước 2: Khám Phá
- **sitemap.html** - Xem danh sách tất cả trang
- **index.html** - Trang chủ
- **tours.html** - Danh sách tours
- **tour-detail.html** - Chi tiết tour (thêm ?id=1 vào URL)
- **destinations.html** - Điểm đến
- **about.html** - Giới thiệu
- **contact.html** - Liên hệ
- **demo-preview.html** - Demo preview với ảnh placeholder

### Bước 3: Tùy Chỉnh (Tùy chọn)

#### Thay Đổi Màu Sắc
Mở `style.css`, tìm `:root` và thay đổi:
```css
--primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
--secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
--warm-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
```

#### Thêm Hình Ảnh
1. Tạo thư mục `images/`
2. Thêm ảnh: halong-bay.jpg, sapa.jpg, danang.jpg, etc.
3. Update src trong HTML files

## 📂 Cấu Trúc File

```
viettravel/
├── 📄 HTML Files (6 trang)
│   ├── index.html
│   ├── tours.html
│   ├── tour-detail.html
│   ├── destinations.html
│   ├── about.html
│   └── contact.html
│
├── 🎨 CSS Files
│   ├── style.css (Main CSS với gradients)
│   ├── tour-detail.css
│   └── contact.css
│
├── ⚡ JavaScript Files
│   ├── main.js (Core functionality)
│   ├── tours.js
│   ├── tour-detail.js
│   └── contact.js
│
└── 📚 Documentation
    ├── README.md (Chi tiết)
    ├── QUICK-START.md (File này)
    └── sitemap.html (Navigation)
```

## ✨ Tính Năng Chính

✅ **6 trang hoàn chỉnh** được liên kết với nhau  
✅ **Gradient hiện đại** - Purple, Pink, Blue, Yellow  
✅ **Glassmorphism** - Search box, header  
✅ **Smooth animations** - Hover, scroll, page transitions  
✅ **Responsive design** - Mobile, tablet, desktop  
✅ **Interactive elements** - Forms, filters, buttons  
✅ **Notification system** - Toast messages  
✅ **Scroll effects** - Scroll-to-top, animation on scroll  

## 🎯 Navigation Giữa Các Trang

Tất cả links trong navigation menu đã được kết nối:
- Trang chủ → `index.html`
- Tours → `tours.html`
- Điểm đến → `destinations.html`
- Giới thiệu → `about.html`
- Liên hệ → `contact.html`

## 💡 Tips

1. **Để xem tốt nhất**: Sử dụng Chrome, Firefox, hoặc Edge
2. **Responsive testing**: F12 → Toggle device toolbar
3. **Tùy chỉnh nhanh**: Tất cả màu sắc ở `:root` trong style.css
4. **Preview demo**: Mở `demo-preview.html` để xem thiết kế với ảnh placeholder

## 🆘 Cần Giúp Đỡ?

Đọc file `README.md` để biết thêm chi tiết về:
- Tùy chỉnh màu sắc & font
- Thêm hình ảnh
- JavaScript features
- SEO optimization
- Deployment tips

---

**Enjoy your beautiful website! 🎉**
