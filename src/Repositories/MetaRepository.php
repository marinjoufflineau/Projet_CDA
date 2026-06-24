<?php
namespace App\Repositories;

use App\Config\DatabaseConnection;

final class MetaRepository
{
    private $db;

    public function __construct()
    {
        $this->db = DatabaseConnection::getDatabase();
    }

    public function categories(): array
    {
        return $this->db->selectCollection('categories')->find([], ['sort' => ['nom' => 1]])->toArray();
    }

    public function lieux(): array
    {
        return $this->db->selectCollection('lieux')->find([], ['sort' => ['nom' => 1]])->toArray();
    }

    public function count(string $collection): int
    {
        return $this->db->selectCollection($collection)->countDocuments();
    }
}
