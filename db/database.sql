-- Create the Database
CREATE DATABASE IF NOT EXISTS hiraya_db;
USE hiraya_db;

-- 1. USERS TABLE (Handles Admins, Employers, and Job Seekers)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Unique Identifiers
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(150) UNIQUE DEFAULT NULL,
    phone_number VARCHAR(20) UNIQUE DEFAULT NULL,
    password VARCHAR(255) NOT NULL,

    -- Name Components
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_initial CHAR(1),
    extension VARCHAR(10) DEFAULT NULL, -- e.g., Jr., Sr., III

    -- Birthday and Metadata
    birthday DATE NOT NULL,
    
    -- Status flag (Optional: Use this to manually promote an Admin/Employer)
    account_type ENUM('user', 'employer', 'admin') DEFAULT 'user',
    profile_image VARCHAR(255) DEFAULT 'default_user.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. COMPANIES TABLE (Profiles for employers)
CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    logo VARCHAR(255) DEFAULT 'default_logo.png',
    website VARCHAR(255),
    location VARCHAR(255),
    verified BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 3. CATEGORIES TABLE (For the "Explore Opportunities" section)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50), -- Store emoji or icon class name
    color_class VARCHAR(50) -- Tailwind color e.g., 'bg-blue-500'
);

-- 4. JOBS TABLE (The core job listings)
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    work_type ENUM('Remote', 'On-site', 'Hybrid') DEFAULT 'On-site',
    job_type ENUM('Full-time', 'Part-time', 'Internship', 'Contract') DEFAULT 'Full-time',
    salary_min INT,
    salary_max INT,
    tags JSON, -- Stores tags like ["PWD-Friendly", "Urgent"]
    is_active BOOLEAN DEFAULT TRUE,
    posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- 5. APPLICATIONS TABLE (Links seekers to jobs)
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    seeker_id INT NOT NULL,
    resume_path VARCHAR(255),
    status ENUM('pending', 'reviewed', 'interview', 'hired', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE
);