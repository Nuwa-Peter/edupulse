-- EduPulse School Management System
-- Database Schema: edupulsedb
-- Author: Jules, AI Software Engineer
-- Version: 1.0
--
-- This script creates the database, tables, and populates them with sample data.

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `edupulsedb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `edupulsedb`;

--
-- Table structure for table `schools`
--
CREATE TABLE `schools` (
  `school_id` int(11) NOT NULL AUTO_INCREMENT,
  `edupulse_id` varchar(20) NOT NULL UNIQUE,
  `name` varchar(255) NOT NULL,
  `level` enum('Primary','Secondary') NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `motto` varchar(255) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `established_year` year(4) DEFAULT NULL,
  `accreditation` varchar(255) DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `edupulse_id` varchar(20) NOT NULL UNIQUE,
  `school_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL UNIQUE,
  `password_hash` varchar(255) DEFAULT NULL, -- Nullable for OTP-based logins
  `phone` varchar(50) DEFAULT NULL,
  `role` enum('Superadmin','Headteacher','Deputy Headteacher','DOS','Bursar','Teacher','Student','Parent') NOT NULL,
  `profile_photo_url` varchar(255) DEFAULT 'assets/images/placeholder.png',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  FOREIGN KEY (`school_id`) REFERENCES `schools`(`school_id`) ON DELETE CASCADE,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `licenses`
