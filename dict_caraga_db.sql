-- =====================================================================
-- DICT Caraga - PMT Training Monitoring & Analytics Web Portal
-- Database Setup Script for phpMyAdmin (XAMPP)
-- =====================================================================
-- HOW TO USE:
-- 1. Start Apache and MySQL in the XAMPP Control Panel.
-- 2. Open http://localhost/phpmyadmin in your browser.
-- 3. Click the "Import" tab at the top.
-- 4. Click "Choose File" and select this file (dict_caraga_db.sql).
-- 5. Scroll down and click "Go".
-- This will create the `dict_caraga_db` database, its 3 tables, and
-- seed them with the same starting data the PHP file uses.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `dict_caraga_db`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `dict_caraga_db`;

-- ---------------------------------------------------------------------
-- Table: office_allocations
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `office_allocations` (
    `office_name` VARCHAR(50) NOT NULL PRIMARY KEY,
    `target` INT NOT NULL DEFAULT 0,
    `budget` DECIMAL(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB;

INSERT INTO `office_allocations` (`office_name`, `target`, `budget`) VALUES
('Regional Office', 5, 500000.00),
('Butuan City', 6, 200000.00),
('Agusan del Norte', 5, 150000.00),
('Agusan del Sur', 7, 250000.00),
('Surigao del Norte', 6, 200000.00),
('Surigao del Sur', 6, 200000.00),
('Dinagat Islands', 5, 150000.00)
ON DUPLICATE KEY UPDATE `office_name` = `office_name`;

-- ---------------------------------------------------------------------
-- Table: pmt_downloads
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pmt_downloads` (
    `id` VARCHAR(50) NOT NULL PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `target_trainings` INT NOT NULL DEFAULT 0,
    `unit_budget` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `subaro_code` VARCHAR(100) NOT NULL,
    `uacs_code` VARCHAR(100) NOT NULL,
    `course_type` VARCHAR(100) NOT NULL,
    `duration_hours` VARCHAR(100) NOT NULL,
    `drive_link` TEXT NOT NULL
) ENGINE=InnoDB;

INSERT INTO `pmt_downloads`
(`id`, `title`, `target_trainings`, `unit_budget`, `subaro_code`, `uacs_code`, `course_type`, `duration_hours`, `drive_link`)
VALUES
('dl-101', 'FY 2026 Free Wi-Fi for All Tech Operations & Fiber Deployments', 15, 80000.00, 'SUB-ARO-CO-26-4402', '5020201000', 'ICT Training', '16h, 20h, 40h', 'https://drive.google.com/drive/folders/1b-DICT-FreeWifi2026Caraga-example'),
('dl-102', 'FY 2026 Executive Cyber Security Readiness Assessment', 10, 120000.00, 'SUB-ARO-CO-26-8901', '5020201002', 'ICT Training', 'Full Catalog (All Speeds)', 'https://drive.google.com/drive/folders/1c-DICT-CybersecAuditingCaraga-example')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- ---------------------------------------------------------------------
-- Table: trainings
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `trainings` (
    `id` VARCHAR(50) NOT NULL PRIMARY KEY,
    `training_title` VARCHAR(255) NOT NULL,
    `course_code` VARCHAR(100) NOT NULL,
    `course_name` VARCHAR(255) NOT NULL,
    `province` VARCHAR(100) NOT NULL,
    `municipality` VARCHAR(100) NOT NULL,
    `barangay` VARCHAR(100) NOT NULL,
    `venue` VARCHAR(255) NOT NULL,
    `latitude` DECIMAL(10,8) NOT NULL,
    `longitude` DECIMAL(11,8) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `course_officer` VARCHAR(150) NOT NULL,
    `resource_person` VARCHAR(150) NOT NULL,
    `target_participants` INT NOT NULL DEFAULT 0,
    `male_participants` INT NOT NULL DEFAULT 0,
    `female_participants` INT NOT NULL DEFAULT 0,
    `actual_participants` INT NOT NULL DEFAULT 0,
    `budget_allocated` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `budget_utilized` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('completed', 'ongoing', 'upcoming', 'cancelled', 'rescheduled') NOT NULL DEFAULT 'upcoming',
    `drive_link` TEXT NULL,
    `photos` TINYINT(1) NOT NULL DEFAULT 0,
    `documents` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (`province`),
    INDEX (`status`)
) ENGINE=InnoDB;

INSERT INTO `trainings`
(`id`, `training_title`, `course_code`, `course_name`, `province`, `municipality`, `barangay`, `venue`,
 `latitude`, `longitude`, `start_date`, `end_date`, `course_officer`, `resource_person`,
 `target_participants`, `male_participants`, `female_participants`, `actual_participants`,
 `budget_allocated`, `budget_utilized`, `status`, `drive_link`, `photos`, `documents`)
VALUES
('tr-101', 'Information Security Management System Compliance Auditor', 'DICT-SEC-301', 'ISMS Compliance auditing', 'Regional Office', 'Butuan City', 'Doongan', 'DICT Regional Office Conference Hall', 8.95150000, 125.53500000, '2026-06-01', '2026-06-05', 'Maria Fe S. Lopez', 'Engr. Ricardo Salvador', 30, 14, 14, 28, 120000.00, 110500.00, 'completed', 'https://drive.google.com/drive/folders/1b-DICT-SEC301-CertsAndDocs', 1, 1),
('tr-102', 'Tech-Farming & Rural Impact Sourcing Digital Commerce', 'DICT-RIST-11', 'E-Commerce Strategy for Agriculture', 'Agusan del Sur', 'Prosperidad', 'Poblacion', 'Agusan del Sur Provincial Library Computer Wing', 8.59940000, 125.91890000, '2026-07-10', '2026-07-24', 'Jayson Del Prado', 'Atty. Fernando Corpuz', 45, 20, 25, 45, 125000.00, 121000.00, 'ongoing', 'https://drive.google.com/drive/folders/1c-DICT-RIST11-AgusanSur', 1, 0),
('tr-103', 'Cybersecurity Incident Management & Disaster Recovery Operations', 'DICT-SEC-402', 'Disaster Recovery protocols', 'Surigao del Norte', 'Surigao City', 'San Juan', 'Surigao State University Audio Visual Auditorium', 9.78910000, 125.49580000, '2026-08-15', '2026-08-19', 'Christian Paul Ruiz', 'Dr. Catherine Perez', 25, 0, 0, 0, 90000.00, 0.00, 'upcoming', '', 0, 0),
('tr-104', 'GovNet Technical Fiber Splicing & Optical Core Termination', 'DICT-NET-202', 'Fiber Infrastructure Management', 'Surigao del Sur', 'Tandag', 'Bag-ong Lungsod', 'Provincial Capitol Training Annex B', 9.07680000, 126.19560000, '2026-05-12', '2026-05-14', 'Vince Joshua Mendoza', 'Engr. Mark Joshua', 20, 12, 8, 20, 55000.00, 54200.00, 'completed', 'https://drive.google.com/drive/folders/1d-GovNetFiberSurigaoSur', 1, 1)
ON DUPLICATE KEY UPDATE `id` = `id`;
