CREATE DATABASE IF NOT EXISTS laboratory_booking_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE laboratory_booking_system;

CREATE TABLE users (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL, role ENUM('admin','student') NOT NULL DEFAULT 'student',
 is_active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE students (
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL UNIQUE, student_id VARCHAR(50) NOT NULL UNIQUE,
 phone VARCHAR(30), department VARCHAR(100), course VARCHAR(100), year_of_study VARCHAR(30),
 registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_students_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE laboratories (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, department VARCHAR(100),
 building VARCHAR(100), room_number VARCHAR(30), description TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE equipment_categories (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE equipment (
 id INT AUTO_INCREMENT PRIMARY KEY, equipment_code VARCHAR(60) NOT NULL UNIQUE, name VARCHAR(150) NOT NULL,
 category_id INT NULL, description TEXT, quantity INT NOT NULL DEFAULT 1, available_quantity INT NOT NULL DEFAULT 1,
 laboratory_id INT NOT NULL, condition_status ENUM('Good','Under Maintenance') NOT NULL DEFAULT 'Good',
 image_path VARCHAR(255), date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_equipment_category FOREIGN KEY(category_id) REFERENCES equipment_categories(id) ON DELETE SET NULL,
 CONSTRAINT fk_equipment_lab FOREIGN KEY(laboratory_id) REFERENCES laboratories(id),
 CHECK(quantity >= 0), CHECK(available_quantity >= 0)
) ENGINE=InnoDB;

CREATE TABLE bookings (
 id INT AUTO_INCREMENT PRIMARY KEY, student_user_id INT NOT NULL, equipment_id INT NOT NULL,
 quantity INT NOT NULL, booking_date DATE NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL,
 purpose TEXT NOT NULL, status ENUM('Pending','Approved','Rejected','Cancelled','Completed') NOT NULL DEFAULT 'Pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_booking_slot(equipment_id,booking_date,start_time,end_time,status),
 CONSTRAINT fk_booking_student FOREIGN KEY(student_user_id) REFERENCES users(id),
 CONSTRAINT fk_booking_equipment FOREIGN KEY(equipment_id) REFERENCES equipment(id),
 CHECK(quantity > 0), CHECK(end_time > start_time)
) ENGINE=InnoDB;

CREATE TABLE maintenance_requests (
 id INT AUTO_INCREMENT PRIMARY KEY, equipment_id INT NOT NULL, reported_by INT NOT NULL,
 issue_type VARCHAR(100) NOT NULL, description TEXT NOT NULL,
 status ENUM('Reported','Under Review','In Progress','Repaired','Closed') NOT NULL DEFAULT 'Reported',
 admin_remarks TEXT, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 CONSTRAINT fk_maintenance_equipment FOREIGN KEY(equipment_id) REFERENCES equipment(id),
 CONSTRAINT fk_maintenance_reporter FOREIGN KEY(reported_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE maintenance_records (
 id INT AUTO_INCREMENT PRIMARY KEY, equipment_id INT NOT NULL, maintenance_request_id INT NULL,
 action_taken TEXT NOT NULL, performed_by INT NULL, maintenance_date DATETIME DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_record_equipment FOREIGN KEY(equipment_id) REFERENCES equipment(id),
 CONSTRAINT fk_record_request FOREIGN KEY(maintenance_request_id) REFERENCES maintenance_requests(id) ON DELETE SET NULL,
 CONSTRAINT fk_record_user FOREIGN KEY(performed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE notifications (
 id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, message TEXT NOT NULL,
 is_read TINYINT(1) NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_notification_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO equipment_categories(name) VALUES ('Electronics'),('Optics'),('Computing'),('Mechanical');
INSERT INTO laboratories(name,department,building,room_number,description) VALUES
('Electronics Lab','CSE / ECE','Block A','A-101','Electronics and embedded systems laboratory'),
('Computer Lab 1','CSE','Block B','B-204','General computing laboratory'),
('Physics Lab','Science','Block C','C-012','Physics experiments and optics');

-- Demo admin: admin@labdesk.local / Admin@123
-- Demo student: student@labdesk.local / Student@123
-- Password hashes below are generated for these demo passwords.
INSERT INTO users(name,email,password_hash,role) VALUES
('System Administrator','admin@labdesk.local','$2y$10$F8zL37fAMSg3C0chen4vEeMuirlXIheHpTKtHqRgkKwagek6TIaAm','admin'),
('Demo Student','student@labdesk.local','$2y$10$6j2qhV6jF7vAu8ERqYTjv.SHgJ1jH3s5jxbEEhtNCPyU9nIv3N9wO','student');
INSERT INTO students(user_id,student_id,department,course,year_of_study) VALUES (2,'STU2026001','CSE','B.Tech CSE','3rd Year');
INSERT INTO equipment(equipment_code,name,category_id,description,quantity,available_quantity,laboratory_id) VALUES
('EQ-001','Digital Multimeter',1,'Digital meter for voltage, current and resistance measurements',10,10,1),
('EQ-002','Arduino Uno Kit',3,'Microcontroller development board kit',8,8,2),
('EQ-003','Optical Bench',2,'Optics experiment bench',4,4,3),
('EQ-004','Oscilloscope',1,'Two-channel digital oscilloscope',5,5,1);
