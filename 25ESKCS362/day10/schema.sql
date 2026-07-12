-- ============================================================
-- Student Management Portal - Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS student_portal
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE student_portal;

CREATE TABLE IF NOT EXISTS students (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    email       VARCHAR(150)  NOT NULL UNIQUE,
    branch      VARCHAR(100)  NOT NULL,
    cgpa        DECIMAL(3,2)  NOT NULL DEFAULT 0.00,
    status      ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    photo       VARCHAR(255)  DEFAULT NULL,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Helpful indexes for search / filter performance
CREATE INDEX idx_students_branch ON students (branch);
CREATE INDEX idx_students_status ON students (status);
CREATE INDEX idx_students_cgpa   ON students (cgpa);

-- ============================================================
-- Sample seed data
-- ============================================================
INSERT INTO students (name, email, branch, cgpa, status, photo) VALUES
('Aarav Sharma',   'aarav.sharma@example.com',   'Computer Science', 8.75, 'Active',   NULL),
('Priya Verma',    'priya.verma@example.com',    'Electronics',      9.10, 'Active',   NULL),
('Rohan Gupta',    'rohan.gupta@example.com',    'Mechanical',       7.40, 'Inactive', NULL),
('Sneha Iyer',     'sneha.iyer@example.com',     'Computer Science', 8.20, 'Active',   NULL),
('Karan Mehta',    'karan.mehta@example.com',    'Civil',            6.95, 'Active',   NULL),
('Ananya Singh',   'ananya.singh@example.com',   'Electronics',      9.35, 'Inactive', NULL),
('Vikram Rao',     'vikram.rao@example.com',     'Mechanical',       7.80, 'Active',   NULL),
('Ishita Nair',    'ishita.nair@example.com',    'Civil',            8.05, 'Active',   NULL);
