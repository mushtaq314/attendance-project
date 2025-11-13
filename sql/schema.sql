CREATE DATABASE IF NOT EXISTS attendance_project CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE attendance_project;


CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(150) NOT NULL,
email VARCHAR(150) UNIQUE NOT NULL,
password VARCHAR(255) DEFAULT NULL,
phone VARCHAR(30) DEFAULT NULL,
role ENUM('employee','admin') DEFAULT 'employee',
approved TINYINT(1) DEFAULT 0,
status ENUM('pending','approved','rejected','active') DEFAULT 'pending',
twofa_secret VARCHAR(255) DEFAULT NULL,
face_descriptor MEDIUMTEXT DEFAULT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE face_descriptors (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
descriptor_json MEDIUMTEXT NOT NULL,
image_path VARCHAR(255) NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE attendance (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
action ENUM('login','logout','break_start','break_end','location') NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
latitude DECIMAL(10,7) NULL,
longitude DECIMAL(10,7) NULL,
provider VARCHAR(50) DEFAULT NULL,
accuracy DECIMAL(10,2) DEFAULT NULL,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE posts (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NULL,
title VARCHAR(255),
body TEXT,
visible_to ENUM('all','employees','admins') DEFAULT 'all',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;


CREATE TABLE notifications (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NULL,
message TEXT NOT NULL,
is_read TINYINT(1) DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


CREATE TABLE emp_notifications (
id INT AUTO_INCREMENT PRIMARY KEY,
emp_id INT NOT NULL,
type VARCHAR(50) DEFAULT NULL,
message TEXT NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (emp_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE locations (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL,
latitude DECIMAL(10,7) NOT NULL,
longitude DECIMAL(10,7) NOT NULL,
status ENUM('active','inactive') DEFAULT 'active',
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
