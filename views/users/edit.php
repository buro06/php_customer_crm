<?php
require_once __DIR__ . '/../../config/app.php';
requireAdmin();

$db = getDB();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['flash_error'] = 'User not found.';
    header('Location: ' . url('/views/users/list.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $display_name = trim($_POST['display_name'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';

    if ($display_name === '') {
        $_SESSION['flash_error'] = 'Display name is required.';
        header('Location: ' . url('/views/users/edit.php?id=' . $id));
        exit;
    }

    if (!in_array($role, ['admin', 'user'])) {
        $role = 'user';
    }

    // Prevent removing own admin role
    if ($id === $_SESSION['user_id'] && $role !== 'admin') {
        $_SESSION['flash_error'] = 'You cannot remove your own admin role.';
        header('Location: ' . url('/views/users/edit.php?id=' . $id));
        exit;
    }

    if ($password !== '') {
        $stmt = $db->prepare("UPDATE users SET display_name=?, role=?, password=? WHERE id=?");
        $stmt->execute([$display_name, $role, password_hash($password, PASSWORD_DEFAULT), $id]);
    } else {
        $stmt = $db->prepare("UPDATE users SET display_name=?, role=? WHERE id=?");
        $stmt->execute([$display_name, $role, $id]);
    }

    // Update session if editing self
    if ($id === $_SESSION['user_id']) {
        $_SESSION['display_name'] = $display_name;
        $_SESSION['user_role'] = $role;
    }

    $_SESSION['flash_success'] = 'User updated successfully.';
    header('Location: ' . url('/views/users/list.php'));
    exit;
}

$pageTitle = 'Edit User';
require_once BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Edit User</h2>
    <a href="<?= url('/views/users/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $user['id'] ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Username</label>
                    <input type="text" class="form-control" value="<?= e($user['username']) ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label for="display_name" class="form-label fw-semibold">Display Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="display_name" name="display_name" value="<?= e($user['display_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current" minlength="4">
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label fw-semibold">Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Update User</button>
                <a href="<?= url('/views/users/list.php') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
