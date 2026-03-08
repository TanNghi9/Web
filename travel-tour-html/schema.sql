-- ============================================
-- TravelViet – Database Schema
-- CT214H Web Programming Final Project
-- ============================================

CREATE DATABASE IF NOT EXISTS travelviet
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE travelviet;

-- ============================================
-- 1. USERS
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,          -- Hashed (bcrypt/password_hash)
    full_name   VARCHAR(100),
    phone       VARCHAR(20),
    avatar      VARCHAR(255) DEFAULT NULL,
    role        ENUM('admin','user') DEFAULT 'user',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role  (role)
) ENGINE=InnoDB;

-- ============================================
-- 2. TOURS
-- ============================================
CREATE TABLE IF NOT EXISTS tours (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(200) NOT NULL,
    slug            VARCHAR(200) NOT NULL UNIQUE,
    description     TEXT,
    destination     VARCHAR(100) NOT NULL,
    duration        INT  NOT NULL DEFAULT 1,     -- số ngày
    price           DECIMAL(12,0) NOT NULL,      -- VNĐ
    max_people      INT  NOT NULL DEFAULT 20,
    available_spots INT  NOT NULL DEFAULT 20,
    category        ENUM('beach','mountain','city','cultural','adventure','cruise') DEFAULT 'city',
    image           VARCHAR(255) DEFAULT NULL,
    emoji           VARCHAR(10)  DEFAULT '✈',
    badge           VARCHAR(50)  DEFAULT '',
    schedule        TEXT,                        -- JSON string của lịch trình
    includes_list   TEXT,                        -- JSON string
    excludes_list   TEXT,                        -- JSON string
    status          ENUM('active','inactive') DEFAULT 'active',
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status   (status),
    INDEX idx_price    (price)
) ENGINE=InnoDB;

-- ============================================
-- 3. BOOKINGS
-- ============================================
CREATE TABLE IF NOT EXISTS bookings (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    booking_code    VARCHAR(20)  NOT NULL UNIQUE,
    user_id         INT          NOT NULL,
    tour_id         INT          NOT NULL,
    tour_date       DATE         NOT NULL,
    num_people      INT          NOT NULL DEFAULT 1,
    price_each      DECIMAL(12,0) NOT NULL,
    service_fee     DECIMAL(12,0) NOT NULL DEFAULT 0,
    total_price     DECIMAL(12,0) NOT NULL,
    pay_method      ENUM('bank','cash','momo','vnpay') DEFAULT 'bank',
    notes           TEXT         DEFAULT NULL,
    status          ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (tour_id) REFERENCES tours(id)  ON DELETE CASCADE,
    INDEX idx_user_id   (user_id),
    INDEX idx_tour_id   (tour_id),
    INDEX idx_status    (status),
    INDEX idx_tour_date (tour_date)
) ENGINE=InnoDB;

-- ============================================
-- 4. REVIEWS
-- ============================================
CREATE TABLE IF NOT EXISTS reviews (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    tour_id     INT NOT NULL,
    rating      TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment     TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    UNIQUE KEY uq_user_tour (user_id, tour_id),   -- mỗi user chỉ review 1 lần/tour
    INDEX idx_tour_id (tour_id),
    INDEX idx_rating  (rating)
) ENGINE=InnoDB;
