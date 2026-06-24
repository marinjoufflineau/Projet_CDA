<?php
require_once __DIR__ . '/bootstrap.php';
App\Services\Auth::logout();
header('Location: /');
