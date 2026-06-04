<?php
require_once __DIR__ . '/../config/Database.php';

class UsageStat {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query(
            "SELECT us.*, st.name AS software_name
             FROM usage_stats us
             JOIN software_titles st ON us.software_id = st.id
             ORDER BY us.term_name DESC, st.name ASC"
        );
        return $stmt->fetchAll();
    }

    public function getByTerm(string $termName): array {
        $stmt = $this->pdo->prepare(
            "SELECT us.*, st.name AS software_name
             FROM usage_stats us
             JOIN software_titles st ON us.software_id = st.id
             WHERE us.term_name = ?
             ORDER BY us.activation_rate DESC"
        );
        $stmt->execute([$termName]);
        return $stmt->fetchAll();
    }

    public function create(int $softwareId, string $termName, int $totalAllocated, int $totalActivated): bool {
        $rate = $totalAllocated > 0
            ? round(($totalActivated / $totalAllocated) * 100, 2)
            : 0.00;

        $stmt = $this->pdo->prepare(
            "INSERT INTO usage_stats (software_id, term_name, total_allocated, total_activated, activation_rate)
             VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$softwareId, $termName, $totalAllocated, $totalActivated, $rate]);
    }

    public function update(int $id, int $totalAllocated, int $totalActivated): bool {
        $rate = $totalAllocated > 0
            ? round(($totalActivated / $totalAllocated) * 100, 2)
            : 0.00;

        $stmt = $this->pdo->prepare(
            "UPDATE usage_stats SET total_allocated = ?, total_activated = ?, activation_rate = ? WHERE id = ?"
        );
        return $stmt->execute([$totalAllocated, $totalActivated, $rate, $id]);
    }

    public function getDistinctTerms(): array {
        $stmt = $this->pdo->query("SELECT DISTINCT term_name FROM usage_stats ORDER BY term_name DESC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
