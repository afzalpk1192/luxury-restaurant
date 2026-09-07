-- ==============================================================================
-- Elite Estates - Luxury Real Estate Database Schema
-- Charset: utf8mb4, Collation: utf8mb4_unicode_ci
-- Engine: InnoDB
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `elite_estates` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `elite_estates`;

-- ------------------------------------------------------------------------------
-- 1. Table: properties
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `properties` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ref_code` VARCHAR(50) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `tag` VARCHAR(50) DEFAULT 'For Sale',
    `property_type` VARCHAR(50) NOT NULL,
    `location` VARCHAR(150) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `state` VARCHAR(50) NOT NULL,
    `address` VARCHAR(255) DEFAULT NULL,
    `price` DECIMAL(14, 2) NOT NULL,
    `beds` INT UNSIGNED NOT NULL DEFAULT 1,
    `baths` DECIMAL(3, 1) NOT NULL DEFAULT 1.0,
    `sqft` INT UNSIGNED NOT NULL,
    `lot_size` VARCHAR(50) DEFAULT NULL,
    `year_built` SMALLINT UNSIGNED DEFAULT NULL,
    `featured_image` TEXT NOT NULL,
    `description` TEXT NOT NULL,
    `amenities` JSON DEFAULT NULL,
    `status` ENUM('active', 'pending', 'sold', 'draft') NOT NULL DEFAULT 'active',
    `is_featured` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_properties_location` (`location`),
    INDEX `idx_properties_type` (`property_type`),
    INDEX `idx_properties_price` (`price`),
    INDEX `idx_properties_status` (`status`),
    INDEX `idx_properties_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. Table: property_images (Gallery for each property)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `property_images` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT UNSIGNED NOT NULL,
    `image_url` TEXT NOT NULL,
    `display_order` INT NOT NULL DEFAULT 0,
    `caption` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_property_images_property`
        FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_property_images_property_id` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Table: inquiries (Lead capture / viewing requests)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inquiries` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT UNSIGNED DEFAULT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(50) NOT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('new', 'in_review', 'contacted', 'closed') NOT NULL DEFAULT 'new',
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_inquiries_property`
        FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_inquiries_status` (`status`),
    INDEX `idx_inquiries_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. Table: subscribers (Newsletter recipients)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `subscribers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `status` ENUM('subscribed', 'unsubscribed') NOT NULL DEFAULT 'subscribed',
    `subscribed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_subscribers_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. Table: admins (Platform management staff)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL DEFAULT 'Estate Administrator',
    `role` ENUM('superadmin', 'agent') NOT NULL DEFAULT 'superadmin',
    `last_login` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
