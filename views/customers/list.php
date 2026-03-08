<?php
require_once __DIR__ . '/../../config/app.php';
requireLogin();

$db = getDB();
$search = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

if ($search !== '') {
    $like = '%' . $search . '%';
    $countStmt = $db->prepare("SELECT COUNT(*) FROM customers WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? OR company LIKE ?");
    $countStmt->execute([$like, $like, $like, $like]);
    $total = $countStmt->fetchColumn();

    $stmt = $db->prepare("SELECT * FROM customers WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? OR company LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$like, $like, $like, $like, $perPage, $offset]);
} else {
    $total = $db->query("SELECT COUNT(*) FROM customers")->fetchColumn();
    $stmt = $db->prepare("SELECT * FROM customers ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute([$perPage, $offset]);
}

$customers = $stmt->fetchAll();
$totalPages = max(1, ceil($total / $perPage));

$pageTitle = 'Customers';
require_once BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="bi bi-person-lines-fill text-primary me-2"></i>Customers</h2>
    <a href="<?= url('/views/customers/add.php') ?>" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Customer</a>
</div>

<div class="card">
    <div class="card-header bg-white py-3">
        <form method="GET" class="d-flex gap-2">
            <div class="input-group search-box">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="q" placeholder="Search customers..." value="<?= e($search) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($search): ?>
                <a href="<?= url('/views/customers/list.php') ?>" class="btn btn-outline-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <?php if (empty($customers)): ?>
            <div class="text-center text-muted py-5">
                <i class="bi bi-search" style="font-size: 3rem;"></i>
                <p class="mt-2"><?= $search ? 'No customers found matching your search.' : 'No customers yet.' ?></p>
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
                        <th>City</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $c): ?>
                    <tr>
                        <td class="fw-semibold"><?= e($c['name']) ?></td>
                        <td><a href="mailto:<?= e($c['email']) ?>"><?= e($c['email']) ?></a></td>
                        <td><?= e($c['phone']) ?></td>
                        <td><?= e($c['company']) ?></td>
                        <td><?= e($c['city']) ?></td>
                        <td class="action-btns">
                            <a href="<?= url('/views/customers/edit.php?id=' . $c['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="<?= url('/views/customers/delete.php') ?>" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-delete" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-white">
        <nav>
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">Prev</a>
                </li>
                <?php
                $range = 2;
                $pages = [];
                $pages[] = 1;
                if ($totalPages > 1) $pages[] = $totalPages;
                for ($i = max(2, $page - $range); $i <= min($totalPages - 1, $page + $range); $i++) {
                    $pages[] = $i;
                }
                $pages = array_unique($pages);
                sort($pages);
                $prev = 0;
                foreach ($pages as $p):
                    if ($p - $prev > 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php $prev = $p;
                endforeach; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?q=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
