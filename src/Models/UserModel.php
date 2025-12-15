<?php

namespace App\Models;

use App\Services\Database;
use App\Services\Logger;

class UserModel
{
    protected \PDO $db;
    protected $logger;

    public function __construct(string $use = 'mysql')
    {
        $this->logger = Logger::get('db');
        $this->db = $use === 'mariadb' ? Database::mariadb() : Database::mysql();
    }

    public function all(): array
    {
        $this->logger->info("Fetching all users");
        $stmt = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll() ?: [];
    }

    public function find($id): ?array
    {
        $this->logger->info("Fetching user {$id}");
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([(int)$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $name, string $email): int
    {
        $this->logger->info("Creating user {$email}");
        $stmt = $this->db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $email): bool
    {
        $this->logger->info("Updating user {$id}");
        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $id]);
    }

    public function delete(int $id): bool
    {
        $this->logger->info("Deleting user {$id}");
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
