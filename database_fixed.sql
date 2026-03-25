SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS school_management
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE school_management;

CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(150)  NOT NULL,
    email      VARCHAR(191)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    role       ENUM('admin','teacher','student','parent') NOT NULL DEFAULT 'student',
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
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

CREATE TABLE IF NOT EXISTS classes (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    grade_level   VARCHAR(50)  NOT NULL,
    teacher_id    INT UNSIGNED NULL,
    academic_year VARCHAR(10)  NOT NULL DEFAULT '2024-2025',
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS teachers (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NOT NULL UNIQUE,
    employee_id VARCHAR(50)  NOT NULL UNIQUE,
    phone       VARCHAR(20)  NULL,
    address     TEXT         NULL,
    joined_date DATE         NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS parents (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL UNIQUE,
    phone      VARCHAR(20)  NULL,
    address    TEXT         NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS students (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL UNIQUE,
    student_id    VARCHAR(50)  NOT NULL UNIQUE,
    class_id      INT UNSIGNED NULL,
    parent_id     INT UNSIGNED NULL,
    date_of_birth DATE         NULL,
    gender        ENUM('male','female','other') NULL,
    phone         VARCHAR(20)  NULL,
    address       TEXT         NULL,
    enrolled_date DATE         NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)   REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (class_id)  REFERENCES classes(id)  ON DELETE SET NULL,
    FOREIGN KEY (parent_id) REFERENCES parents(id)  ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS subjects (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    code       VARCHAR(20)  NOT NULL UNIQUE,
    class_id   INT UNSIGNED NULL,
    teacher_id INT UNSIGNED NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id)   REFERENCES classes(id)  ON DELETE SET NULL,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS attendance (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    class_id   INT UNSIGNED NOT NULL,
    date       DATE         NOT NULL,
    status     ENUM('present','absent','late','excused') NOT NULL DEFAULT 'present',
    note       TEXT         NULL,
    created_by INT UNSIGNED NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attendance (student_id, class_id, date),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id)   REFERENCES classes(id)  ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)    ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS grades (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED   NOT NULL,
    subject_id INT UNSIGNED   NOT NULL,
    score      DECIMAL(5,2)   NOT NULL,
    grade      VARCHAR(5)     NULL,
    term       VARCHAR(30)    NOT NULL,
    exam_type  ENUM('quiz','midterm','final','assignment') NOT NULL DEFAULT 'final',
    created_by INT UNSIGNED   NULL,
    created_at DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)    ON DELETE SET NULL
);
CREATE TABLE IF NOT EXISTS timetable (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    class_id   INT UNSIGNED NOT NULL,
    subject_id INT UNSIGNED NOT NULL,
    teacher_id INT UNSIGNED NULL,
    day        ENUM('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday') NOT NULL,
    start_time TIME         NOT NULL,
    end_time   TIME         NOT NULL,
    room       VARCHAR(50)  NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id)   REFERENCES classes(id)  ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL
);

ALTER TABLE classes
    ADD CONSTRAINT fk_classes_teacher
    FOREIGN KEY IF NOT EXISTS (teacher_id) REFERENCES teachers(id) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS = 1;
INSERT IGNORE INTO users (name, email, password, role) VALUES
(
    'System Admin',
    'admin@school.com',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
);
