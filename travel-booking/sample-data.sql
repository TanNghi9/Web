-- ============================================
-- TRAVEL BOOKING WEBSITE - SAMPLE DATA
-- ============================================

USE travel_booking;

-- CATEGORIES
INSERT INTO categories (name, icon, description) VALUES
('Biển & Đảo', '🏖️', 'Khám phá các bãi biển và đảo đẹp của Việt Nam'),
('Núi & Cao Nguyên', '🏔️', 'Trải nghiệm thiên nhiên hùng vĩ miền núi'),
('Di Sản Văn Hóa', '🏛️', 'Tìm hiểu lịch sử và văn hóa đặc sắc'),
('Miền Quê', '🌾', 'Khám phá cuộc sống yên bình làng quê'),
('Nước Ngoài', '✈️', 'Hành trình đến các quốc gia nổi tiếng');

-- ADMIN USER (password: password)
INSERT INTO users (full_name, email, password, phone, role) VALUES
('Admin', 'admin@travelvn.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0900000000', 'admin');

-- SAMPLE USERS (password: 123456)
INSERT INTO users (full_name, email, password, phone, role) VALUES
('Nguyễn Văn An', 'an@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0901234567', 'user'),
('Trần Thị Bình', 'binh@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0912345678', 'user'),
('Lê Minh Cường', 'cuong@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0923456789', 'user');

-- TOURS
INSERT INTO tours (category_id, name, slug, description, highlights, itinerary, destination, duration_days, price, max_capacity, current_bookings, departure_date, status) VALUES
(1, 'Phú Quốc – Đảo Ngọc 4N3Đ', 'phu-quoc-dao-ngoc-4n3d',
 'Khám phá hòn đảo thiên đường với bãi biển cát trắng, nước biển xanh ngọc bích, và những hoàng hôn tuyệt đẹp. Tour bao gồm tham quan Vinpearl Safari, lặn ngắm san hô, và thưởng thức hải sản tươi ngon.',
 'Bãi biển cát trắng|Lặn ngắm san hô|Vinpearl Safari|Hoàng hôn tuyệt đẹp|Hải sản tươi ngon',
 'Ngày 1: Bay đến Phú Quốc, nhận phòng khách sạn, tắm biển buổi chiều\nNgày 2: Tham quan Vinpearl Safari và Grand World\nNgày 3: Lặn ngắm san hô, tham quan làng chài\nNgày 4: Mua sắm đặc sản, bay về',
 'Phú Quốc, Kiên Giang', 4, 3990000, 20, 8, '2025-07-15', 'active'),

(1, 'Đà Nẵng – Hội An 3N2Đ', 'da-nang-hoi-an-3n2d',
 'Hành trình khám phá thành phố biển năng động Đà Nẵng kết hợp phố cổ Hội An lãng mạn. Tận hưởng bãi biển Mỹ Khê, cầu Rồng phun lửa, và những con phố đèn lồng thơ mộng.',
 'Bãi biển Mỹ Khê|Cầu Vàng Bà Nà|Phố cổ Hội An|Cầu Rồng phun lửa|Ẩm thực đặc sắc',
 'Ngày 1: Bay đến Đà Nẵng, tham quan cầu Rồng buổi tối\nNgày 2: Bà Nà Hills, Cầu Vàng\nNgày 3: Hội An – Phố cổ, Chùa Cầu, thả đèn hoa đăng',
 'Đà Nẵng – Hội An', 3, 2890000, 25, 15, '2025-07-20', 'active'),

(2, 'Sapa – Chinh Phục Fansipan 3N2Đ', 'sapa-fansipan-3n2d',
 'Trải nghiệm khí hậu mát mẻ, ruộng bậc thang hùng vĩ và chinh phục nóc nhà Đông Dương – đỉnh Fansipan 3143m. Giao lưu với đồng bào dân tộc H\'Mông, Dao đỏ.',
 'Đỉnh Fansipan 3143m|Ruộng bậc thang|Bản làng dân tộc|Cáp treo Fansipan|Chợ tình Sapa',
 'Ngày 1: Tàu đêm Hà Nội – Lào Cai, lên Sapa\nNgày 2: Chinh phục Fansipan bằng cáp treo, tham quan bản làng\nNgày 3: Chợ phiên, mua đặc sản, tàu về Hà Nội',
 'Sapa, Lào Cai', 3, 2590000, 15, 5, '2025-08-01', 'active'),

(3, 'Hà Nội – Ninh Bình – Di Sản 4N3Đ', 'ha-noi-ninh-binh-4n3d',
 'Khám phá thủ đô ngàn năm văn hiến, di sản thiên nhiên thế giới Tràng An, và cố đô Hoa Lư. Hành trình văn hóa lịch sử đặc sắc từ Bắc vào Nam.',
 'Hồ Hoàn Kiếm|Tràng An|Cố đô Hoa Lư|Chùa Bái Đính|Phố cổ Hà Nội',
 'Ngày 1: Tham quan Hà Nội – Hồ Hoàn Kiếm, Văn Miếu\nNgày 2: Ninh Bình – Tràng An, Hoa Lư\nNgày 3: Chùa Bái Đính, Tam Cốc\nNgày 4: Mua sắm, kết thúc tour',
 'Hà Nội – Ninh Bình', 4, 3290000, 30, 12, '2025-07-25', 'active'),

