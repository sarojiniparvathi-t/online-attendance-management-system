-- =====================================================
-- Online Student Attendance System
-- Database: attendance_system
-- =====================================================

CREATE DATABASE IF NOT EXISTS attendance_system;
USE attendance_system;

-- -----------------------------------------------------
-- Table: admin
-- -----------------------------------------------------
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: classes
-- -----------------------------------------------------
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: sections
-- -----------------------------------------------------
CREATE TABLE sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(50) NOT NULL,
    class_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: staff
-- -----------------------------------------------------
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    gender ENUM('Male','Female','Other') DEFAULT 'Male',
    address VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: subjects
-- -----------------------------------------------------
CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(20),
    class_id INT NOT NULL,
    staff_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: students
-- -----------------------------------------------------
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    roll_no VARCHAR(30) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    gender ENUM('Male','Female','Other') DEFAULT 'Male',
    dob DATE,
    address VARCHAR(255),
    class_id INT NOT NULL,
    section_id INT NOT NULL,
    parent_name VARCHAR(100),
    parent_phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Table: attendance
-- -----------------------------------------------------
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    staff_id INT NOT NULL,
    class_id INT NOT NULL,
    section_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    status ENUM('Present','Absent') NOT NULL DEFAULT 'Present',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_attendance (student_id, subject_id, attendance_date),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- Sample / Seed Data
-- =====================================================

-- Default Admin login -> email: admin@attendance.com | password: admin123
INSERT INTO admin (full_name, email, password) VALUES
('System Admin', 'admin@attendance.com', '$2y$10$.r8f1n2NnE5brw99Wpyl4uVbacggQUf9nmV3NzUk1mN3PwZ5YnhMe');

-- Classes
INSERT INTO classes (class_name) VALUES ('BCA I Year'), ('BCA II Year'), ('BCA III Year');

-- Sections
INSERT INTO sections (section_name, class_id) VALUES
('Section A', 1), ('Section B', 1),
('Section A', 2),
('Section A', 3);

-- Staff login -> email: staff@attendance.com | password: staff123
INSERT INTO staff (full_name, email, phone, gender, address, password) VALUES
('Ramesh Kumar', 'staff@attendance.com', '9876543210', 'Male', 'Erode, Tamil Nadu', '$2y$10$S3x6czgshkW3GszBKQLJXOJOUrTbP2gCZXiwAcRONTDYz8/qGSyBi');

-- Subjects
INSERT INTO subjects (subject_name, subject_code, class_id, staff_id) VALUES
('Programming in C', 'BCA101', 1, 1),
('Database Management System', 'BCA201', 2, 1),
('Web Technology', 'BCA301', 3, 1);

-- Students login password for all -> student123
INSERT INTO students (roll_no, full_name, email, phone, gender, dob, address, class_id, section_id, parent_name, parent_phone, password) VALUES
('BCA001', 'Arun Prakash', 'student@attendance.com', '9998887771', 'Male', '2006-05-12', 'Salem, Tamil Nadu', 1, 1, 'Prakash M', '9998887770', '$2y$10$yzy5mOsYOz9bi59vQO1wR.4HGp4YN.a/nrZQxrXK1JmDAWVo0wlVW'),
('BCA002', 'Divya Sri', 'divya@attendance.com', '9998887772', 'Female', '2006-08-21', 'Erode, Tamil Nadu', 1, 1, 'Suresh R', '9998887773', '$2y$10$yzy5mOsYOz9bi59vQO1wR.4HGp4YN.a/nrZQxrXK1JmDAWVo0wlVW'),
('BCA003', 'Karthik Raja', 'karthik@attendance.com', '9998887774', 'Male', '2005-11-02', 'Coimbatore, Tamil Nadu', 1, 2, 'Raja S', '9998887775', '$2y$10$yzy5mOsYOz9bi59vQO1wR.4HGp4YN.a/nrZQxrXK1JmDAWVo0wlVW');
