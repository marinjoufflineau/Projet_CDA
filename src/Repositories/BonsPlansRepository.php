<?php
namespace App\Repositories;

use App\Config\DatabaseConnection;
use MongoDB\BSON\ObjectId;

final class BonsPlansRepository
{
    private $bonsPlans;
    private $categories;
    private $lieux;

    public function __construct()
    {
        $db = DatabaseConnection::getDatabase();
        $this->bonsPlans = $db->selectCollection('bons_plans');
        $this->categories = $db->selectCollection('categories');
        $this->lieux = $db->selectCollection('lieux');
    }

    public function findAll(?string $categoryId = null): array
    {
        $filter = [];
        if ($categoryId && $this->isObjectId($categoryId)) {
            $filter['categorie_id'] = $categoryId;
        }
        $items = $this->bonsPlans->find($filter, ['sort' => ['score' => -1, 'created_at' => -1]])->toArray();
        return array_map(fn($item) => $this->hydrate($item), $items);
    }

    public function search(string $term, ?string $categoryId = null): array
    {
        $term = trim($term);
        $filter = [];

        if ($term !== '') {
            $regex = new \MongoDB\BSON\Regex(preg_quote($term), 'i');
            $filter['$or'] = [
                ['titre' => $regex],
                ['description' => $regex],
                ['tags' => $regex],
            ];
        }

        if ($categoryId && $this->isObjectId($categoryId)) {
            $filter['categorie_id'] = $categoryId;
        }

        $items = $this->bonsPlans->find($filter, ['sort' => ['score' => -1, 'created_at' => -1]])->toArray();
        return array_map(fn($item) => $this->hydrate($item), $items);
    }

    public function featured(int $limit = 3): array
    {
        $items = $this->bonsPlans->find([], ['sort' => ['score' => -1], 'limit' => $limit])->toArray();
        return array_map(fn($item) => $this->hydrate($item), $items);
    }

    public function findById(string $id): ?array
    {
        if (!$this->isObjectId($id)) return null;
        $item = $this->bonsPlans->findOne(['_id' => new ObjectId($id)]);
        return $item ? $this->hydrate($item) : null;
    }

    public function create(array $data): string
    {
        $doc = $this->sanitize($data) + ['score' => 0, 'created_at' => new \MongoDB\BSON\UTCDateTime()];
        $result = $this->bonsPlans->insertOne($doc);
        return (string) $result->getInsertedId();
    }

    public function update(string $id, array $data): bool
    {
        if (!$this->isObjectId($id)) return false;
        $result = $this->bonsPlans->updateOne(['_id' => new ObjectId($id)], ['$set' => $this->sanitize($data)]);
        return $result->getMatchedCount() === 1;
    }

    public function delete(string $id): bool
    {
        if (!$this->isObjectId($id)) return false;
        return $this->bonsPlans->deleteOne(['_id' => new ObjectId($id)])->getDeletedCount() === 1;
    }

    public function count(): int
    {
        return $this->bonsPlans->countDocuments();
    }

    private function hydrate(object $item): array
    {
        $data = json_decode(json_encode($item), true);
        $data['_id'] = (string)$item->_id;
        $data['categorie_nom'] = $this->findName($this->categories, $data['categorie_id'] ?? null, 'Bon plan');
        $data['lieu_nom'] = $this->findName($this->lieux, $data['lieu_id'] ?? null, 'Local');
        $data['emoji'] = $data['emoji'] ?? '✨';
        $data['tags'] = $data['tags'] ?? [];
        return $data;
    }

    private function findName($collection, ?string $id, string $default): string
    {
        if (!$id || !$this->isObjectId($id)) return $default;
        $doc = $collection->findOne(['_id' => new ObjectId($id)]);
        return $doc['nom'] ?? $default;
    }

    private function sanitize(array $data): array
    {
        $tags = $data['tags'] ?? '';
        if (is_string($tags)) {
            $tags = array_values(array_filter(array_map('trim', explode(',', $tags))));
        }
        return [
            'titre' => trim($data['titre'] ?? ''),
            'description' => trim($data['description'] ?? ''),
            'categorie_id' => trim($data['categorie_id'] ?? ''),
            'lieu_id' => trim($data['lieu_id'] ?? ''),
            'prix' => isset($data['prix']) && $data['prix'] !== '' ? (float)$data['prix'] : null,
            'emoji' => trim($data['emoji'] ?? '✨') ?: '✨',
            'tags' => $tags,
        ];
    }

    private function isObjectId(string $id): bool
    {
        return preg_match('/^[a-f0-9]{24}$/i', $id) === 1;
    }
}