(1, 'Nha Trang – Biển Xanh 3N2Đ', 'nha-trang-bien-xanh-3n2d',
 'Thiên đường nghỉ dưỡng biển với nước biển trong xanh, các đảo san hô rực rỡ và ẩm thực biển phong phú. Tour bao gồm lặn ngắm san hô, tham quan VinWonders.',
 'Bãi biển Nha Trang|Lặn ngắm san hô|VinWonders|Tháp Chàm Ponagar|Hải sản tươi sống',
 'Ngày 1: Bay đến Nha Trang, tắm biển, dạo phố đêm\nNgày 2: Tour 4 đảo, lặn ngắm san hô\nNgày 3: VinWonders, mua sắm, bay về',
 'Nha Trang, Khánh Hòa', 3, 2790000, 20, 3, '2025-08-10', 'active'),

(5, 'Thái Lan – Bangkok – Pattaya 5N4Đ', 'thai-lan-bangkok-pattaya-5n4d',
 'Hành trình khám phá đất nước Chùa Vàng với kinh đô Bangkok tráng lệ và bãi biển Pattaya sôi động. Mua sắm thiên đường, ẩm thực đường phố và lễ hội ánh sáng.',
 'Cung điện Hoàng gia|Chùa Wat Pho|Bãi biển Pattaya|Chợ nổi|Floating market',
 'Ngày 1: Bay đến Bangkok, nhận phòng\nNgày 2: Grand Palace, Wat Pho, Wat Arun\nNgày 3: Chợ nổi Damnoen Saduak, chợ Chatuchak\nNgày 4: Pattaya – bãi biển, Sanctuary of Truth\nNgày 5: Mua sắm, bay về',
 'Bangkok – Pattaya, Thái Lan', 5, 8990000, 20, 10, '2025-08-15', 'active'),

(2, 'Đà Lạt – Thành Phố Sương Mù 3N2Đ', 'da-lat-thanh-pho-suong-mu-3n2d',
 'Lạc vào thành phố mộng mơ với khí hậu mát lạnh, vườn hoa rực rỡ, đồi chè xanh ngút và những con dốc lãng mạn. Trải nghiệm xe đạp dạo phố, cắm trại và thưởng thức café Đà Lạt.',
 'Hồ Xuân Hương|Thung lũng Tình Yêu|Vườn hoa Đà Lạt|Đồi chè Cầu Đất|Ga xe lửa cổ',
 'Ngày 1: Lên Đà Lạt, hồ Xuân Hương, chợ đêm\nNgày 2: Thung lũng Tình Yêu, Vườn hoa thành phố, đồi chè\nNgày 3: Thiền Viện Trúc Lâm, mua sắm đặc sản',
 'Đà Lạt, Lâm Đồng', 3, 2490000, 25, 18, '2025-07-30', 'active'),

(4, 'Mekong Delta – Miền Tây Sông Nước 2N1Đ', 'mekong-delta-mien-tay-2n1d',
 'Trải nghiệm cuộc sống sông nước miền Tây với chợ nổi Cái Răng, vườn trái cây xanh mát, làng nghề truyền thống và hương vị ẩm thực đồng quê đậm chất Nam Bộ.',
 'Chợ nổi Cái Răng|Vườn trái cây|Làng nghề|Đờn ca tài tử|Ẩm thực miền Tây',
 'Ngày 1: Chợ nổi Cái Răng sáng sớm, vườn trái cây, làng nghề kẹo dừa\nNgày 2: Tham quan cồn Thới Sơn, nghe đờn ca tài tử, về TP.HCM',
 'Cần Thơ – Tiền Giang', 2, 1290000, 30, 20, '2025-08-05', 'active');

-- BOOKINGS
INSERT INTO bookings (user_id, tour_id, num_people, total_price, status, contact_name, contact_phone, special_requests) VALUES
(2, 1, 2, 7980000, 'confirmed', 'Nguyễn Văn An', '0901234567', 'Phòng view biển nếu có'),
(3, 2, 3, 8670000, 'confirmed', 'Trần Thị Bình', '0912345678', NULL),
(4, 6, 2, 17980000, 'pending', 'Lê Minh Cường', '0923456789', 'Cần hỗ trợ visa Thái Lan'),
(2, 4, 4, 13160000, 'confirmed', 'Nguyễn Văn An', '0901234567', NULL),
(3, 7, 2, 4980000, 'cancelled', 'Trần Thị Bình', '0912345678', NULL);

-- REVIEWS
INSERT INTO reviews (user_id, tour_id, rating, comment) VALUES
(2, 1, 5, 'Tour tuyệt vời! Hướng dẫn viên nhiệt tình, khách sạn sạch đẹp. Phú Quốc đẹp hơn tôi tưởng tượng nhiều!'),
(3, 2, 4, 'Hội An rất thơ mộng buổi tối. Tour tổ chức tốt, chỉ tiếc thời gian hơi ngắn.'),
(4, 6, 5, 'Bangkok – Pattaya quá xuất sắc! Sẽ đặt tour tiếp với công ty.');
