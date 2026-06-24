<?php
require_once __DIR__ . '/bootstrap.php';

use App\Services\Auth;
use App\Services\Security;

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::checkCsrf($_POST['_csrf'] ?? null)) { http_response_code(419); exit('Jeton CSRF invalide.'); }

    if (Auth::login($_POST['email'] ?? '', $_POST['password'] ?? '')) {
        header('Location: /');
        exit;
    }

    $error = 'Identifiants invalides.';
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<?php require __DIR__ . '/partials/header.php'; ?>

<main class="auth-wrap">
    <section class="auth-card">
        <span class="eyebrow">Espace sécurisé</span>
        <h1>Connexion</h1>
        <p class="muted">Connecte-toi pour voter, commenter ou accéder à l’administration.</p>

        <?php if ($error): ?><p class="alert alert-error"><?= Security::e($error) ?></p><?php endif; ?>

        <form method="post">
            <input type="hidden" name="_csrf" value="<?= Security::csrfToken() ?>">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" required placeholder="admin@local.test">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required placeholder="••••••••">
            </div>
            <button class="btn btn-primary btn-block">Se connecter</button>
        </form>

        <p class="muted">
            Compte admin : <strong>admin@local.test</strong> / <strong>admin</strong><br>
            Compte utilisateur : <strong>marin@local.test</strong> / <strong>marin</strong>
        </p>
    </section>
</main>
</body>
</html>
