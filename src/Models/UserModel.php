<?php

namespace App\Models;

use App\Services\Database;
use App\Services\Logger;

class UserModel
{
    protected $db;
    protected $logger;

    public function __construct($use = 'mysql')
    {
        $this->logger = Logger::get('db');
        $this->db = $use === 'mariadb' ? Database::mariadb() : Database::mysql();
    }

    public function all()
    {
        $this->logger->info("Fetching all users");
        $stmt = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function find($id)
    {
        $this->logger->info("Fetching user {$id}");
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([(int)$id]);
        return $stmt->fetch();
    }

    public function create($name, $email)
    {
        $this->logger->info("Creating user {$email}");
        $stmt = $this->db->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);
        return $this->db->lastInsertId();
    }
}
