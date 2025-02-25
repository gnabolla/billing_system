<?php include  __DIR__ . '/../partials/head.php' ?>
<?php include  __DIR__ . '/../partials/nav.php' ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php include  __DIR__ . '/../partials/sidebar.php' ?>
        </div>
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Plan Details</h3>
                    <div>
                        <a href="<?= url('plans/edit?id=' . $plan['plan_id']) ?>" class="btn btn-primary me-2">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="<?= url('plans') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Plans
                        </a>
                    </div>
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
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Basic Information</h5>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Plan Name</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= htmlspecialchars($plan['plan_name']) ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Status</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?php if ($plan['status'] === 'Active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php elseif ($plan['status'] === 'Inactive'): ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php else: ?>
                                            <span class="badge bg-info"><?= htmlspecialchars($plan['status']) ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Description</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?= !empty($plan['plan_description']) ? nl2br(htmlspecialchars($plan['plan_description'])) : '<em class="text-muted">No description provided</em>' ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Billing Details</h5>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Monthly Fee</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">$<?= number_format($plan['monthly_fee'], 2) ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Speed Rate</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?= !empty($plan['speed_rate']) ? htmlspecialchars($plan['speed_rate']) : '<em class="text-muted">Not specified</em>' ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Billing Cycle</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= htmlspecialchars($plan['billing_cycle']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Metadata</h5>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-2 col-form-label text-muted">Created On</label>
                                <div class="col-sm-4">
                                    <p class="form-control-plaintext"><?= date('F d, Y g:i A', strtotime($plan['created_at'])) ?></p>
                                </div>
                                
                                <label class="col-sm-2 col-form-label text-muted">Last Updated</label>
                                <div class="col-sm-4">
                                    <p class="form-control-plaintext"><?= date('F d, Y g:i A', strtotime($plan['updated_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Subscribers using this plan (placeholder for now) -->
            <div class="card">
                <div class="card-header">
                    <h5>Subscribers Using This Plan</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($subscribersUsingPlan)): ?>
                        <p class="text-muted">No subscribers are currently using this plan.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Account No</th>
                                        <th>Subscriber Name</th>
                                        <th>Start Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subscribersUsingPlan as $subscriber): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($subscriber['account_no']) ?></td>
                                            <td>
                                                <?php if (!empty($subscriber['company_name'])): ?>
                                                    <?= htmlspecialchars($subscriber['company_name']) ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($subscriber['first_name'] . ' ' . $subscriber['last_name']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($subscriber['start_date'])) ?></td>
                                            <td>
                                                <?php if ($subscriber['status'] === 'Active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($subscriber['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= url('subscribers/view?id=' . $subscriber['subscriber_id']) ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include  __DIR__ . '/../partials/foot.php' ?>