<?php
require_once __DIR__ . '/../../config/app.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $_SESSION['flash_error'] = 'Customer name is required.';
        header('Location: ' . url('/views/customers/add.php'));
        exit;
    }

    $db = getDB();
    $stmt = $db->prepare("INSERT INTO customers (name, email, phone, company, street, city, state, zip, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
        $_SESSION['user_id'],
    ]);

    $_SESSION['flash_success'] = 'Customer added successfully.';
    header('Location: ' . url('/views/customers/list.php'));
    exit;
}

$pageTitle = 'Add Customer';
require_once BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-person-plus text-primary me-2"></i>Add Customer</h2>
    <a href="<?= url('/views/customers/list.php') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required autofocus>
                </div>
                <div class="col-md-6">
                    <label for="company" class="form-label fw-semibold">Company</label>
                    <input type="text" class="form-control" id="company" name="company">
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">Phone</label>
                    <input type="tel" class="form-control" id="phone" name="phone">
                </div>

                <hr class="my-2">
                <h6 class="fw-semibold text-muted"><i class="bi bi-geo-alt me-1"></i>Address</h6>

                <div class="col-12">
                    <label for="street" class="form-label fw-semibold">Street</label>
                    <input type="text" class="form-control" id="street" name="street">
                </div>
                <div class="col-md-4">
                    <label for="city" class="form-label fw-semibold">City</label>
                    <input type="text" class="form-control" id="city" name="city">
                </div>
                <div class="col-md-4">
                    <label for="state" class="form-label fw-semibold">State</label>
                    <input type="text" class="form-control" id="state" name="state">
                </div>
                <div class="col-md-4">
                    <label for="zip" class="form-label fw-semibold">ZIP Code</label>
                    <input type="text" class="form-control" id="zip" name="zip">
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label fw-semibold">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Save Customer</button>
                <a href="<?= url('/views/customers/list.php') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
