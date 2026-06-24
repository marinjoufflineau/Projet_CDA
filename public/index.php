<?php
require_once __DIR__ . '/bootstrap.php';

use App\Repositories\BonsPlansRepository;
use App\Repositories\MetaRepository;
use App\Services\Security;

$query = trim($_GET['q'] ?? '');
$category = trim($_GET['categorie'] ?? '');
$repo = new BonsPlansRepository();
$meta = new MetaRepository();
$categories = $meta->categories();
$bonsPlans = $repo->search($query, $category);
$featured = $repo->featured(3);
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>LocalBonPlan — bons plans locaux</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="eyebrow">Application web sécurisée · PHP 8 · MongoDB</span>
      <h1>Trouve les meilleurs bons plans locaux sans perdre ton temps.</h1>
      <p>Une application simple pour découvrir, voter et commenter des offres locales. Côté technique : architecture en couches, repositories MongoDB, authentification, CSRF et tests unitaires.</p>
      <div class="hero-actions">
        <a class="btn btn-primary" href="#bons-plans">Explorer les offres</a>
        <a class="btn btn-ghost" href="/login.php">Connexion utilisateur</a>
      </div>
    </div>
    <aside class="hero-panel">
      <span class="eyebrow">Démo CDA</span>
      <h2>Projet complet, lisible et présentable</h2>
      <p>Le projet couvre les interfaces, les composants métier, l’accès NoSQL, les tests, la sécurité et une interface d’administration.</p>
      <div class="stat-grid">
        <div class="stat"><strong><?= $repo->count() ?></strong><span>Bons plans</span></div>
        <div class="stat"><strong><?= $meta->count('categories') ?></strong><span>Catégories</span></div>
        <div class="stat"><strong><?= $meta->count('commentaires') ?></strong><span>Commentaires</span></div>
        <div class="stat"><strong><?= $meta->count('votes') ?></strong><span>Votes</span></div>
      </div>
    </aside>
  </div>
</section>

<main id="bons-plans" class="section">
  <div class="container">
    <div class="section-head">
      <div>
        <h2>Bons plans à la une</h2>
        <p>Les offres les mieux notées par la communauté.</p>
      </div>
    </div>
    <section class="grid" style="margin-bottom:34px">
      <?php foreach ($featured as $bp): ?>
        <?php include __DIR__ . '/partials/deal_card.php'; ?>
      <?php endforeach; ?>
    </section>

    <div class="section-head">
      <div>
        <h2>Tous les bons plans</h2>
        <p>Recherche par mot-clé ou filtre par catégorie.</p>
      </div>
    </div>

    <form class="search-card" method="get">
      <input type="search" name="q" value="<?= Security::e($query) ?>" placeholder="Restaurant, sport, culture, coworking...">
      <select name="categorie">
        <option value="">Toutes les catégories</option>
        <?php foreach ($categories as $cat): $id=(string)$cat['_id']; ?>
          <option value="<?= Security::e($id) ?>" <?= $category === $id ? 'selected' : '' ?>><?= Security::e($cat['nom'] ?? '') ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary" type="submit">Rechercher</button>
      <a class="btn btn-light" href="/">Réinitialiser</a>
    </form>

    <?php if (!$bonsPlans): ?>
      <div class="empty-state page-card">Aucun bon plan ne correspond à cette recherche.</div>
    <?php else: ?>
      <section class="grid">
        <?php foreach ($bonsPlans as $bp): ?>
          <?php include __DIR__ . '/partials/deal_card.php'; ?>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  </div>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
