
-- Drop database if exists and create a new one
DROP DATABASE IF EXISTS st_alphonsus;
CREATE DATABASE st_alphonsus;

-- Use the database
USE st_alphonsus;

-- Create Teachers table
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    annual_salary DECIMAL(10, 2) NOT NULL,
    background_check_status ENUM('passed', 'pending', 'failed') NOT NULL,
    class_id INT NULL
);

-- Create Classes table
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    capacity INT NOT NULL,
    teacher_id INT NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
);

-- Add FK constraint to teachers for class_id
ALTER TABLE teachers
ADD CONSTRAINT fk_teacher_class
FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL;

-- Create Parents table
CREATE TABLE parents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    address VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    relationship ENUM('mother', 'father', 'guardian', 'other') NOT NULL
);

-- Create Pupils table
CREATE TABLE pupils (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    address VARCHAR(255) NOT NULL,
    medical_information TEXT,
    class_id INT NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
);

-- Create pupil-parent relationship table (many-to-many)
CREATE TABLE pupil_parents (
    pupil_id INT NOT NULL,
    parent_id INT NOT NULL,
    PRIMARY KEY (pupil_id, parent_id),
    FOREIGN KEY (pupil_id) REFERENCES pupils(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES parents(id) ON DELETE CASCADE
);

-- Insert sample data for teachers
INSERT INTO teachers (first_name, last_name, address, phone_number, email, annual_salary, background_check_status)
VALUES 
('John', 'Smith', '123 School Lane, Cityville', '555-1234', 'john.smith@stalphonsus.edu', 45000.00, 'passed'),
('Mary', 'Johnson', '456 Education Street, Townsville', '555-5678', 'mary.johnson@stalphonsus.edu', 48000.00, 'passed');

-- Insert sample data for classes
INSERT INTO classes (name, capacity)
VALUES 
('Year 1 Alpha', 25),
('Year 2 Beta', 28);

-- Update teachers with class assignments
UPDATE teachers SET class_id = 1 WHERE id = 1;
UPDATE classes SET teacher_id = 1 WHERE id = 1;

UPDATE teachers SET class_id = 2 WHERE id = 2;
UPDATE classes SET teacher_id = 2 WHERE id = 2;

-- Insert sample data for parents
INSERT INTO parents (first_name, last_name, address, phone_number, email, relationship)
VALUES 
('Robert', 'Brown', '789 Family Road, Parentville', '555-9012', 'robert.brown@email.com', 'father'),
('Sarah', 'Brown', '789 Family Road, Parentville', '555-3456', 'sarah.brown@email.com', 'mother');

-- Insert sample data for pupils
INSERT INTO pupils (first_name, last_name, date_of_birth, address, medical_information, class_id)
VALUES 
('James', 'Brown', '2017-05-12', '789 Family Road, Parentville', 'No allergies', 1);

-- Link pupils to parents
INSERT INTO pupil_parents (pupil_id, parent_id)
VALUES 
(1, 1),
(1, 2);
