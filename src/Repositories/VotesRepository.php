<?php
namespace App\Repositories;

use App\Config\DatabaseConnection;

final class VotesRepository
{
    private $votes;
    private $bonsPlans;

    public function __construct()
    {
        $db = DatabaseConnection::getDatabase();
        $this->votes = $db->selectCollection('votes');
        $this->bonsPlans = $db->selectCollection('bons_plans');
    }

    public function vote(string $bonPlanId, string $userId, int $value): bool
    {
        if (!in_array($value, [-1, 1], true)) return false;
        $old = $this->votes->findOne(['bon_plan_id' => $bonPlanId, 'user_id' => $userId]);
        $oldValue = (int)($old['value'] ?? 0);
        $delta = $value - $oldValue;

        $this->votes->updateOne(
            ['bon_plan_id' => $bonPlanId, 'user_id' => $userId],
            ['$set' => ['value' => $value, 'updated_at' => new \MongoDB\BSON\UTCDateTime()]],
            ['upsert' => true]
        );
        $this->bonsPlans->updateOne(['_id' => new \MongoDB\BSON\ObjectId($bonPlanId)], ['$inc' => ['score' => $delta]]);
        return true;
    }
}
