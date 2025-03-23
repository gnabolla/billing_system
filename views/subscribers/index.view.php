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
                    <h3>Subscribers</h3>
                    <a href="<?= url('subscribers/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Subscriber
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
                            <form action="<?= url('subscribers') ?>" method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search subscribers..." value="<?= htmlspecialchars($filters['search']) ?>">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select" id="statusFilter" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="Active" <?= $filters['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= $filters['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="Suspended" <?= $filters['status'] === 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </div>
                    </div>

                    <?php if (empty($subscribers)): ?>
                        <div class="alert alert-info">
                            No subscribers found. <a href="<?= url('subscribers/create') ?>">Add your first subscriber</a>.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Account No</th>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Registration</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subscribers as $subscriber): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($subscriber['account_no']) ?></td>
                                            <td>
                                                <?php if (!empty($subscriber['company_name'])): ?>
                                                    <?= htmlspecialchars($subscriber['company_name']) ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($subscriber['first_name'] . ' ' . $subscriber['last_name']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($subscriber['phone_number'])): ?>
                                                    <div><?= htmlspecialchars($subscriber['phone_number']) ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($subscriber['email'])): ?>
                                                    <div class="text-muted small"><?= htmlspecialchars($subscriber['email']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($subscriber['registration_date'])) ?></td>
                                            <td>
                                                <?php if ($subscriber['status'] === 'Active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php elseif ($subscriber['status'] === 'Inactive'): ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php elseif ($subscriber['status'] === 'Suspended'): ?>
                                                    <span class="badge bg-warning text-dark">Suspended</span>
                                                <?php elseif ($subscriber['status'] === 'Deleted'): ?>
                                                    <span class="badge bg-danger">Deleted</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info"><?= htmlspecialchars($subscriber['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= url('subscribers/view?id=' . $subscriber['subscriber_id']) ?>" class="btn btn-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= url('subscribers/edit?id=' . $subscriber['subscriber_id']) ?>" class="btn btn-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $subscriber['subscriber_id'] ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>

                                                <!-- Delete Confirmation Modal -->
                                                <div class="modal fade" id="deleteModal<?= $subscriber['subscriber_id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $subscriber['subscriber_id'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel<?= $subscriber['subscriber_id'] ?>">Confirm Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this subscriber?</p>
                                                                <p><strong>Account No:</strong> <?= htmlspecialchars($subscriber['account_no']) ?></p>
                                                                <p><strong>Name:</strong>
                                                                    <?php if (!empty($subscriber['company_name'])): ?>
                                                                        <?= htmlspecialchars($subscriber['company_name']) ?>
                                                                    <?php else: ?>
                                                                        <?= htmlspecialchars($subscriber['first_name'] . ' ' . $subscriber['last_name']) ?>
                                                                    <?php endif; ?>
                                                                </p>
                                                                <p class="text-danger">This action cannot be undone.</p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="<?= url('subscribers/delete?id=' . $subscriber['subscriber_id']) ?>" method="POST">
                                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Subscriber pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= url('subscribers?page=' . ($page - 1) .
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
                                            <a class="page-link" href="<?= url('subscribers?page=1' .
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
                                            <a class="page-link" href="<?= url('subscribers?page=' . $i .
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
                                            <a class="page-link" href="<?= url('subscribers?page=' . $totalPages .
                                                                            (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') .
                                                                            (isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '')) ?>">
                                                <?= $totalPages ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= url('subscribers?page=' . ($page + 1) .
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