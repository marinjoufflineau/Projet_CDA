<?php
require_once __DIR__ . '/../bootstrap.php';

use App\Repositories\BonsPlansRepository;
use App\Repositories\MetaRepository;
use App\Services\Auth;

Auth::requireAdmin();
$bonsRepo = new BonsPlansRepository();
$meta = new MetaRepository();
?>
<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Administration</title><link rel="stylesheet" href="/assets/css/style.css"></head><body>
<?php require __DIR__ . '/../partials/header.php'; ?>
<main class="container admin-layout">
  <?php require __DIR__ . '/../partials/admin_sidebar.php'; ?>
  <section>
    <div class="section-head"><div><h2>Tableau de bord</h2><p>Vue synthétique de l’activité et de la structure MongoDB.</p></div><a class="btn btn-primary" href="/admin/bons_plans.php?action=create">+ Nouveau bon plan</a></div>
    <div class="dashboard-grid">
      <div class="insight-card"><strong><?= $bonsRepo->count() ?></strong><span>Bons plans</span></div>
      <div class="insight-card"><strong><?= $meta->count('categories') ?></strong><span>Catégories</span></div>
      <div class="insight-card"><strong><?= $meta->count('commentaires') ?></strong><span>Commentaires</span></div>
      <div class="insight-card"><strong><?= $meta->count('votes') ?></strong><span>Votes</span></div>
    </div>
    <div class="page-card">
      <span class="eyebrow">Qualité CDA</span>
      <h2>Points techniques valorisables</h2>
      <p class="muted">Cette interface permet de démontrer une séparation claire entre présentation, logique métier et accès aux données. Les données sont stockées dans MongoDB via des repositories dédiés.</p>
      <div class="badges" style="margin-top:16px">
        <span class="badge badge-success">Architecture en couches</span><span class="badge badge-success">NoSQL</span><span class="badge badge-success">CRUD</span><span class="badge badge-success">CSRF</span><span class="badge badge-success">Sessions</span><span class="badge badge-success">Tests PHPUnit</span>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
</body></html>
