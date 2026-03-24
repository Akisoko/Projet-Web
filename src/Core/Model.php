<?php

namespace App\Core;

use App\Core\Database;
use PDO;

class Model {
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = "id";

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): bool {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)"
        );

        return $stmt->execute(array_values($data));
    }

    public function update(int $id, array $data): bool {
        $fields = implode(',', array_map(fn($col) => "$col = ?", array_keys($data)));
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET $fields WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([...array_values($data), $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$id]);
    }
}

