<?php
require_once __DIR__ . '/../../config/app.php';
requireAdmin();
verify_csrf();

$id = (int)($_POST['id'] ?? 0);

if ($id === $_SESSION['user_id']) {
    $_SESSION['flash_error'] = 'You cannot delete your own account.';
    header('Location: ' . url('/views/users/list.php'));
    exit;
}

$db = getDB();
$stmt = $db->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['flash_success'] = 'User deleted successfully.';
header('Location: ' . url('/views/users/list.php'));
exit;
