<?php

require_once __DIR__ . '/../config/database.php';

class GradeModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    private function calcLetterGrade(float $score): string {
        return match(true) {
            $score >= 90 => 'A',
            $score >= 80 => 'B',
            $score >= 70 => 'C',
            $score >= 60 => 'D',
            default      => 'F',
        };
    }

    public function getByStudent(int $studentId, string $term = ''): array {
        $sql = "SELECT g.*, sub.name AS subject_name, sub.code AS subject_code,
                       u.name AS recorded_by
                FROM grades g
                JOIN subjects sub ON g.subject_id = sub.id
                LEFT JOIN users u ON g.created_by = u.id
                WHERE g.student_id = ?";
        $params = [$studentId];

        if ($term) {
            $sql .= " AND g.term = ?";
            $params[] = $term;
        }

        $sql .= " ORDER BY sub.name, g.exam_type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getBySubject(int $subjectId, string $term = ''): array {
        $sql = "SELECT g.*, u.name AS student_name, s.student_id AS student_code
                FROM grades g
                JOIN students s ON g.student_id = s.id
                JOIN users u ON s.user_id = u.id
                WHERE g.subject_id = ?";
        $params = [$subjectId];

        if ($term) {
            $sql .= " AND g.term = ?";
            $params[] = $term;
        }

        $sql .= " ORDER BY u.name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getReportCard(int $studentId, string $term): array {
        $stmt = $this->db->prepare(
            "SELECT sub.name AS subject, sub.code,
                    AVG(g.score) AS average,
                    MAX(CASE WHEN g.exam_type='midterm' THEN g.score END) AS midterm,
                    MAX(CASE WHEN g.exam_type='final'   THEN g.score END) AS final_exam,
                    MAX(CASE WHEN g.exam_type='quiz'    THEN g.score END) AS quiz
             FROM grades g
             JOIN subjects sub ON g.subject_id = sub.id
             WHERE g.student_id = ? AND g.term = ?
             GROUP BY sub.id ORDER BY sub.name"
        );
        $stmt->execute([$studentId, $term]);
        $rows = $stmt->fetchAll();

        // Add letter grade to each row
        return array_map(function($r) {
            $r['letter_grade'] = $this->calcLetterGrade((float)$r['average']);
            return $r;
        }, $rows);
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
        $stmt = $this->db->prepare(
            "UPDATE grades SET score=?, grade=?, term=?, exam_type=? WHERE id=?"
        );
        $stmt->execute([
            $data['score'],
            $grade,
            $data['term'],
            $data['exam_type'] ?? 'final',
            $id,
        ]);
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM grades WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM grades WHERE id = ?")->execute([$id]);
    }

    public function getClassAverage(int $subjectId, string $term): ?float {
        $stmt = $this->db->prepare(
            "SELECT AVG(score) FROM grades WHERE subject_id = ? AND term = ?"
        );
        $stmt->execute([$subjectId, $term]);
        $avg = $stmt->fetchColumn();
        return $avg !== false ? round((float)$avg, 2) : null;
    }
}