--
CREATE TABLE `licenses` (
  `license_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `license_key` varchar(255) NOT NULL UNIQUE,
  `expiry_date` date NOT NULL,
  `status` enum('active','expired','revoked') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  FOREIGN KEY (`school_id`) REFERENCES `schools`(`school_id`) ON DELETE CASCADE,
  PRIMARY KEY (`license_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `classes`
--
CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `class_name` varchar(50) NOT NULL,
  `class_teacher_id` int(11) DEFAULT NULL, -- User ID of the teacher
  PRIMARY KEY (`class_id`),
  FOREIGN KEY (`school_id`) REFERENCES `schools`(`school_id`) ON DELETE CASCADE,
  FOREIGN KEY (`class_teacher_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `students`
--
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`class_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `student_parent_relations`
--
CREATE TABLE `student_parent_relations` (
  `relation_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_user_id` int(11) NOT NULL,
  `parent_user_id` int(11) NOT NULL,
  PRIMARY KEY (`relation_id`),
  FOREIGN KEY (`student_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`parent_user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `subjects`
--
CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(100) NOT NULL UNIQUE,
  PRIMARY KEY (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `class_subjects`
--
CREATE TABLE `class_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`class_id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`subject_id`) ON DELETE CASCADE,
  FOREIGN KEY (`teacher_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `marks`
--
CREATE TABLE `marks` (
  `mark_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `term` enum('1','2','3') NOT NULL,
  `academic_year` year(4) NOT NULL,
  `assessment_type` varchar(50) NOT NULL, -- e.g., 'Mid-Term', 'End-of-Term', 'Continuous Assessment'
  `score` decimal(5,2) NOT NULL,
  `recorded_by_id` int(11) NOT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`mark_id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`subject_id`) ON DELETE CASCADE,
  FOREIGN KEY (`recorded_by_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `grading_scales`
--
CREATE TABLE `grading_scales` (
  `grade_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_level` enum('Primary','Secondary') NOT NULL,
  `grade_name` varchar(20) NOT NULL,
  `min_score` int(11) NOT NULL,
  `max_score` int(11) NOT NULL,
  `comment` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`grade_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `fees`
--
CREATE TABLE `fees` (
  `fee_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `term` enum('1','2','3') NOT NULL,
  `academic_year` year(4) NOT NULL,
  `total_due` decimal(10,2) NOT NULL,
  `total_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(10,2) GENERATED ALWAYS AS (`total_due` - `total_paid`) STORED,
  `status` enum('Paid','Partially Paid','Unpaid') NOT NULL DEFAULT 'Unpaid',
  PRIMARY KEY (`fee_id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `fee_payments`
--
CREATE TABLE `fee_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `fee_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('Cash','Bank Deposit','Mobile Money') NOT NULL,
  `received_by_id` int(11) NOT NULL, -- Bursar's user_id
  PRIMARY KEY (`payment_id`),
  FOREIGN KEY (`fee_id`) REFERENCES `fees`(`fee_id`) ON DELETE CASCADE,
  FOREIGN KEY (`received_by_id`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `attendance`
--
CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('Present','Absent','Late','Excused') NOT NULL,
  `marked_by_id` int(11) NOT NULL, -- Teacher's user_id
  PRIMARY KEY (`attendance_id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE,
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`class_id`) ON DELETE CASCADE,
  FOREIGN KEY (`marked_by_id`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `timetable`
--
CREATE TABLE `timetable` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY (`event_id`),
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`class_id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`subject_id`) ON DELETE CASCADE,
  FOREIGN KEY (`teacher_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `announcements`
--
CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `target_role` varchar(50) DEFAULT 'All', -- e.g., 'All', 'Teachers', 'Students', 'Parents'
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`announcement_id`),
  FOREIGN KEY (`school_id`) REFERENCES `schools`(`school_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `chat_messages`
--
CREATE TABLE `chat_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message_content` blob NOT NULL, -- For AES-256 encrypted content
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`message_id`),
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`receiver_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `stories`
--
CREATE TABLE `stories` (
  `story_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `file_url` varchar(255) NOT NULL,
  `caption` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL,
  PRIMARY KEY (`story_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `elearning_materials`
--
CREATE TABLE `elearning_materials` (
  `material_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(20) NOT NULL, -- 'PDF', 'MP4', 'DOCX'
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`material_id`),
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`subject_id`),
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`class_id`),
  FOREIGN KEY (`teacher_id`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `aoi` (Activities of Integration)
--
CREATE TABLE `aoi` (
  `aoi_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_in_charge_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`aoi_id`),
  FOREIGN KEY (`school_id`) REFERENCES `schools`(`school_id`) ON DELETE CASCADE,
  FOREIGN KEY (`teacher_in_charge_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `aoi_participants`
--
CREATE TABLE `aoi_participants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aoi_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`aoi_id`) REFERENCES `aoi`(`aoi_id`) ON DELETE CASCADE,
  FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `surveys`
--
CREATE TABLE `surveys` (
  `survey_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `created_by_id` int(11) NOT NULL, -- Headteacher's user_id
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`survey_id`),
  FOREIGN KEY (`school_id`) REFERENCES `schools`(`school_id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `survey_responses`
--
CREATE TABLE `survey_responses` (
  `response_id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL, -- Parent's user_id
  `response_data` json NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`response_id`),
  FOREIGN KEY (`survey_id`) REFERENCES `surveys`(`survey_id`) ON DELETE CASCADE,
  FOREIGN KEY (`parent_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `audit_logs`
--
CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Table structure for table `ai_predictions`
--
CREATE TABLE `ai_predictions` (
  `prediction_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `prediction_type` enum('at-risk','top-performing') NOT NULL,
  `details` text NOT NULL,
  `confidence_score` decimal(5,4) NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`prediction_id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- =================================================================
-- SAMPLE DATA INSERTION
-- =================================================================
--

--
-- Sample Grading Scales
--
INSERT INTO `grading_scales` (`school_level`, `grade_name`, `min_score`, `max_score`, `comment`) VALUES
('Primary', 'Excellent', 80, 100, 'Excellent performance'),
('Primary', 'Good', 60, 79, 'Good effort'),
('Primary', 'Fair', 40, 59, 'Fair result, can improve'),
('Primary', 'Needs Improvement', 0, 39, 'Needs significant improvement'),
('Secondary', 'A', 80, 100, 'Distinction 1'),
('Secondary', 'B', 70, 79, 'Distinction 2'),
('Secondary', 'C', 60, 69, 'Credit'),
('Secondary', 'D', 50, 59, 'Pass'),
('Secondary', 'E', 40, 49, 'Pass'),
('Secondary', 'F', 0, 39, 'Fail');

--
-- Sample Subjects
--
INSERT INTO `subjects` (`subject_name`) VALUES
('Mathematics'), ('English'), ('Science'), ('Social Studies'), ('Luganda'), ('Religious Education'), ('Physics'), ('Chemistry'), ('Biology'), ('History'), ('Geography'), ('Art');

--
-- Sample Schools
--
INSERT INTO `schools` (`edupulse_id`, `name`, `level`, `address`, `latitude`, `longitude`, `motto`, `contact_email`, `contact_phone`, `established_year`) VALUES
('SJSS1234', 'St. John’s SS, Kampala', 'Secondary', 'Plot 123, Kampala Road, Kampala', 0.34760000, 32.58250000, 'Light and Leadership', 'headteacher@stjohns.ug', '+256772123456', '1985'),
('KAMP1234', 'Kampala PS', 'Primary', 'Plot 456, Makerere Hill, Kampala', 0.29500000, 32.60150000, 'Foundation for the Future', 'headteacher@kampalaps.ug', '+256752987654', '1992');

--
-- Sample Users (Passwords are 'password' hashed)
--
-- Superadmin
INSERT INTO `users` (`edupulse_id`, `first_name`, `last_name`, `email`, `password_hash`, `role`) VALUES
('SUPER001', 'Admin', 'User', 'superadmin@edupulse.com', '$2y$10$Q.MslG52Gcm3n1mE8JgY..JzT5s2jK8c.Cq7Z/I9S.0s8g3c2V9mG', 'Superadmin');

-- St. John's SS Staff & Students
INSERT INTO `users` (`edupulse_id`, `school_id`, `first_name`, `last_name`, `email`, `password_hash`, `role`) VALUES
('HT-SJSS', 1, 'John', 'Muwanga', 'headteacher.sjss@edupulse.com', '$2y$10$Q.MslG52Gcm3n1mE8JgY..JzT5s2jK8c.Cq7Z/I9S.0s8g3c2V9mG', 'Headteacher'),
('T-SJSS-01', 1, 'Alice', 'Nankya', 'teacher.alice@edupulse.com', '$2y$10$Q.MslG52Gcm3n1mE8JgY..JzT5s2jK8c.Cq7Z/I9S.0s8g3c2V9mG', 'Teacher'),
('BUR-SJSS', 1, 'Peter', 'Okello', 'bursar.sjss@edupulse.com', '$2y$10$Q.MslG52Gcm3n1mE8JgY..JzT5s2jK8c.Cq7Z/I9S.0s8g3c2V9mG', 'Bursar'),
('ST-SJSS-01', 1, 'Brian', 'Sempala', 'student.brian@edupulse.com', NULL, 'Student');

-- Kampala PS Staff & Students
INSERT INTO `users` (`edupulse_id`, `school_id`, `first_name`, `last_name`, `email`, `password_hash`, `role`) VALUES
('HT-KPS', 2, 'Mary', 'Achen', 'headteacher.kps@edupulse.com', '$2y$10$Q.MslG52Gcm3n1mE8JgY..JzT5s2jK8c.Cq7Z/I9S.0s8g3c2V9mG', 'Headteacher'),
('T-KPS-01', 2, 'David', 'Byamukama', 'teacher.david@edupulse.com', '$2y$10$Q.MslG52Gcm3n1mE8JgY..JzT5s2jK8c.Cq7Z/I9S.0s8g3c2V9mG', 'Teacher'),
('ST-KPS-01', 2, 'Grace', 'Nakato', 'student.grace@edupulse.com', NULL, 'Student'),
('PA-KPS-01', 2, 'Sarah', 'Nakato', 'parent.sarah@edupulse.com', NULL, 'Parent');


--
-- Link Students to Student Table
--
INSERT INTO `students` (`user_id`, `admission_date`) VALUES
((SELECT user_id FROM users WHERE edupulse_id = 'ST-SJSS-01'), '2023-02-01'),
((SELECT user_id FROM users WHERE edupulse_id = 'ST-KPS-01'), '2023-02-01');

--
-- Link Parent to Student
--
INSERT INTO `student_parent_relations` (`student_user_id`, `parent_user_id`) VALUES
((SELECT user_id FROM users WHERE edupulse_id = 'ST-KPS-01'), (SELECT user_id FROM users WHERE edupulse_id = 'PA-KPS-01'));

--
-- Sample Classes and linking students
--
INSERT INTO `classes` (`school_id`, `class_name`, `class_teacher_id`) VALUES
(1, 'S.1', (SELECT user_id FROM users WHERE edupulse_id = 'T-SJSS-01')),
(2, 'P.4', (SELECT user_id FROM users WHERE edupulse_id = 'T-KPS-01'));

UPDATE `students` SET `class_id` = 1 WHERE `user_id` = (SELECT user_id FROM users WHERE edupulse_id = 'ST-SJSS-01');
UPDATE `students` SET `class_id` = 2 WHERE `user_id` = (SELECT user_id FROM users WHERE edupulse_id = 'ST-KPS-01');

--
-- Sample Marks
--
INSERT INTO `marks` (`student_id`, `subject_id`, `term`, `academic_year`, `assessment_type`, `score`, `recorded_by_id`) VALUES
((SELECT student_id FROM students WHERE user_id = 5), 1, '1', '2025', 'Mid-Term', 85.00, (SELECT user_id FROM users WHERE edupulse_id = 'T-SJSS-01')),
((SELECT student_id FROM students WHERE user_id = 8), 2, '1', '2025', 'Mid-Term', 72.00, (SELECT user_id FROM users WHERE edupulse_id = 'T-KPS-01'));

--
-- Sample Fees
--
INSERT INTO `fees` (`student_id`, `term`, `academic_year`, `total_due`, `total_paid`, `status`) VALUES
((SELECT student_id FROM students WHERE user_id = 5), '1', '2025', 1200000.00, 1000000.00, 'Partially Paid'),
((SELECT student_id FROM students WHERE user_id = 8), '1', '2025', 800000.00, 800000.00, 'Paid');

--
-- Sample Story (Expires in 24 hours from creation)
--
INSERT INTO `stories` (`user_id`, `file_url`, `caption`, `expires_at`) VALUES
((SELECT user_id FROM users WHERE edupulse_id = 'HT-SJSS'), '/Uploads/stories/story_example.jpg', 'Celebrating our sports day winners!', NOW() + INTERVAL 1 DAY);

--
-- Sample AI Prediction
--
INSERT INTO `ai_predictions` (`student_id`, `prediction_type`, `details`, `confidence_score`) VALUES
((SELECT student_id FROM students WHERE user_id = 5), 'top-performing', 'Based on consistent high scores in Mathematics and excellent attendance.', 0.95);

--
-- Sample AOI
--
INSERT INTO `aoi` (`school_id`, `name`, `description`, `teacher_in_charge_id`) VALUES
(1, 'Math Club Meetup', 'Weekly math club for S.1 and S.2', (SELECT user_id FROM users WHERE edupulse_id = 'T-SJSS-01'));
INSERT INTO `aoi_participants` (`aoi_id`, `student_id`) VALUES (1, (SELECT student_id FROM students WHERE user_id = 5));

--
-- Sample Survey
--
INSERT INTO `surveys` (`school_id`, `created_by_id`, `title`, `description`, `start_date`, `end_date`) VALUES
(2, (SELECT user_id FROM users WHERE edupulse_id = 'HT-KPS'), 'Parent Feedback Survey 2025 Term 1', 'Please provide your feedback on our school services.', NOW(), NOW() + INTERVAL 14 DAY);

--
-- Sample License Entry
--
INSERT INTO `licenses` (`school_id`, `license_key`, `expiry_date`) VALUES
(1, 'SJSS1234-2025-ABCD-EFGH', '2025-12-31'),
(2, 'KAMP1234-2025-IJKL-MNOP', '2025-12-31');

--
-- Sample Audit Log
--
INSERT INTO `audit_logs` (`user_id`, `action`, `details`) VALUES
(1, 'CREATE_SCHOOL', 'Superadmin created school: St. John’s SS, Kampala');

COMMIT;
