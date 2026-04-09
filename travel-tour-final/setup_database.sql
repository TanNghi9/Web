-- ============================================================
-- Travel Tour Website – Database Setup
-- ============================================================
DROP DATABASE IF EXISTS traveltour_db;
CREATE DATABASE IF NOT EXISTS traveltour_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE traveltour_db;

CREATE TABLE IF NOT EXISTS categories (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL UNIQUE,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon        VARCHAR(10) DEFAULT '✈️',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tours (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(255) NOT NULL,
    slug         VARCHAR(255) NOT NULL UNIQUE,
    description  TEXT NOT NULL,
    content      TEXT,
    price        BIGINT NOT NULL DEFAULT 0,
    duration     INT NOT NULL DEFAULT 1,
    max_people   INT NOT NULL DEFAULT 10,
    destination  VARCHAR(255) NOT NULL,
    category_id  INT NOT NULL,
    image_url    VARCHAR(255),
    status       ENUM('active','inactive') DEFAULT 'active',
    views        INT DEFAULT 0,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bookings (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    tour_id      INT NOT NULL,
    full_name    VARCHAR(100) NOT NULL,
    email        VARCHAR(150) NOT NULL,
    phone        VARCHAR(20) NOT NULL,
    num_people   INT NOT NULL DEFAULT 1,
    travel_date  DATE NOT NULL,
    message      TEXT,
    status       ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    total_price  BIGINT NOT NULL DEFAULT 0,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
);

INSERT INTO categories (name, slug, description, icon) VALUES
('Beach & Islands',    'beach-islands',     'Pristine coastlines, turquoise waters and stunning tropical islands.',        '🏖️'),
('Mountains & Trekking','mountains-trekking','Majestic peaks and ethnic minority villages of Vietnam highlands.',          '🏔️'),
('Culture & Heritage', 'culture-heritage',  'Ancient citadels, UNESCO towns and Vietnam thousand-year civilization.',     '🏛️'),
('City Tours',         'city-tours',        'Vibrant cityscapes and historic streets of Vietnam major cities.',           '🌆'),
('Adventure',          'adventure',         'Heart-pumping journeys through Vietnam wildest landscapes.',                  '🧗'),
('Food & Countryside', 'food-countryside',  'Floating markets, rice paddies and authentic local Vietnamese flavors.',     '🍜');

INSERT INTO tours (title, slug, description, content, price, duration, max_people, destination, category_id, image_url, views) VALUES
(
    'Ha Long Bay & Ninh Binh Discovery',
    'ha-long-ninh-binh',
    'Discover the natural wonder of Ha Long Bay alongside the poetic ancient capital of Ninh Binh in this unforgettable 5-day northern Vietnam journey.',
    'Day 1: Pick-up in Hanoi. Transfer to Tuan Chau Harbour. Board traditional junk boat cruise around Ha Long Bay. Visit Surprising Cave, kayak through limestone karsts. Overnight on board.\n\nDay 2: Sunrise over the bay. Visit Cua Van floating fishing village. Return to port, transfer to Ninh Binh.\n\nDay 3: Trang An Landscape Complex – Vietnam''s dual UNESCO Heritage Site. Row through cave tunnels. Visit Hoa Lu Citadel.\n\nDay 4: Climb Mua Cave mountain for panoramic views. Tour Bich Dong Pagoda nestled inside a natural cave.\n\nDay 5: Free morning. Transfer back to Hanoi.',
    14900000, 5, 15, 'Ha Long & Ninh Binh, Vietnam', 1,
    '/images/halong.jpg', 487
),
(
    'Sapa & Fansipan Summit Trek',
    'sapa-fansipan',
    'Trek through golden rice terraces, visit ethnic minority villages, and summit Fansipan – the Roof of Indochina – in this epic 5-day highland adventure.',
    'Day 1: Overnight train from Hanoi to Lao Cai. Transfer to Sapa. Check in, stroll the night market.\n\nDay 2: Trek to Cat Cat village of the Black H''Mong. Waterfall and brocade weaving. Visit Ta Van village.\n\nDay 3: Conquer Fansipan (3,143m) by cable car and trekking trail. Panoramic views of Hoang Lien Son.\n\nDay 4: Trek to Lao Chai through rice terrace scenery. Cultural exchange with H''Mong families.\n\nDay 5: Return to Lao Cai. Overnight train back to Hanoi.',
    11200000, 5, 12, 'Sapa, Lao Cai, Vietnam', 2,
    '/images/sapa.jpg', 362
),
(
    'Hoi An Ancient Town & Da Nang Beach',
    'hoi-an-da-nang',
    'Wander the lantern-lit streets of UNESCO-listed Hoi An Ancient Town, swim at My Khe Beach, and uncover the Marble Mountains in this 4-day central Vietnam escape.',
    'Day 1: Fly into Da Nang. Visit Museum of Cham Sculpture. Evening stroll across Dragon Bridge.\n\nDay 2: Marble Mountains – limestone hills with caves and shrines. Afternoon at My Khe Beach.\n\nDay 3: Travel to Hoi An. Explore UNESCO Ancient Town: Japanese Covered Bridge, Tan Ky Old House. Evening lantern floating on Hoai River.\n\nDay 4: Cycle to Tra Que Herb Village. Hands-on cooking class. Departure.',
    9900000, 4, 18, 'Hoi An & Da Nang, Vietnam', 3,
    '/images/dannang.webp', 541
),
(
    'Hue – Imperial Capital of Vietnam',
    'hue-imperial-capital',
    'Step inside the last imperial capital of Vietnam – explore the Citadel, royal tombs, and taste the refined cuisine of the Nguyen royal court in this 3-day experience.',
    'Day 1: Arrive in Hue. Dragon boat cruise on Perfume River to Thien Mu Pagoda. Visit Imperial Citadel and Forbidden Purple City.\n\nDay 2: Royal tomb trail by motorbike – Emperor Minh Mang and Tu Duc tombs. Evening royal court music (Nha Nhac) performance.\n\nDay 3: Dong Ba Market for local specialties. Visit Bao Quoc Pagoda. Departure.',
    7500000, 3, 16, 'Hue, Vietnam', 3,
    '/images/hue.jpg', 389
),
(
    'Phu Quoc Island – Pearl of the East',
    'phu-quoc-pearl-island',
    'Immerse yourself in Vietnam''s largest island with pristine beaches, vibrant coral reefs and fresh seafood in this 5-day tropical paradise journey.',
    'Day 1: Fly to Phu Quoc. Check in beachfront resort. Afternoon at Bai Sao Beach. Evening at night market.\n\nDay 2: Snorkeling tour at An Thoi Archipelago. Visit Phu Quoc fish sauce factory and fishing village.\n\nDay 3: Hon Thom Cable Car – world''s longest overwater cable car (7,899m). Sun World water park.\n\nDay 4: Phu Quoc National Park trekking. Sea kayaking around small islands. Sunset at Mui Ong Doi.\n\nDay 5: Shopping for local specialties – fish sauce, pepper, sim wine. Departure.',
    13500000, 5, 16, 'Phu Quoc, Kien Giang, Vietnam', 1,
    '/images/phuquoc.jpg', 689
),
(
    'Hanoi – City of a Thousand Years',
    'hanoi-thousand-years',
    'Explore the tree-lined boulevards, ancient temples, and French colonial grandeur of Vietnam''s capital – a city where every corner holds a thousand years of history.',
    'Day 1: Hoan Kiem Lake and Ngoc Son Temple. Evening in the 36 ancient trade streets of the Old Quarter.\n\nDay 2: Ho Chi Minh Mausoleum, One Pillar Pagoda, Temple of Literature.\n\nDay 3: Day trip to Perfume Pagoda – boat ride through karst scenery to a sacred cave pagoda.\n\nDay 4: Museum of Ethnology. Cyclo ride through Old Quarter, street food tour at dusk.\n\nDay 5: Dong Xuan Market. Water Puppet Theatre. Departure.',
    9500000, 5, 20, 'Hanoi, Vietnam', 4,
    '/images/hanoi.jpg', 523
),
(
    'Ho Chi Minh City – Saigon Pulse',
    'ho-chi-minh-saigon',
    'Feel the electric pulse of Vietnam''s most dynamic metropolis – from historic war museums and colonial landmarks to rooftop bars and the world''s most exciting street food scene.',
    'Day 1: Reunification Palace, Notre-Dame Cathedral, Central Post Office. Evening at Bui Vien Walking Street.\n\nDay 2: Cu Chi Tunnels – 250km underground network used during the Vietnam War. Afternoon War Remnants Museum.\n\nDay 3: Cho Lon Chinatown. Binh Tay Market, Thien Hau Pagoda. Ben Thanh Market.\n\nDay 4: Day trip to Mekong Delta – floating markets and tropical fruit orchards. Departure.',
    10500000, 4, 20, 'Ho Chi Minh City, Vietnam', 4,
    '/images/hochiminh.jpg', 612
),
(
    'Can Tho – Heart of the Mekong Delta',
    'can-tho-mekong',
    'Drift through floating markets at dawn, cruise the Mekong waterways, and taste the bold flavors of southern Vietnamese cuisine in this immersive 4-day adventure.',
    'Day 1: Arrive in Can Tho. Visit Ninh Kieu Wharf. Boat cruise on Can Tho River. Night market dinner.\n\nDay 2: Cai Rang Floating Market at sunrise – dozens of wooden boats loaded with tropical fruits. Visit a rice noodle workshop. Cycling through fruit orchards.\n\nDay 3: Full-day boat tour through Mekong waterway network. Phong Dien floating market, brick kiln village, honeybee farm. Overnight homestay with local family.\n\nDay 4: Morning Tai Chi by the river. Visit Binh Thuy Ancient House – 130-year-old French-Vietnamese colonial mansion. Transfer to airport.',
    8700000, 4, 16, 'Can Tho, Mekong Delta, Vietnam', 6,
    '/images/cantho.jpg', 298
),
(
    'Da Lat – City of Eternal Spring',
    'da-lat-eternal-spring',
    'Escape to the cool highlands of Da Lat – a city of eternal spring, pine forests, romantic waterfalls, and colorful flower farms set amid the misty Central Highlands.',
    'Day 1: Arrive Da Lat. Xuan Huong Lake, Crazy House. Da Lat Night Market – strawberry wine and grilled corn.\n\nDay 2: Flower farm tour – hydrangea, rose, sunflower. Langbiang Mountain trek and panoramic views.\n\nDay 3: Elephant Waterfall and Datanla Waterfall. Valley of Love, Bao Dai Summer Palace.\n\nDay 4: Coffee and silk worm farm. Cycling through pine forests and strawberry plantations. Departure.',
    8700000, 4, 15, 'Da Lat, Lam Dong, Vietnam', 5,
    '/images/dalat.jpg', 467
),
(
    'Nha Trang – Pearl of the South China Sea',
    'nha-trang-pearl',
    'Relax on one of Asia''s finest urban beaches, island-hop through turquoise waters, and explore ancient Cham towers in this vibrant 5-day coastal retreat.',
    'Day 1: Arrive Nha Trang. Afternoon at Nha Trang Beach. Sunset at Thap Ba Ponagar Cham towers.\n\nDay 2: Full-day island-hopping – Hon Mun snorkeling, Hon Tam beach, Hon Mieu seafood lunch.\n\nDay 3: Long Son Pagoda. Alexandre Yersin Museum. Vinpearl Island via the world''s longest overwater cable car.\n\nDay 4: Mud bath therapy at Thap Ba Hot Spring. Ba Ho Waterfall in jungle valley.\n\nDay 5: Nha Trang market. Departure.',
    11900000, 5, 18, 'Nha Trang, Khanh Hoa, Vietnam', 1,
    '/images/nhatrang.jpg', 421
),
(
    'Ban Gioc Waterfall & Cao Bang Explorer',
    'ban-gioc-cao-bang',
    'Witness Southeast Asia''s largest waterfall, explore mystical caves and wild limestone plateaus in this breathtaking 4-day northern frontier adventure.',
    'Day 1: Depart from Hanoi to Cao Bang. Explore city market, taste local specialties.\n\nDay 2: Ban Gioc Waterfall – majestic twin falls 300m wide. Bamboo boat ride to view the falls up close. Nguom Ngao Cave – 3km of stunning stalactites.\n\nDay 3: Thang Hen Lake – a chain of 36 interconnected lakes hidden in limestone valleys. Tay ethnic minority village, bamboo rice and smoked buffalo meat.\n\nDay 4: Pac Bo – historic site where President Ho Chi Minh lived during the resistance. Return to Hanoi.',
    8900000, 4, 14, 'Ban Gioc, Cao Bang, Vietnam', 2,
    '/images/thacbangioc.jpg', 334
),
(
    'Vung Tau – Beach Escape from Saigon',
    'vung-tau-beach',
    'Escape the Saigon hustle to this charming coastal city just 2 hours away – beach swimming, mountain hiking, seafood feasts and the iconic Christ the King statue.',
    'Day 1: High-speed boat from Ho Chi Minh City to Vung Tau (1.5 hrs). Check in beachfront hotel. Afternoon at Bai Sau Beach. Evening grilled seafood and Banh Khot.\n\nDay 2: Hike Nui Lon mountain (245m) to Christ the King statue – panoramic city and sea views. Visit Bach Dinh Villa (1898 French colonial mansion). Afternoon swim at Bai Truoc Beach.\n\nDay 3: Vung Tau Lighthouse – one of Vietnam''s oldest (1862). Thich Ca Phat Dai Buddhist park. Shopping for local specialties.\n\nDay 4: Final morning swim. High-speed boat back to Ho Chi Minh City.',
    6500000, 4, 20, 'Vung Tau, Ba Ria - Vung Tau, Vietnam', 1,
    '/images/vungtau.jpg', 445
);
