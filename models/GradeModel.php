<?php
require_once __DIR__ . '/../config/database.php';

class GradeModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    private function calcLetterGrade(float $score): string {
        return match(true) {
            $score >= 90 => 'A', $score >= 80 => 'B', $score >= 70 => 'C', $score >= 60 => 'D', default => 'F',
        };
    }

    public function getBySubject(int $subjectId, string $term = ''): array {
        $sql = "SELECT g.*, s.student_full_name AS student_name, s.student_code AS student_code
                FROM grades g
                JOIN students s ON g.student_id = s.student_id
                WHERE g.subject_id = ?";
        $params = [$subjectId];
        if ($term) { $sql .= " AND g.term = ?"; $params[] = $term; }
        $sql .= " ORDER BY s.student_full_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $grade = $this->calcLetterGrade((float)$data['score']);
        $stmt = $this->db->prepare(
            "INSERT INTO grades (student_id, subject_id, score, grade, term, exam_type, created_by)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['student_id'],
            $data['subject_id'],
            $data['score'],
            $grade,
            $data['term'],
            $data['exam_type']  ?? 'final',
            $data['created_by'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $grade = $this->calcLetterGrade((float)$data['score']);
        $stmt = $this->db->prepare("UPDATE grades SET score=?, grade=?, term=?, exam_type=? WHERE id=?");
        $stmt->execute([$data['score'], $grade, $data['term'], $data['exam_type'] ?? 'final', $id]);
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM grades WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM grades WHERE id = ?")->execute([$id]);
    }
}
