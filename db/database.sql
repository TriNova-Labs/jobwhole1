-- Create the Database
CREATE DATABASE IF NOT EXISTS hiraya_db;
USE hiraya_db;

-- 1. USERS TABLE 
-- Matches image_329b3c: Includes extension, middle_initial, and profile_image
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(150) UNIQUE DEFAULT NULL,
    `phone_number` VARCHAR(20) DEFAULT NULL,
    `password` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `first_name` VARCHAR(50) NOT NULL,
    `middle_initial` CHAR(1),
    `extension` VARCHAR(10) DEFAULT NULL,
    `birthday` DATE NOT NULL DEFAULT '2000-01-01',
    `account_type` ENUM('user', 'employer', 'admin') DEFAULT 'user',
    `profile_image` VARCHAR(255) DEFAULT 'default_user.png',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. COMPANIES TABLE 
-- Matches image_329b80: Includes is_pwd_friendly, industry, and size
CREATE TABLE IF NOT EXISTS `companies` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `employer_id` INT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `industry` VARCHAR(100),
    `description` TEXT,
    `logo` VARCHAR(255) DEFAULT 'default_logo.png',
    `website` VARCHAR(255),
    `location` VARCHAR(255),
    `size` VARCHAR(50) DEFAULT '51-200',
    `is_verified` TINYINT(1) DEFAULT 0,
    `is_pwd_friendly` TINYINT(1) DEFAULT 0,
    `verified` TINYINT(1) DEFAULT 0, -- Duplicate found in your screenshot
    FOREIGN KEY (`employer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. CATEGORIES TABLE 
-- Matches image_329bb7
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `icon` VARCHAR(50), 
    `color_class` VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. JOBS TABLE 
-- Matches image_329b5e
CREATE TABLE IF NOT EXISTS `jobs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `company_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `work_type` ENUM('Remote', 'On-site', 'Hybrid') DEFAULT 'On-site',
    `job_type` ENUM('full time', 'part time', 'Internship', 'Contract') DEFAULT 'full time',
    `salary_min` INT,
    `salary_max` INT,
    `tags` TEXT, -- Simplified to TEXT/VARCHAR for easier PHP handling (or JSON if preferred)
    `is_active` TINYINT(1) DEFAULT 1,
    `posted_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. APPLICATIONS TABLE 
-- Matches image_329bf5
CREATE TABLE IF NOT EXISTS `applications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `job_id` INT NOT NULL,
    `seeker_id` INT NOT NULL,
    `resume_path` VARCHAR(255),
    `status` ENUM('pending', 'reviewed', 'interview', 'hired', 'rejected') DEFAULT 'pending',
    `applied_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`job_id`) REFERENCES `jobs`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`seeker_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

---
-- INITIAL DATA FOR TESTING
---

-- Add a default Admin/Employer user
INSERT INTO `users` (`username`, `email`, `password`, `last_name`, `first_name`, `birthday`, `account_type`) 
VALUES ('admin', 'admin@hiraya.com', '$2y$10$YourHashedPassword', 'Admin', 'System', '1990-01-01', 'admin');

-- Add a Category
INSERT INTO `categories` (`name`, `icon`, `color_class`) 
VALUES ('Technology', 'fa-code', 'bg-teal-50');

-- Add a Company (linking to user ID 1)
INSERT INTO `companies` (`employer_id`, `name`, `industry`, `description`, `location`, `is_verified`, `is_pwd_friendly`) 
VALUES (1, 'TechBridge Solutions', 'Technology', 'Leading inclusive tech provider', 'Makati City', 1, 1);

-- Add a Job
INSERT INTO `jobs` (`company_id`, `category_id`, `title`, `description`, `location`, `work_type`, `job_type`, `salary_min`, `salary_max`, `tags`) 
VALUES (1, 1, 'Frontend Developer', 'Looking for an inclusive dev.', 'Makati City', 'Hybrid', 'full time', 35000, 55000, 'PWD-Friendly, Fresh Graduate');

-- 1. Create the Employers Table
CREATE TABLE IF NOT EXISTS employers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Create the Companies Table
CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    website VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- This links the company to the employer
    FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

ALTER TABLE jobs 
ADD COLUMN IF NOT EXISTS is_accessible TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS salary_range VARCHAR(100);

ALTER TABLE jobs 
ADD COLUMN IF NOT EXISTS is_accessible TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS salary_min DECIMAL(10,2),
ADD COLUMN IF NOT EXISTS salary_max DECIMAL(10,2),
ADD COLUMN IF NOT EXISTS tags VARCHAR(255),
ADD COLUMN IF NOT EXISTS icon VARCHAR(50) DEFAULT 'fa-briefcase',
ADD COLUMN IF NOT EXISTS work_type VARCHAR(50);

ALTER TABLE jobs 
ADD COLUMN employer_id INT NOT NULL AFTER id;

INSERT IGNORE INTO categories (name) VALUES 
('Technology'), ('Healthcare'), ('Education'), 
('Finance'), ('Retail'), ('Manufacturing'), 
('Hospitality'), ('Government'), ('Non-Profit'), ('Other');

-- 1. Populate categories so category_id is never null
INSERT IGNORE INTO categories (name) VALUES 
('Technology'), ('Healthcare'), ('Education'), 
('Finance'), ('Retail'), ('Manufacturing'), 
('Hospitality'), ('Government'), ('Non-Profit'), ('Other');

-- 2. Modify the tags column to remove strict constraints and allow nulls
ALTER TABLE jobs MODIFY COLUMN tags TEXT NULL;

ALTER TABLE jobs MODIFY COLUMN job_type VARCHAR(50) NOT NULL;

CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    user_id INT NULL,
    applicant_name VARCHAR(255),
    message TEXT,
    resume_path VARCHAR(255),
    status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'seeker' AFTER email;

ALTER TABLE jobs ADD COLUMN admin_feedback TEXT DEFAULT NULL;

ALTER TABLE jobs 
ADD COLUMN requirements TEXT AFTER tags,
ADD COLUMN benefits TEXT AFTER requirements;

ALTER TABLE jobs DROP CONSTRAINT IF EXISTS jobs_tags_check;
-- Or, if it's a JSON validation issue, ensure the column is set to LONGTEXT or JSON
ALTER TABLE jobs MODIFY tags LONGTEXT;

ALTER TABLE jobs ADD COLUMN requirements TEXT AFTER description;

-- Ensure benefits column exists (it's in your screenshot but might be missing in DB)
ALTER TABLE jobs ADD COLUMN IF NOT EXISTS benefits TEXT AFTER requirements;

-- Ensure icon column exists for your listing icons
ALTER TABLE jobs ADD COLUMN IF NOT EXISTS icon VARCHAR(50) AFTER category_id;

-- Drop the strict check constraint on tags to prevent the Integrity Violation
ALTER TABLE jobs DROP CONSTRAINT IF EXISTS jobs_tags_check;

ALTER TABLE jobs 
ADD COLUMN requirements TEXT AFTER description,
ADD COLUMN benefits TEXT AFTER requirements;

-- Remove it if it exists (ignoring errors) to start fresh
ALTER TABLE jobs DROP COLUMN IF EXISTS requirements;
ALTER TABLE jobs DROP COLUMN IF EXISTS benefits;

-- Re-add them properly after the description column
ALTER TABLE jobs 
ADD COLUMN requirements TEXT AFTER description,
ADD COLUMN benefits TEXT AFTER requirements;

ALTER TABLE jobs ADD COLUMN salary_currency VARCHAR(10) DEFAULT 'PHP' AFTER salary_max;

DESCRIBE jobs;

ALTER TABLE applications ADD COLUMN message TEXT AFTER seeker_id;

ALTER TABLE users 
ADD COLUMN first_name VARCHAR(100) AFTER email,
ADD COLUMN last_name VARCHAR(100) AFTER first_name,
ADD COLUMN middle_initial VARCHAR(5) AFTER last_name,
ADD COLUMN extension VARCHAR(10) AFTER middle_initial,
ADD COLUMN resume_path VARCHAR(255) AFTER extension,
ADD COLUMN cover_letter_path VARCHAR(255) AFTER resume_path,
ADD COLUMN nbi_clearance_path VARCHAR(255) AFTER cover_letter_path,
ADD COLUMN police_clearance_path VARCHAR(255) AFTER nbi_clearance_path,
ADD COLUMN id_path VARCHAR(255) AFTER police_clearance_path,
ADD COLUMN employment_status ENUM('active', 'searching', 'hired') DEFAULT 'searching' AFTER id_path;

-- Add only the columns that are missing for the professional profile
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS first_name VARCHAR(100) AFTER email,
ADD COLUMN IF NOT EXISTS last_name VARCHAR(100) AFTER first_name,
ADD COLUMN IF NOT EXISTS middle_initial VARCHAR(5) AFTER last_name,
ADD COLUMN IF NOT EXISTS extension VARCHAR(10) AFTER middle_initial,
ADD COLUMN IF NOT EXISTS resume_path VARCHAR(255) AFTER extension,
ADD COLUMN IF NOT EXISTS cover_letter_path VARCHAR(255) AFTER resume_path,
ADD COLUMN IF NOT EXISTS nbi_clearance_path VARCHAR(255) AFTER cover_letter_path,
ADD COLUMN IF NOT EXISTS police_clearance_path VARCHAR(255) AFTER nbi_clearance_path,
ADD COLUMN IF NOT EXISTS id_path VARCHAR(255) AFTER police_clearance_path,
ADD COLUMN IF NOT EXISTS employment_status ENUM('active', 'searching', 'hired') DEFAULT 'searching' AFTER id_path;

ALTER TABLE users 
ADD COLUMN middle_initial VARCHAR(5) AFTER last_name,
ADD COLUMN extension VARCHAR(10) AFTER middle_initial,
ADD COLUMN resume_path VARCHAR(255) AFTER extension,
ADD COLUMN cover_letter_path VARCHAR(255) AFTER resume_path,
ADD COLUMN nbi_clearance_path VARCHAR(255) AFTER cover_letter_path,
ADD COLUMN police_clearance_path VARCHAR(255) AFTER nbi_clearance_path,
ADD COLUMN id_path VARCHAR(255) AFTER police_clearance_path,
ADD COLUMN employment_status ENUM('active', 'searching', 'hired') DEFAULT 'searching' AFTER id_path;

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS extension VARCHAR(10) AFTER middle_initial,
ADD COLUMN IF NOT EXISTS resume_path VARCHAR(255) AFTER extension,
ADD COLUMN IF NOT EXISTS cover_letter_path VARCHAR(255) AFTER resume_path,
ADD COLUMN IF NOT EXISTS nbi_clearance_path VARCHAR(255) AFTER cover_letter_path,
ADD COLUMN IF NOT EXISTS police_clearance_path VARCHAR(255) AFTER nbi_clearance_path,
ADD COLUMN IF NOT EXISTS id_path VARCHAR(255) AFTER police_clearance_path,
ADD COLUMN IF NOT EXISTS employment_status ENUM('active', 'searching', 'hired') DEFAULT 'searching' AFTER id_path;

ALTER TABLE applications 
ADD COLUMN message TEXT AFTER resume_path;

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS extension VARCHAR(10) AFTER middle_initial,
ADD COLUMN IF NOT EXISTS resume_path VARCHAR(255) AFTER extension,
ADD COLUMN IF NOT EXISTS cover_letter_path VARCHAR(255) AFTER resume_path,
ADD COLUMN IF NOT EXISTS nbi_clearance_path VARCHAR(255) AFTER cover_letter_path,
ADD COLUMN IF NOT EXISTS police_clearance_path VARCHAR(255) AFTER nbi_clearance_path,
ADD COLUMN IF NOT EXISTS id_path VARCHAR(255) AFTER police_clearance_path,
ADD COLUMN IF NOT EXISTS employment_status ENUM('active', 'searching', 'hired') DEFAULT 'searching' AFTER id_path;

/* Run this to add the missing columns without errors */
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS extension VARCHAR(10) AFTER middle_initial,
ADD COLUMN IF NOT EXISTS resume_path VARCHAR(255) AFTER extension,
ADD COLUMN IF NOT EXISTS employment_status ENUM('active', 'searching', 'hired') DEFAULT 'searching';

/* Also ensure the applications table has the message column */
ALTER TABLE applications 
ADD COLUMN IF NOT EXISTS message TEXT AFTER resume_path;

ALTER TABLE applications 
ADD COLUMN message TEXT AFTER resume_path;

ALTER TABLE applications ADD COLUMN IF NOT EXISTS message TEXT AFTER resume_path;

ALTER TABLE applications MODIFY COLUMN message TEXT AFTER resume_path;

ALTER TABLE applications DROP COLUMN IF EXISTS message;
ALTER TABLE applications ADD COLUMN message TEXT AFTER resume_path;

ALTER TABLE applications DROP COLUMN IF EXISTS message;
ALTER TABLE applications ADD COLUMN message TEXT AFTER resume_path;