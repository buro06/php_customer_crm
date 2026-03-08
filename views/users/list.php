<?php
require_once __DIR__ . '/../../config/app.php';
requireAdmin();

$db = getDB();
$users = $db->query("SELECT * FROM users ORDER BY username ASC")->fetchAll();

$pageTitle = 'User Management';
require_once BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-shield-lock text-primary me-2"></i>User Management</h2>
    <a href="<?= url('/views/users/add.php') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add User</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Display Name</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($u['username']) ?></td>
                        <td><?= e($u['display_name']) ?></td>
                        <td>
                            <span class="badge badge-role <?= $u['role'] === 'admin' ? 'bg-primary' : 'bg-secondary' ?>">
                                <?= e(ucfirst($u['role'])) ?>
                            </span>
                        </td>
                        <td class="text-muted"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                        <td class="action-btns">
                            <a href="<?= url('/views/users/edit.php?id=' . $u['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                            <form method="POST" action="<?= url('/views/users/delete.php') ?>" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
