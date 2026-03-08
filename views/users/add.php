<?php
require_once __DIR__ . '/../../config/app.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $display_name = trim($_POST['display_name'] ?? '');
    $role = $_POST['role'] ?? 'user';

    if ($username === '' || $password === '' || $display_name === '') {
        $_SESSION['flash_error'] = 'All fields are required.';
        header('Location: ' . url('/views/users/add.php'));
        exit;
    }

    if (!in_array($role, ['admin', 'user'])) {
        $role = 'user';
    }

    $db = getDB();

    $exists = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $exists->execute([$username]);
    if ($exists->fetchColumn() > 0) {
        $_SESSION['flash_error'] = 'Username already exists.';
        header('Location: ' . url('/views/users/add.php'));
        exit;
    }

    $stmt = $db->prepare("INSERT INTO users (username, password, display_name, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $display_name, $role]);

    $_SESSION['flash_success'] = 'User created successfully.';
    header('Location: ' . url('/views/users/list.php'));
    exit;
}

$pageTitle = 'Add User';
require_once BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-person-plus text-primary me-2"></i>Add User</h2>
    <a href="<?= url('/views/users/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" required autofocus>
                </div>
                <div class="col-md-6">
                    <label for="display_name" class="form-label fw-semibold">Display Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="display_name" name="display_name" required>
                </div>
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="4">
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label fw-semibold">Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Create User</button>
                <a href="<?= url('/views/users/list.php') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
