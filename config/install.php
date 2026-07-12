<?php
/**
 * One-time (idempotent) schema + seed-data installer.
 * Requires config/database.php to already be included ($db available).
 * Safe to run on every request: everything is guarded by
 * "CREATE TABLE IF NOT EXISTS" / row-count checks.
 */

// Ensure database tables exist
try {
    // Table 1: office_allocations (PMT provincial targets and budgets)
    $db->exec("CREATE TABLE IF NOT EXISTS `office_allocations` (
        `office_name` VARCHAR(50) NOT NULL PRIMARY KEY,
        `target` INT NOT NULL DEFAULT 0,
        `budget` DECIMAL(15,2) NOT NULL DEFAULT 0.00
    ) ENGINE=InnoDB");

    // Table 2: pmt_downloads (Central Office PMT downloads)
    $db->exec("CREATE TABLE IF NOT EXISTS `pmt_downloads` (
        `id` VARCHAR(50) NOT NULL PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `target_trainings` INT NOT NULL DEFAULT 0,
        `unit_budget` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
        `subaro_code` VARCHAR(100) NOT NULL,
        `uacs_code` VARCHAR(100) NOT NULL,
        `course_type` VARCHAR(100) NOT NULL,
        `duration_hours` VARCHAR(100) NOT NULL,
        `drive_link` TEXT NOT NULL
    ) ENGINE=InnoDB");

    // Table 3: trainings (Core training and seminar records)
    $db->exec("CREATE TABLE IF NOT EXISTS `trainings` (
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
        `course_type` VARCHAR(50) NOT NULL DEFAULT 'Webinar',
        `duration_hours` VARCHAR(20) NOT NULL DEFAULT '3',
        `implementation_mode` VARCHAR(20) NOT NULL DEFAULT 'Face-to-Face',
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
    ) ENGINE=InnoDB");

    // Table 4: app_meta (tracks one-time install flags, e.g. "have we seeded already?")
    $db->exec("CREATE TABLE IF NOT EXISTS `app_meta` (
        `meta_key` VARCHAR(100) NOT NULL PRIMARY KEY,
        `meta_value` VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB");

    // Table 5: pmt_download_assignments (manual +/- province x duration target
    // assignments made in the Provincial Ledger generator, per registered download)
    $db->exec("CREATE TABLE IF NOT EXISTS `pmt_download_assignments` (
        `download_id` VARCHAR(50) NOT NULL,
        `office_name` VARCHAR(100) NOT NULL,
        `duration_bucket` VARCHAR(30) NOT NULL,
        `target_count` INT NOT NULL DEFAULT 0,
        PRIMARY KEY (`download_id`, `office_name`, `duration_bucket`)
    ) ENGINE=InnoDB");

    // Table 6: participants (Participants Penetration tab - CSV bulk import +
    // manual single registrant entries. province/municipality are auto-resolved
    // from training_id against the trainings table at save time.)
    $db->exec("CREATE TABLE IF NOT EXISTS `participants` (
        `id` VARCHAR(50) NOT NULL PRIMARY KEY,
        `participant_name` VARCHAR(255) NOT NULL,
        `project` VARCHAR(255) NULL,
        `program` VARCHAR(255) NULL,
        `training_title` VARCHAR(255) NULL,
        `training_date` DATE NULL,
        `training_id` VARCHAR(50) NULL,
        `cert_id` VARCHAR(100) NULL,
        `certificate_type` VARCHAR(100) NULL,
        `resource_person` VARCHAR(150) NULL,
        `sex` ENUM('Male','Female') NOT NULL DEFAULT 'Male',
        `province` VARCHAR(100) NULL,
        `municipality` VARCHAR(100) NULL,
        INDEX (`training_id`),
        INDEX (`province`)
    ) ENGINE=InnoDB");

    // Table 7: users (login credentials for the portal)
    $db->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `username` VARCHAR(50) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // Auto-upgrade check: add course_type/duration_hours to installs created before this update
    $hasCourseType = $db->query("SHOW COLUMNS FROM `trainings` LIKE 'course_type'")->fetch();
    if (!$hasCourseType) {
        $db->exec("ALTER TABLE `trainings` ADD COLUMN `course_type` VARCHAR(50) NOT NULL DEFAULT 'Webinar' AFTER `resource_person`");
    }
    $hasDurationHours = $db->query("SHOW COLUMNS FROM `trainings` LIKE 'duration_hours'")->fetch();
    if (!$hasDurationHours) {
        $db->exec("ALTER TABLE `trainings` ADD COLUMN `duration_hours` VARCHAR(20) NOT NULL DEFAULT '3' AFTER `course_type`");
    }
    $hasImplementationMode = $db->query("SHOW COLUMNS FROM `trainings` LIKE 'implementation_mode'")->fetch();
    if (!$hasImplementationMode) {
        $db->exec("ALTER TABLE `trainings` ADD COLUMN `implementation_mode` VARCHAR(20) NOT NULL DEFAULT 'Face-to-Face' AFTER `duration_hours`");
    }

    // Seed exactly one default admin account, once ever — independent of the
    // trainings/downloads 'seeded' flag below, so this always runs even on
    // installs that were already fully seeded before login was added.
    $usersSeeded = $db->query("SELECT meta_value FROM `app_meta` WHERE meta_key = 'users_seeded'")->fetchColumn();
    if (!$usersSeeded) {
        $defaultHash = password_hash('ChangeMe123!', PASSWORD_DEFAULT);
        $db->prepare("INSERT INTO `users` (username, password_hash) VALUES (?, ?)")
           ->execute(['admin', $defaultHash]);
        $db->prepare("INSERT INTO `app_meta` (meta_key, meta_value) VALUES ('users_seeded', '1')
                      ON DUPLICATE KEY UPDATE meta_value = '1'")->execute();
    }

    // Has the one-time seed already run? If so, skip ALL seeding below —
    // even if the tables are currently empty because the user deleted rows.
    $alreadySeeded = $db->query("SELECT meta_value FROM `app_meta` WHERE meta_key = 'seeded'")->fetchColumn();

    if (!$alreadySeeded) {
        // Seed allocations (IGNORE = skip quietly if a row already exists)
        $alloc_stmt = $db->prepare("INSERT IGNORE INTO `office_allocations` (office_name, target, budget) VALUES (?, ?, ?)");
        $alloc_stmt->execute(["Regional Office", 5, 500000.00]);
        $alloc_stmt->execute(["Butuan City", 6, 200000.00]);
        $alloc_stmt->execute(["Agusan del Norte", 5, 150000.00]);
        $alloc_stmt->execute(["Agusan del Sur", 7, 250000.00]);
        $alloc_stmt->execute(["Surigao del Norte", 6, 200000.00]);
        $alloc_stmt->execute(["Surigao del Sur", 6, 200000.00]);
        $alloc_stmt->execute(["Dinagat Islands", 5, 150000.00]);

        // Seed central downloads (IGNORE = skip quietly if a row already exists)
        $dl_stmt = $db->prepare("INSERT IGNORE INTO `pmt_downloads` (id, title, target_trainings, unit_budget, subaro_code, uacs_code, course_type, duration_hours, drive_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $dl_stmt->execute([
            "dl-101",
            "FY 2026 Free Wi-Fi for All Tech Operations & Fiber Deployments",
            15,
            80000.00,
            "SUB-ARO-CO-26-4402",
            "5020201000",
            "ICT Training",
            "16h, 20h, 40h",
            "https://drive.google.com/drive/folders/1b-DICT-FreeWifi2026Caraga-example"
        ]);
        $dl_stmt->execute([
            "dl-102",
            "FY 2026 Executive Cyber Security Readiness Assessment",
            10,
            120000.00,
            "SUB-ARO-CO-26-8901",
            "5020201002",
            "ICT Training",
            "Full Catalog (All Speeds)",
            "https://drive.google.com/drive/folders/1c-DICT-CybersecAuditingCaraga-example"
        ]);

        // Seed trainings (IGNORE = skip quietly if a row already exists)
        $t_stmt = $db->prepare("INSERT IGNORE INTO `trainings` (id, training_title, course_code, course_name, province, municipality, barangay, venue, latitude, longitude, start_date, end_date, course_officer, resource_person, course_type, duration_hours, target_participants, male_participants, female_participants, actual_participants, budget_allocated, budget_utilized, status, drive_link, photos, documents) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $t_stmt->execute([
            "tr-101",
            "Information Security Management System Compliance Auditor",
            "DICT-SEC-301",
            "ISMS Compliance auditing",
            "Regional Office",
            "Butuan City",
            "Doongan",
            "DICT Regional Office Conference Hall",
            8.95150000,
            125.53500000,
            "2026-06-01",
            "2026-06-05",
            "Maria Fe S. Lopez",
            "Engr. Ricardo Salvador",
            "ICT Training",
            "40",
            30,
            14,
            14,
            28,
            120000.00,
            110500.00,
            "completed",
            "https://drive.google.com/drive/folders/1b-DICT-SEC301-CertsAndDocs",
            1,
            1
        ]);

        $t_stmt->execute([
            "tr-102",
            "Tech-Farming & Rural Impact Sourcing Digital Commerce",
            "DICT-RIST-11",
            "E-Commerce Strategy for Agriculture",
            "Agusan del Sur",
            "Prosperidad",
            "Poblacion",
            "Agusan del Sur Provincial Library Computer Wing",
            8.59940000,
            125.91890000,
            "2026-07-10",
            "2026-07-24",
            "Jayson Del Prado",
            "Atty. Fernando Corpuz",
            "Webinar",
            "4",
            45,
            20,
            25,
            45,
            125000.00,
            121000.00,
            "ongoing",
            "https://drive.google.com/drive/folders/1c-DICT-RIST11-AgusanSur",
            1,
            0
        ]);

        $t_stmt->execute([
            "tr-103",
            "Cybersecurity Incident Management & Disaster Recovery Operations",
            "DICT-SEC-402",
            "Disaster Recovery protocols",
            "Surigao del Norte",
            "Surigao City",
            "San Juan",
            "Surigao State University Audio Visual Auditorium",
            9.78910000,
            125.49580000,
            "2026-08-15",
            "2026-08-19",
            "Christian Paul Ruiz",
            "Dr. Catherine Perez",
            "ICT Training",
            "20",
            25,
            0,
            0,
            0,
            90000.00,
            0.00,
            "upcoming",
            "",
            0,
            0
        ]);

        $t_stmt->execute([
            "tr-104",
            "GovNet Technical Fiber Splicing & Optical Core Termination",
            "DICT-NET-202",
            "Fiber Infrastructure Management",
            "Surigao del Sur",
            "Tandag",
            "Bag-ong Lungsod",
            "Provincial Capitol Training Annex B",
            9.07680000,
            126.19560000,
            "2026-05-12",
            "2026-05-14",
            "Vince Joshua Mendoza",
            "Engr. Mark Joshua",
            "ICT Training",
            "16",
            20,
            12,
            8,
            20,
            55000.00,
            54200.00,
            "completed",
            "https://drive.google.com/drive/folders/1d-GovNetFiberSurigaoSur",
            1,
            1
        ]);

        // Mark seeding as done so it never runs again, even if these tables
        // are later emptied out through the app's own delete actions.
        $db->prepare("INSERT INTO `app_meta` (meta_key, meta_value) VALUES ('seeded', '1')
                      ON DUPLICATE KEY UPDATE meta_value = '1'")->execute();
    }
} catch (PDOException $e) {
    die("Table Initialization Failure: " . $e->getMessage());
}