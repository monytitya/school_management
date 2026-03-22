-- Student registry (flat table — matches phpMyAdmin columns)
-- Run this once in MySQL/phpMyAdmin if the table does not exist yet.

USE school_management;

CREATE TABLE IF NOT EXISTS student_registry (
    student_id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_code       VARCHAR(50)  NOT NULL,
    student_full_name  VARCHAR(200) NOT NULL,
    gender             ENUM('male','female','other') NULL,
    dob                DATE         NULL,
    email              VARCHAR(191) NULL,
    phone              VARCHAR(30)  NULL,
    school_id          INT UNSIGNED NULL,
    stage_id           INT UNSIGNED NULL,
    section_id         INT UNSIGNED NULL,
    created_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_student_registry_code (student_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
