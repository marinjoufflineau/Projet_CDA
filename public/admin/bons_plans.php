<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Repositories\BonsPlansRepository;
use App\Repositories\MetaRepository;
use App\Services\Auth;
use App\Services\Security;

Auth::requireAdmin();

$repo = new BonsPlansRepository();
$meta = new MetaRepository();
$editing = null;

if (isset($_GET['edit'])) {
    $editing = $repo->findById($_GET['edit']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::checkCsrf($_POST['_csrf'] ?? null)) { http_response_code(419); exit('Jeton CSRF invalide.'); }
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $repo->create($_POST);
    }

    if ($action === 'update') {
        $repo->update($_POST['id'] ?? '', $_POST);
    }

    if ($action === 'delete') {
        $repo->delete($_POST['id'] ?? '');
    }

    header('Location: /admin/bons_plans.php');
    exit;
}

$bonsPlans = $repo->findAll();
$categories = $meta->categories();
$lieux = $meta->lieux();
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des bons plans</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="container admin-layout">
    <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>

    <section>
        <div class="section-head">
            <div>
                <h2>Gestion des bons plans</h2>
                <p>Création, modification et suppression des documents MongoDB.</p>
            </div>
        </div>

        <form method="post" class="page-card">
            <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
            <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
            <?php if ($editing): ?><input type="hidden" name="id" value="<?= Security::e($editing['_id']) ?>"><?php endif; ?>

            <h2><?= $editing ? 'Modifier le bon plan' : 'Créer un bon plan' ?></h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="titre">Titre</label>
                    <input id="titre" name="titre" required value="<?= Security::e($editing['titre'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="prix">Prix</label>
                    <input id="prix" name="prix" type="number" step="0.01" value="<?= Security::e(isset($editing['prix']) ? (string)$editing['prix'] : '') ?>">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="emoji">Icône / emoji</label>
                    <input id="emoji" name="emoji" maxlength="4" value="<?= Security::e($editing['emoji'] ?? '✨') ?>">
                </div>
                <div class="form-group">
                    <label for="tags">Tags séparés par des virgules</label>
                    <input id="tags" name="tags" value="<?= Security::e(isset($editing['tags']) && is_array($editing['tags']) ? implode(', ', $editing['tags']) : '') ?>" placeholder="promo, local, étudiant">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="categorie_id">Catégorie</label>
                    <select id="categorie_id" name="categorie_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <?php $id = (string)$cat['_id']; ?>
                            <option value="<?= Security::e($id) ?>" <?= ($editing['categorie_id'] ?? '') === $id ? 'selected' : '' ?>><?= Security::e($cat['nom'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="lieu_id">Lieu</label>
                    <select id="lieu_id" name="lieu_id" required>
                        <?php foreach ($lieux as $lieu): ?>
                            <?php $id = (string)$lieu['_id']; ?>
                            <option value="<?= Security::e($id) ?>" <?= ($editing['lieu_id'] ?? '') === $id ? 'selected' : '' ?>><?= Security::e($lieu['nom'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?= Security::e($editing['description'] ?? '') ?></textarea>
            </div>

            <div class="actions">
                <button class="btn btn-primary"><?= $editing ? 'Enregistrer les modifications' : 'Créer le bon plan' ?></button>
                <?php if ($editing): ?><a class="btn btn-light" href="/admin/bons_plans.php">Annuler</a><?php endif; ?>
            </div>
        </form>

        <div class="section-head" style="margin-top: 28px;">
            <div>
                <h2>Liste des bons plans</h2>
                <p><?= count($bonsPlans) ?> élément(s) enregistré(s).</p>
            </div>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Lieu</th>
                        <th>Prix</th>
                        <th>Score</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($bonsPlans as $bp): ?>
                    <tr>
                        <td><strong><?= Security::e($bp['titre'] ?? '') ?></strong></td>
                        <td><span class="badge badge-success"><?= Security::e($bp['categorie_nom'] ?? '') ?></span></td>
                        <td><span class="badge"><?= Security::e($bp['lieu_nom'] ?? '') ?></span></td>
                        <td><?= isset($bp['prix']) && $bp['prix'] !== null ? number_format((float)$bp['prix'], 2, ',', ' ') . ' €' : '-' ?></td>
                        <td><span class="score"><?= (int)($bp['score'] ?? 0) ?></span></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-light btn-small" href="/bonplan.php?id=<?= urlencode($bp['_id']) ?>">Voir</a>
                                <a class="btn btn-light btn-small" href="/admin/bons_plans.php?edit=<?= urlencode($bp['_id']) ?>">Modifier</a>
                                <form method="post" onsubmit="return confirm('Supprimer ce bon plan ?')">
                                    <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= Security::e($bp['_id']) ?>">
                                    <button class="btn btn-danger btn-small">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
