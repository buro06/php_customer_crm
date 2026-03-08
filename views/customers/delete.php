<?php
require_once __DIR__ . '/../../config/app.php';
requireLogin();
verify_csrf();

$id = (int)($_POST['id'] ?? 0);
$db = getDB();
$stmt = $db->prepare("DELETE FROM customers WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['flash_success'] = 'Customer deleted successfully.';
header('Location: ' . url('/views/customers/list.php'));
exit;
