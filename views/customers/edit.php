<?php
require_once __DIR__ . '/../../config/app.php';
requireLogin();

$db = getDB();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch();

if (!$customer) {
    $_SESSION['flash_error'] = 'Customer not found.';
    header('Location: ' . url('/views/customers/list.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $_SESSION['flash_error'] = 'Customer name is required.';
        header('Location: ' . url('/views/customers/edit.php?id=' . $id));
        exit;
    }

    $stmt = $db->prepare("UPDATE customers SET name=?, email=?, phone=?, company=?, street=?, city=?, state=?, zip=?, notes=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
    $stmt->execute([
        $name,
        trim($_POST['email'] ?? ''),
        trim($_POST['phone'] ?? ''),
        trim($_POST['company'] ?? ''),
        trim($_POST['street'] ?? ''),
        trim($_POST['city'] ?? ''),
        trim($_POST['state'] ?? ''),
        trim($_POST['zip'] ?? ''),
        trim($_POST['notes'] ?? ''),
        $id,
    ]);

    $_SESSION['flash_success'] = 'Customer updated successfully.';
    header('Location: ' . url('/views/customers/list.php'));
    exit;
}

$pageTitle = 'Edit Customer';
$c = $customer;
require_once BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Customer</h2>
    <a href="<?= url('/views/customers/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $c['id'] ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= e($c['name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="company" class="form-label fw-semibold">Company</label>
                    <input type="text" class="form-control" id="company" name="company" value="<?= e($c['company']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= e($c['email']) ?>">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">Phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone" value="<?= e($c['phone']) ?>">
                </div>

                <hr class="my-2">
                <h6 class="fw-semibold text-muted"><i class="bi bi-geo-alt me-1"></i>Address</h6>

                <div class="col-12">
                    <label for="street" class="form-label fw-semibold">Street</label>
                    <input type="text" class="form-control" id="street" name="street" value="<?= e($c['street']) ?>">
                </div>
                <div class="col-md-4">
                    <label for="city" class="form-label fw-semibold">City</label>
                    <input type="text" class="form-control" id="city" name="city" value="<?= e($c['city']) ?>">
                </div>
                <div class="col-md-4">
                    <label for="state" class="form-label fw-semibold">State</label>
                    <input type="text" class="form-control" id="state" name="state" value="<?= e($c['state']) ?>">
                </div>
                <div class="col-md-4">
                    <label for="zip" class="form-label fw-semibold">ZIP Code</label>
                    <input type="text" class="form-control" id="zip" name="zip" value="<?= e($c['zip']) ?>">
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label fw-semibold">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"><?= e($c['notes']) ?></textarea>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-between">
                <div>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Update Customer</button>
                    <a href="<?= url('/views/customers/list.php') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
                <form method="POST" action="<?= url('/views/customers/delete.php') ?>" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <button type="submit" class="btn btn-outline-danger btn-delete"><i class="bi bi-trash me-1"></i>Delete</button>
                </form>
            </div>
        </form>
    </div>
</div>

<div class="card mt-3">
    <div class="card-body text-muted small">
        <i class="bi bi-info-circle me-1"></i>
        Created: <?= date('M j, Y g:i A', strtotime($c['created_at'])) ?>
        | Last updated: <?= date('M j, Y g:i A', strtotime($c['updated_at'])) ?>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
