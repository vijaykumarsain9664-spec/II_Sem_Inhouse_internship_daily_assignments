-- =========================================================
-- Student Registration Portal - Database Schema
-- Import this in phpMyAdmin: SQL tab -> paste -> Go
-- =========================================================
 
CREATE DATABASE IF NOT EXISTS student_portal;
USE student_portal;
 
CREATE TABLE IF NOT EXISTS students (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(100)  NOT NULL,
    email           VARCHAR(100)  NOT NULL UNIQUE,
    phone           VARCHAR(20)   NOT NULL,
    course          VARCHAR(100)  NOT NULL,
    registered_at   DATETIME      DEFAULT CURRENT_TIMESTAMP
);
 
-- Sample records so the table isn't empty when you demo it
INSERT INTO students (full_name, email, phone, course) VALUES
('Aarav Sharma', 'aarav.sharma@example.com', '9876543210', 'B.Tech Computer Science'),
('Priya Verma', 'priya.verma@example.com', '9876500011', 'BBA'),
('Rohan Gupta', 'rohan.gupta@example.com', '9876511122', 'B.Sc Physics');
 
