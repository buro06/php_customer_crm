<?php
require_once __DIR__ . '/../config/app.php';
requireLogin();

$db = getDB();
$totalCustomers = $db->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$recentCustomers = $db->query("SELECT * FROM customers ORDER BY created_at DESC LIMIT 5")->fetchAll();

$pageTitle = 'Dashboard';
require_once BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard</h2>
    <a href="<?= url('/views/customers/add.php') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Customer</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Total Customers</div>
                    <div class="stat-number"><?= $totalCustomers ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-people"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Active Users</div>
                    <div class="stat-number"><?= $totalUsers ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-person-check"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Companies</div>
                    <div class="stat-number"><?= $db->query("SELECT COUNT(DISTINCT company) FROM customers WHERE company != '' AND company IS NOT NULL")->fetchColumn() ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-building"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold"><i class="bi bi-clock-history text-primary me-2"></i>Recent Customers</h5>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentCustomers)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-2">No customers yet. <a href="<?= url('/views/customers/add.php') ?>">Add your first customer</a>.</p>
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Added</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentCustomers as $c): ?>
                    <tr style="cursor:pointer" onclick="window.location='<?= url('/views/customers/edit.php?id=' . $c['id']) ?>'">
                        <td class="fw-semibold"><?= e($c['name']) ?></td>
                        <td><?= e($c['email']) ?></td>
                        <td><?= e($c['phone']) ?></td>
                        <td><?= e($c['company']) ?></td>
                        <td class="text-muted"><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
