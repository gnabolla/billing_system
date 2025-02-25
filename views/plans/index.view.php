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
                    <h3>Plans</h3>
                    <a href="<?= url('plans/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add New Plan
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
                            <form action="<?= url('plans') ?>" method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search plans..." value="<?= htmlspecialchars($filters['search']) ?>">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select" id="statusFilter" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="Active" <?= $filters['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                <option value="Inactive" <?= $filters['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <p class="mb-0"><strong>Active Plan Subscriptions:</strong> <?= $activePlansCount ?></p>
                        </div>
                    </div>
                    
                    <?php if (empty($plans)): ?>
                        <div class="alert alert-info">
                            No plans found. <a href="<?= url('plans/create') ?>">Add your first plan</a>.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Plan Name</th>
                                        <th>Monthly Fee</th>
                                        <th>Speed Rate</th>
                                        <th>Billing Cycle</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($plans as $plan): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($plan['plan_name']) ?></strong>
                                                <?php if (!empty($plan['plan_description'])): ?>
                                                    <div class="text-muted small"><?= htmlspecialchars(substr($plan['plan_description'], 0, 60)) ?><?= strlen($plan['plan_description']) > 60 ? '...' : '' ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= number_format($plan['monthly_fee'], 2) ?></td>
                                            <td><?= htmlspecialchars($plan['speed_rate']) ?></td>
                                            <td><?= htmlspecialchars($plan['billing_cycle']) ?></td>
                                            <td>
                                                <?php if ($plan['status'] === 'Active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php elseif ($plan['status'] === 'Inactive'): ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info"><?= htmlspecialchars($plan['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= url('plans/view?id=' . $plan['plan_id']) ?>" class="btn btn-info" title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= url('plans/edit?id=' . $plan['plan_id']) ?>" class="btn btn-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-danger" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $plan['plan_id'] ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Delete Confirmation Modal -->
                                                <div class="modal fade" id="deleteModal<?= $plan['plan_id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $plan['plan_id'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteModalLabel<?= $plan['plan_id'] ?>">Confirm Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Are you sure you want to delete this plan?</p>
                                                                <p><strong>Plan Name:</strong> <?= htmlspecialchars($plan['plan_name']) ?></p>
                                                                <p><strong>Monthly Fee:</strong> <?= number_format($plan['monthly_fee'], 2) ?></p>
                                                                
                                                                <p class="text-danger">
                                                                    <strong>Note:</strong> If this plan is currently assigned to subscribers, 
                                                                    it will be marked as inactive instead of being deleted.
                                                                </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="<?= url('plans/delete?id=' . $plan['plan_id']) ?>" method="POST">
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
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Plan pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= url('plans?page=' . ($page - 1) . 
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
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="<?= url('plans?page=' . $i . 
                                                (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') . 
                                                (isset($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '')) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="<?= url('plans?page=' . ($page + 1) . 
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