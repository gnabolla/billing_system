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
                    <h3>Subscriber Details</h3>
                    <div>
                        <a href="<?= url('subscribers/edit?id=' . $subscriber['subscriber_id']) ?>" class="btn btn-primary me-2">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="<?= url('subscribers') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
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
                                <label class="col-sm-4 col-form-label text-muted">Account Number</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= htmlspecialchars($subscriber['account_no']) ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Status</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?php if ($subscriber['status'] === 'Active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php elseif ($subscriber['status'] === 'Inactive'): ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php elseif ($subscriber['status'] === 'Suspended'): ?>
                                            <span class="badge bg-warning text-dark">Suspended</span>
                                        <?php else: ?>
                                            <span class="badge bg-info"><?= htmlspecialchars($subscriber['status']) ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Registration Date</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= date('F d, Y', strtotime($subscriber['registration_date'])) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Contact Details</h5>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Phone Number</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?= !empty($subscriber['phone_number']) ? htmlspecialchars($subscriber['phone_number']) : '<em class="text-muted">Not provided</em>' ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Email</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?= !empty($subscriber['email']) ? htmlspecialchars($subscriber['email']) : '<em class="text-muted">Not provided</em>' ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label text-muted">Address</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= htmlspecialchars($subscriber['address']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Identity</h5>
                            
                            <?php if (!empty($subscriber['company_name'])): ?>
                                <div class="mb-3 row">
                                    <label class="col-sm-3 col-form-label text-muted">Company/Business Name</label>
                                    <div class="col-sm-9">
                                        <p class="form-control-plaintext"><?= htmlspecialchars($subscriber['company_name']) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label text-muted">Full Name</label>
                                <div class="col-sm-9">
                                    <p class="form-control-plaintext">
                                        <?php 
                                            $fullName = trim($subscriber['first_name'] . ' ' . $subscriber['middle_name'] . ' ' . $subscriber['last_name']);
                                            echo !empty($fullName) ? htmlspecialchars($fullName) : '<em class="text-muted">Not provided</em>';
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Metadata</h5>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label text-muted">Created On</label>
                                <div class="col-sm-9">
                                    <p class="form-control-plaintext"><?= date('F d, Y g:i A', strtotime($subscriber['created_at'])) ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-3 row">
                                <label class="col-sm-3 col-form-label text-muted">Last Updated</label>
                                <div class="col-sm-9">
                                    <p class="form-control-plaintext"><?= date('F d, Y g:i A', strtotime($subscriber['updated_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Active Plans -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Active Plans</h5>
                    <a href="<?= url('subscribers/assign-plan?id=' . $subscriber['subscriber_id']) ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Assign Plan
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($subscriberPlans)): ?>
                        <p class="text-muted">No active plans found. Assign a plan to this subscriber.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Monthly Fee</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subscriberPlans as $plan): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($plan['plan_name']) ?></td>
                                            <td><?= htmlspecialchars(number_format($plan['monthly_fee'], 2)) ?></td>
                                            <td><?= date('M d, Y', strtotime($plan['start_date'])) ?></td>
                                            <td><?= !empty($plan['end_date']) ? date('M d, Y', strtotime($plan['end_date'])) : '<em>Active</em>' ?></td>
                                            <td>
                                                <?php if ($plan['status'] === 'Active'): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($plan['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= url('subscriber-plans/edit?id=' . $plan['subscriber_plan_id']) ?>" class="btn btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if ($plan['status'] === 'Active'): ?>
                                                        <a href="<?= url('subscriber-plans/terminate?id=' . $plan['subscriber_plan_id']) ?>" class="btn btn-outline-danger">
                                                            <i class="bi bi-x-circle"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Statements -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Recent Statements</h5>
                    <a href="<?= url('statements/create?subscriber_id=' . $subscriber['subscriber_id']) ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Statement
                    </a>
                </div>
                <div class="card-body">
                    <?php if (empty($statements)): ?>
                        <p class="text-muted">No statements found for this subscriber.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Statement #</th>
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
                                                <?= date('M d', strtotime($statement['bill_period_start'])) ?> - 
                                                <?= date('M d, Y', strtotime($statement['bill_period_end'])) ?>
                                            </td>
                                            <td><?= htmlspecialchars(number_format($statement['total_amount'], 2)) ?></td>
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
                                                    <a href="<?= url('statements/view?id=' . $statement['statement_id']) ?>" class="btn btn-outline-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <?php if ($statement['status'] !== 'Paid'): ?>
                                                        <a href="<?= url('payments/create?statement_id=' . $statement['statement_id']) ?>" class="btn btn-outline-success">
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
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Payments -->
            <div class="card">
                <div class="card-header">
                    <h5>Recent Payments</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($payments)): ?>
                        <p class="text-muted">No payments found for this subscriber.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Statement</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($payment['or_no']) ?></td>
                                            <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                            <td><?= htmlspecialchars(number_format($payment['paid_amount'], 2)) ?></td>
                                            <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                                            <td><?= htmlspecialchars($payment['statement_no']) ?></td>
                                            <td>
                                                <a href="<?= url('payments/view?id=' . $payment['payment_id']) ?>" class="btn btn-sm btn-outline-info">
                                                    <i class="bi bi-eye"></i>
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