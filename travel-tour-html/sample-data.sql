-- ============================================
-- TravelViet – Sample Data
-- CT214H Web Programming Final Project
-- ============================================

USE travelviet;

-- ============================================
-- USERS (password = password_hash của "admin123" / "user123")
-- Để test: chạy PHP: echo password_hash('admin123', PASSWORD_DEFAULT);
-- Ở đây dùng giá trị hash mẫu
-- ============================================
INSERT INTO users (username, email, password, full_name, phone, role) VALUES
('admin',    'admin@travelviet.vn',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator',    '0901000000', 'admin'),
('vana',     'vana@travelviet.vn',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A',     '0901111111', 'user'),
('thib',     'thib@travelviet.vn',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B',       '0902222222', 'user'),
('mincc',    'mincc@travelviet.vn',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Minh C',        '0903333333', 'user'),
('phuongd',  'phuongd@travelviet.vn','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị Phương',  '0904444444', 'user');

-- ============================================
-- TOURS (8 tours)
-- ============================================
INSERT INTO tours (title, slug, description, destination, duration, price, max_people, available_spots, category, emoji, badge, status) VALUES

-- 1. Hạ Long
('Khám Phá Hạ Long Bay',
 'kham-pha-ha-long-bay',
 'Trải nghiệm vẻ đẹp huyền bí của Vịnh Hạ Long với hàng nghìn hòn đảo đá vôi kỳ vĩ. Tour bao gồm du thuyền cao cấp, hoạt động kayak và ẩm thực hải sản tươi ngon.',
 'Quảng Ninh', 3, 3500000, 20, 15, 'cruise', '🚢', 'HOT 🔥', 'active'),

-- 2. Hội An
('Phố Cổ Hội An Enchanting',
 'pho-co-hoi-an-enchanting',
 'Đắm chìm vào không khí cổ kính của phố cổ Hội An với ngôi nhà gỗ trăm năm và đèn lồng lung linh. Trải nghiệm văn hóa Việt – Nhật – Pháp độc đáo.',
 'Quảng Nam', 2, 1800000, 25, 20, 'cultural', '🏮', '', 'active'),

-- 3. Sapa
('Trekking Sapa Mây Bay',
 'trekking-sapa-may-bay',
 'Chinh phục đỉnh núi hùng vĩ của Sapa, khám phá bản làng dân tộc H''Mông, Dao Đỏ và ruộng bậc thang đẹp nhất Việt Nam.',
 'Lào Cai', 4, 4200000, 15, 10, 'mountain', '⛰️', 'NEW ⭐', 'active'),

-- 4. Đà Nẵng
('Đà Nẵng – Bà Nà Hills',
 'da-nang-ba-na-hills',
 'Thăm Cầu Vàng nổi tiếng thế giới và trải nghiệm thế giới thần tiên tại Bà Nà Hills với kiến trúc Pháp cổ điển trên độ cao 1.400m.',
 'Đà Nẵng', 3, 3200000, 30, 25, 'city', '🌉', '', 'active'),

-- 5. Phú Quốc
('Phú Quốc Paradise Island',
 'phu-quoc-paradise-island',
 'Tận hưởng bãi biển trong xanh của đảo ngọc Phú Quốc với cát trắng mịn và nước biển màu ngọc bích. Lặn biển ngắm san hô.',
 'Kiên Giang', 5, 6500000, 20, 18, 'beach', '🏝️', 'HOT 🔥', 'active'),

-- 6. Mũi Né
('Mũi Né Cát Vàng',
 'mui-ne-cat-vang',
 'Khám phá đồi cát vàng và cát đỏ huyền bí, trải nghiệm lướt ván diều trên biển xanh ngắt và làng chài cổ kính.',
 'Bình Thuận', 2, 2100000, 20, 12, 'beach', '🌊', '', 'active'),

-- 7. Ninh Bình
('Ninh Bình – Tràng An',
 'ninh-binh-trang-an',
 'Khám phá Tràng An – Di sản thiên nhiên và văn hóa thế giới với hang động kỳ bí và đền chùa cổ kính giữa núi non.',
 'Ninh Bình', 2, 1600000, 25, 20, 'cultural', '⛩️', '', 'active'),

-- 8. Côn Đảo
('Côn Đảo Hoang Sơ',
 'con-dao-hoang-so',
 'Trải nghiệm thiên nhiên hoang sơ của Côn Đảo với bãi biển đẹp nhất Việt Nam, lặn biển ngắm san hô và khám phá lịch sử hào hùng.',
 'Bà Rịa – Vũng Tàu', 4, 7200000, 12, 8, 'beach', '🌺', 'VIP ⭐', 'active');

-- ============================================
-- BOOKINGS (6 bookings mẫu)
-- ============================================
INSERT INTO bookings (booking_code, user_id, tour_id, tour_date, num_people, price_each, service_fee, total_price, pay_method, notes, status) VALUES

('TV240001', 2, 1, '2025-06-15', 2, 3500000, 140000, 7140000, 'bank',   'Xin phòng đôi trên thuyền', 'confirmed'),
('TV240002', 3, 5, '2025-07-20', 3, 6500000, 390000, 19890000, 'momo',  NULL,                          'pending'),
('TV240003', 2, 2, '2025-05-10', 2, 1800000,  72000,  3672000, 'cash',  'Ăn chay',                     'confirmed'),
('TV240004', 4, 3, '2025-08-05', 1, 4200000,  84000,  4284000, 'vnpay', NULL,                          'pending'),
('TV240005', 3, 4, '2025-06-01', 4, 3200000, 256000, 13056000, 'bank',  'Cần hướng dẫn viên tiếng Anh','cancelled'),
('TV240006', 5, 8, '2025-09-10', 2, 7200000, 288000, 14688000, 'bank',  NULL,                          'confirmed');

-- ============================================
-- REVIEWS (5 reviews mẫu)
-- ============================================
INSERT INTO reviews (user_id, tour_id, rating, comment) VALUES
(2, 1, 5, 'Tour tuyệt vời! Hướng dẫn viên nhiệt tình, cảnh đẹp mê hồn. Du thuyền rất sang trọng. Chắc chắn sẽ quay lại!'),
(3, 5, 5, 'Phú Quốc thiên đường thật sự! Biển trong xanh, đồ ăn ngon, dịch vụ 5 sao. Sẽ quay lại năm sau!'),
(2, 2, 4, 'Phố cổ Hội An rất đẹp và thơ mộng. Đèn lồng lung linh rất ấn tượng. Chỉ hơi đông vào ban ngày.'),
(4, 3, 5, 'Trekking Sapa là trải nghiệm tuyệt vời nhất trong đời! Phong cảnh hùng vĩ, người dân rất thân thiện.'),
(5, 8, 5, 'Côn Đảo là điểm đến tuyệt vời nhất tôi từng đến. Biển đẹp, không khí trong lành, lịch sử sâu sắc.');

-- ============================================
-- KIỂM TRA DỮ LIỆU
-- ============================================
-- SELECT * FROM users;
-- SELECT * FROM tours;
-- SELECT b.booking_code, u.full_name, t.title, b.total_price, b.status
--   FROM bookings b
--   JOIN users u ON b.user_id = u.id
--   JOIN tours t ON b.tour_id = t.id;
-- SELECT r.rating, r.comment, u.full_name, t.title
--   FROM reviews r
--   JOIN users u ON r.user_id = u.id
--   JOIN tours t ON r.tour_id = t.id;
