-- Homa Bay County Citizen Portal - Database Schema

CREATE DATABASE IF NOT EXISTS citizen_portal;
USE citizen_portal;

-- Departments
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    contact_email VARCHAR(150),
    contact_phone VARCHAR(20)
);

-- Announcements / Notices
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    body TEXT NOT NULL,
    department_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Citizen feedback / complaints
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150),
    email VARCHAR(150),
    department_id INT,
    message TEXT NOT NULL,
    status ENUM('new','in_review','resolved') DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Admin/staff users
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('super_admin','department_staff') DEFAULT 'department_staff',
    department_id INT,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Sample seed data
INSERT INTO departments (name, description, contact_email, contact_phone) VALUES
('Blue Economy, Mining, Fisheries and Digital Economy', 'Oversees fisheries development, mining regulation, and digital transformation initiatives for the county.', 'blueeconomy@homabay.go.ke', '0700000001'),
('Health Services', 'Manages public health facilities, programs, and services across the county.', 'health@homabay.go.ke', '0700000002'),
('Finance and Economic Planning', 'Handles county budgeting, revenue collection, and economic planning.', 'finance@homabay.go.ke', '0700000003');

INSERT INTO announcements (title, body, department_id) VALUES
('Digital Services Portal Launch', 'The county is piloting a new citizen portal to improve access to information and services. This page is part of that pilot.', 1),
('Fisheries Licensing Update', 'New guidelines for fisheries licensing are now in effect. Visit the Blue Economy office for details.', 1);

-- No admin account is seeded here on purpose (a hard-coded password hash in
-- source control is a bad habit to start). Run admin/setup.php once after
-- deploying to create your first admin account securely, then delete that file.
