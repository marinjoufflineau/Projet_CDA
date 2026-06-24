<?php
require_once __DIR__ . '/bootstrap.php';

use App\Repositories\BonsPlansRepository;
use App\Repositories\CommentairesRepository;
use App\Repositories\VotesRepository;
use App\Services\Auth;
use App\Services\Security;

$id = $_GET['id'] ?? '';
$bonsRepo = new BonsPlansRepository();
$commentairesRepo = new CommentairesRepository();
$bonPlan = $bonsRepo->findById($id);

if (!$bonPlan) { http_response_code(404); exit('Bon plan introuvable'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && Auth::user()) {
    if (!Security::checkCsrf($_POST['_csrf'] ?? null)) { http_response_code(419); exit('Jeton CSRF invalide.'); }
    if (isset($_POST['commentaire'])) $commentairesRepo->add($id, Auth::user()['id'], $_POST['commentaire']);
    if (isset($_POST['vote'])) (new VotesRepository())->vote($id, Auth::user()['id'], (int)$_POST['vote']);
    header('Location: /bonplan.php?id=' . urlencode($id)); exit;
}
$commentaires = $commentairesRepo->findByBonPlan($id);
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= Security::e($bonPlan['titre'] ?? '') ?></title><link rel="stylesheet" href="/assets/css/style.css"></head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>
<main class="section">
  <div class="container detail-layout">
    <section class="page-card detail-hero">
      <div class="detail-emoji"><?= Security::e($bonPlan['emoji'] ?? '✨') ?></div>
      <div class="badges">
        <span class="badge badge-success"><?= Security::e($bonPlan['categorie_nom'] ?? 'Bon plan') ?></span>
        <span class="badge"><?= Security::e($bonPlan['lieu_nom'] ?? 'Local') ?></span>
        <?php if (isset($bonPlan['prix']) && $bonPlan['prix'] !== null): ?><span class="badge badge-warning"><?= number_format((float)$bonPlan['prix'],2,',',' ') ?> €</span><?php endif; ?>
      </div>
      <h1 style="font-size:clamp(2rem,4vw,3.5rem);letter-spacing:-.06em;margin:18px 0 12px"><?= Security::e($bonPlan['titre'] ?? '') ?></h1>
      <p class="muted" style="font-size:1.08rem;line-height:1.8"><?= nl2br(Security::e($bonPlan['description'] ?? '')) ?></p>
      <?php if (!empty($bonPlan['tags'])): ?><div class="tag-row" style="margin-top:18px"><?php foreach ($bonPlan['tags'] as $tag): ?><span class="tag">#<?= Security::e($tag) ?></span><?php endforeach; ?></div><?php endif; ?>
      <div style="margin-top:24px"><a class="btn btn-light" href="/">← Retour aux bons plans</a></div>
    </section>

    <aside class="page-card">
      <span class="eyebrow">Interaction</span>
      <h2 style="font-size:2rem;letter-spacing:-.04em">Score communautaire</h2>
      <p class="score" style="font-size:2.2rem">▲ <?= (int)($bonPlan['score'] ?? 0) ?></p>
      <?php if (Auth::user()): ?>
        <form method="post" class="vote-row">
          <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
          <button class="btn btn-primary" name="vote" value="1">👍 Recommander</button>
          <button class="btn btn-danger" name="vote" value="-1">👎 Pas convaincu</button>
        </form>
      <?php else: ?>
        <p class="muted">Connecte-toi pour voter et publier un commentaire.</p>
        <a class="btn btn-primary" href="/login.php">Se connecter</a>
      <?php endif; ?>
    </aside>

    <section class="page-card">
      <h2>Commentaires</h2>
      <?php if (Auth::user()): ?>
        <form method="post" style="margin-bottom:20px">
          <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
          <label for="commentaire">Ajouter un retour d’expérience</label>
          <textarea id="commentaire" name="commentaire" maxlength="1000" required placeholder="Pourquoi ce bon plan vaut le coup ?"></textarea>
          <button class="btn btn-primary" style="margin-top:10px">Publier</button>
        </form>
      <?php endif; ?>
      <?php if (!$commentaires): ?><div class="empty-state">Aucun commentaire pour le moment.</div><?php else: ?>
        <?php foreach ($commentaires as $c): ?><article class="comment"><p><?= nl2br(Security::e($c['contenu'] ?? '')) ?></p></article><?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
</body></html>
