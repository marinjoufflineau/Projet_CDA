<?php
namespace App\Repositories;

use App\Config\DatabaseConnection;
use MongoDB\BSON\ObjectId;

final class CommentairesRepository
{
    private $collection;

    public function __construct()
    {
        $this->collection = DatabaseConnection::getDatabase()->selectCollection('commentaires');
    }

    public function findByBonPlan(string $bonPlanId): array
    {
        return $this->collection->find(['bon_plan_id' => $bonPlanId], ['sort' => ['created_at' => -1]])->toArray();
    }

    public function add(string $bonPlanId, string $userId, string $contenu): bool
    {
        $contenu = trim($contenu);
        if ($contenu === '' || mb_strlen($contenu) > 1000) return false;
        $this->collection->insertOne([
            'bon_plan_id' => $bonPlanId,
            'user_id' => $userId,
            'contenu' => $contenu,
            'created_at' => new \MongoDB\BSON\UTCDateTime()
        ]);
        return true;
    }
}
