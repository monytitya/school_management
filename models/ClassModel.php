<?php
require_once __DIR__ . '/../config/database.php';

class ClassModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(): array {
        return $this->db->query(
            "SELECT c.*, 
                    c.class_id as id,
                    COALESCE(t.teacher_full_name, '(Unassigned)') AS teacher_name,
                    c.class_name as name,
                    COALESCE(stg.stage_name, c.stage_id) as grade_level,
                    COALESCE(sec.section_name, c.section_id) as section_name,
                    COALESCE(s.title, '(No Subject)') as subject_title,
                    COALESCE(rm.room_name, '(No Room)') as room_name
             FROM classes c
             LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
             LEFT JOIN stages stg ON c.stage_id = stg.stage_id
             LEFT JOIN sections sec ON c.section_id = sec.section_id
             LEFT JOIN subjects s ON c.subject_id = s.subject_id
             LEFT JOIN classrooms rm ON c.classroom_id = rm.classroom_id
             ORDER BY c.stage_id, c.class_name"
        )->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT c.*, 
                    c.class_id as id, 
                    COALESCE(t.teacher_full_name, '(Unassigned)') AS teacher_name, 
                    c.class_name as name,
                    COALESCE(stg.stage_name, c.stage_id) as grade_level,
                    COALESCE(sec.section_name, c.section_id) as section_name,
                    COALESCE(s.title, '(No Subject)') as subject_title,
                    COALESCE(rm.room_name, '(No Room)') as room_name
             FROM classes c
             LEFT JOIN teachers t ON c.teacher_id = t.teacher_id
             LEFT JOIN stages stg ON c.stage_id = stg.stage_id
             LEFT JOIN sections sec ON c.section_id = sec.section_id
             LEFT JOIN subjects s ON c.subject_id = s.subject_id
             LEFT JOIN classrooms rm ON c.classroom_id = rm.classroom_id
             WHERE c.class_id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO classes (class_name, stage_id, section_id, teacher_id, subject_id, classroom_id)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        $stageId = !empty($data['stage_id']) ? (int)$data['stage_id'] : null;
        $sectionId = !empty($data['section_id']) ? (int)$data['section_id'] : null;
        $teacherId = !empty($data['teacher_id']) ? (int)$data['teacher_id'] : null;
        $subjectId = !empty($data['subject_id']) ? (int)$data['subject_id'] : null;
        $classroomId = !empty($data['classroom_id']) ? (int)$data['classroom_id'] : null;

        $stmt->execute([
            $data['class_name']  ?? $data['name'],
            $stageId,
            $sectionId,
            $teacherId,
            $subjectId,
            $classroomId
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            "UPDATE classes SET class_name=?, stage_id=?, section_id=?, teacher_id=?, subject_id=?, classroom_id=? WHERE class_id=?"
        );
        
        $stageId = !empty($data['stage_id']) ? (int)$data['stage_id'] : null;
        $sectionId = !empty($data['section_id']) ? (int)$data['section_id'] : null;
        $teacherId = !empty($data['teacher_id']) ? (int)$data['teacher_id'] : null;
        $subjectId = !empty($data['subject_id']) ? (int)$data['subject_id'] : null;
        $classroomId = !empty($data['classroom_id']) ? (int)$data['classroom_id'] : null;

        $stmt->execute([
            $data['class_name']  ?? $data['name'],
            $stageId,
            $sectionId,
            $teacherId,
            $subjectId,
            $classroomId,
            $id,
        ]);
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM classes WHERE class_id = ?")->execute([$id]);
    }

    public function getStudents(int $classId): array {
        // Find the section_id for this class first
        $c = $this->findById($classId);
        if (!$c || !isset($c['section_id'])) return [];

        $stmt = $this->db->prepare(
            "SELECT s.*, s.student_full_name as student_full_name, s.email FROM students s
             WHERE s.section_id = ? ORDER BY s.student_full_name"
        );
        $stmt->execute([$c['section_id']]);
        return $stmt->fetchAll();
    }
}
