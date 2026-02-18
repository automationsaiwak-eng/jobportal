CREATE DATABASE IF NOT EXISTS job_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE job_portal;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employer', 'job_seeker') NOT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_users_role (role)
);

CREATE TABLE job_seekers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    phone VARCHAR(40) NULL,
    address VARCHAR(255) NULL,
    skills TEXT NULL,
    experience VARCHAR(120) NULL,
    resume VARCHAR(255) NULL,
    profile_image VARCHAR(255) NULL,
    CONSTRAINT fk_job_seekers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE employers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    company_name VARCHAR(180) NOT NULL,
    company_logo VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    description TEXT NULL,
    CONSTRAINT fk_employers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE
);

CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(120) NOT NULL,
    salary INT NOT NULL,
    category_id INT NULL,
    job_type ENUM('Full-time', 'Part-time', 'Remote') NOT NULL,
    deadline DATE NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL,
    CONSTRAINT fk_jobs_employer FOREIGN KEY (employer_id) REFERENCES employers(id) ON DELETE CASCADE,
    CONSTRAINT fk_jobs_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_jobs_status_created (status, created_at),
    INDEX idx_jobs_location_type (location, job_type)
);

CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    job_seeker_id INT NOT NULL,
    cover_letter TEXT NOT NULL,
    status ENUM('pending', 'shortlisted', 'rejected') NOT NULL DEFAULT 'pending',
    applied_at DATETIME NOT NULL,
    CONSTRAINT fk_applications_job FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    CONSTRAINT fk_applications_seeker FOREIGN KEY (job_seeker_id) REFERENCES job_seekers(id) ON DELETE CASCADE,
    UNIQUE KEY uq_application (job_id, job_seeker_id),
    INDEX idx_applications_status (status)
);

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME NOT NULL
);

INSERT INTO categories (name) VALUES
('Software Development'), ('Marketing'), ('Design'), ('Sales'), ('Finance');
