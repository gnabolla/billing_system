<?php include  __DIR__ . '/../partials/head.php' ?>
<?php include  __DIR__ . '/../partials/nav.php' ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php include  __DIR__ . '/../partials/sidebar.php' ?>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Statements</h3>
                    <a href="<?= url('statements/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create New Statement
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="alert alert-<?= $_SESSION['flash_message_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                            <?= $_SESSION['flash_message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php
                        // Clear the flash message
                        unset($_SESSION['flash_message']);
                        unset($_SESSION['flash_message_type']);
                        ?>
                    <?php endif; ?>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form action="<?= url('statements') ?>" method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search statements..." value="<?= htmlspecialchars($filters['search']) ?>">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select" id="statusFilter" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="Unpaid" <?= $filters['status'] === 'Unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                <option value="Partially Paid" <?= $filters['status'] === 'Partially Paid' ? 'selected' : '' ?>>Partially Paid</option>
                                <option value="Paid" <?= $filters['status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="Overdue" <?= $filters['status'] === 'Overdue' ? 'selected' : '' ?>>Overdue</option>
                            </select>
                        </div>
                    </div>

                    <?php if (empty($statements)): ?>
                        <div class="alert alert-info">
                            No statements found. <a href="<?= url('statements/create') ?>">Create your first statement</a>.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Statement #</th>
                                        <th>Subscriber</th>
                                        <th>Period</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($statements as $statement): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($statement['statement_no']) ?></td>
                                            <td>
                                                <?php if (!empty($statement['company_name'])): ?>
                                                    <?= htmlspecialchars($statement['company_name']) ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($statement['first_name'] . ' ' . $statement['last_name']) ?>
                                                <?php endif; ?>
                                                <div class="text-muted small"><?= htmlspecialchars($statement['account_no']) ?></div>
                                            </td>
                                            <td>
                                                <?= date('M d', strtotime($statement['bill_period_start'])) ?> -
                                                <?= date('M d, Y', strtotime($statement['bill_period_end'])) ?>
                                            </td>
                                            <td>
                                                ₱<?= number_format($statement['total_amount'], 2) ?>
                                                <?php if ($statement['status'] !== 'Paid' && $statement['unpaid_amount'] < $statement['total_amount']): ?>
                                                    <div class="text-muted small">Unpaid: ₱<?= number_format($statement['unpaid_amount'], 2) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($statement['due_date'])) ?></td>
                                            <td>
                                                <?php if ($statement['status'] === 'Paid'): ?>
                                                    <span class="badge bg-success">Paid</span>
                                                <?php elseif ($statement['status'] === 'Partially Paid'): ?>
                                                    <span class="badge bg-warning text-dark">Partially Paid</span>
                                                <?php elseif ($statement['status'] === 'Overdue'): ?>
                                                    <span class="badge bg-danger">Overdue</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Unpaid</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= url('statements/view?id=' . $statement['statement_id']) ?>" class="btn btn-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <?php if ($statement['status'] !== 'Paid'): ?>
                                                        <a href="<?= url('payments/create?statement_id=' . $statement['statement_id']) ?>" class="btn btn-success" title="Record Payment">
                                                            <i class="bi bi-cash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Statement pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= url('statements?page=' . ($page - 1) .
                                                                            (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') .
                                                                            (isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '')) ?>">
                                                Previous
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">Previous</span>
                                        </li>
                                    <?php endif; ?>

                                    <?php
                                    // Calculate range of pages to show
                                    $range = 2; // Number of pages to show on each side of current page
                                    $rangeStart = max(1, $page - $range);
                                    $rangeEnd = min($totalPages, $page + $range);

                                    // First page
                                    if ($rangeStart > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= url('statements?page=1' .
                                                                            (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') .
                                                                            (isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '')) ?>">
                                                1
                                            </a>
                                        </li>
                                        <?php if ($rangeStart > 2): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif;
                                    endif;

                                    // Pages in range
                                    for ($i = $rangeStart; $i <= $rangeEnd; $i++): ?>
                                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= url('statements?page=' . $i .
                                                                            (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') .
                                                                            (isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '')) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                        <?php endfor;

                                    // Last page
                                    if ($rangeEnd < $totalPages):
                                        if ($rangeEnd < $totalPages - 1): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= url('statements?page=' . $totalPages .
                                                                            (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') .
                                                                            (isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '')) ?>">
                                                <?= $totalPages ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= url('statements?page=' . ($page + 1) .
                                                                            (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') .
                                                                            (isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '')) ?>">
                                                Next
                                            </a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">Next</span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle status filter change
    document.getElementById('statusFilter').addEventListener('change', function() {
        const currentUrl = new URL(window.location.href);
        const status = this.value;

        if (status) {
            currentUrl.searchParams.set('status', status);
        } else {
            currentUrl.searchParams.delete('status');
        }

        // Keep the current page and search parameters
        if (currentUrl.searchParams.has('page')) {
            currentUrl.searchParams.set('page', 1); // Reset to page 1 when changing filters
        }

        window.location.href = currentUrl.toString();
    });
</script>

<?php include  __DIR__ . '/../partials/foot.php' ?>