<?php
use App\Services\Auth;
use App\Services\Security;
$current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$user = Auth::user();
?>
<header class="navbar">
  <div class="container nav-inner">
    <a class="brand" href="/">
      <span class="brand-mark">%</span>
      <span>LocalBonPlan</span>
    </a>
    <nav class="nav-links">
      <a class="<?= $current === '/' || $current === '/index.php' ? 'active' : '' ?>" href="/">Accueil</a>
      <?php if ($user): ?>
        <?php if (Auth::isAdmin()): ?><a class="<?= str_starts_with($current, '/admin') ? 'active' : '' ?>" href="/admin/index.php">Admin</a><?php endif; ?>
        <a href="/logout.php">Déconnexion</a>
      <?php else: ?>
        <a class="<?= $current === '/login.php' ? 'active' : '' ?>" href="/login.php">Connexion</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
