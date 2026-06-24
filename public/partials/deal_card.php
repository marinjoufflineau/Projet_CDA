<?php use App\Services\Security; ?>
<article class="deal-card">
  <div class="deal-cover"><?= Security::e($bp['emoji'] ?? '✨') ?></div>
  <div class="deal-body">
    <div class="badges">
      <span class="badge badge-success"><?= Security::e($bp['categorie_nom'] ?? 'Bon plan') ?></span>
      <span class="badge"><?= Security::e($bp['lieu_nom'] ?? 'Local') ?></span>
      <?php if (isset($bp['prix']) && $bp['prix'] !== null): ?><span class="badge badge-warning"><?= number_format((float)$bp['prix'], 2, ',', ' ') ?> €</span><?php endif; ?>
    </div>
    <h3 class="deal-title"><?= Security::e($bp['titre'] ?? '') ?></h3>
    <p class="deal-description"><?= Security::e(mb_strimwidth($bp['description'] ?? '', 0, 128, '...')) ?></p>
    <?php if (!empty($bp['tags'])): ?><div class="tag-row"><?php foreach (array_slice($bp['tags'],0,3) as $tag): ?><span class="tag">#<?= Security::e($tag) ?></span><?php endforeach; ?></div><?php endif; ?>
    <div class="deal-footer"><span class="score">▲ <?= (int)($bp['score'] ?? 0) ?></span><a class="btn btn-light" href="/bonplan.php?id=<?= urlencode($bp['_id']) ?>">Voir le détail</a></div>
  </div>
</article>
