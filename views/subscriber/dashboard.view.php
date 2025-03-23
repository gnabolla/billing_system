<?php include __DIR__ . '/partials/head.php' ?>
<?php include __DIR__ . '/partials/nav.php' ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php include __DIR__ . '/partials/sidebar.php' ?>
        </div>
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Subscriber Dashboard</h3>
                </div>
                <div class="card-body">
                    <h4>Welcome, <?= htmlspecialchars($subscriber['company_name'] ?: $subscriber['first_name'] . ' ' . $subscriber['last_name']) ?>!</h4>
                    <p class="text-muted">Account #: <?= htmlspecialchars($subscriber['account_no']) ?></p>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Active Plans</h5>
                                    <p class="card-text display-4"><?= count($activePlans) ?></p>
                                    <a href="<?= url('subscriber/plans') ?>" class="btn btn-light btn-sm">View Details</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card <?= $totalUnpaid > 0 ? 'bg-warning' : 'bg-success' ?> text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Outstanding Balance</h5>
                                    <p class="card-text display-4">₱<?= number_format($totalUnpaid, 2) ?></p>
                                    <a href="<?= url('subscriber/statements') ?>" class="btn btn-light btn-sm">View Statements</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Next Payment Due</h5>
                                    <p class="card-text display-4">
                                        <?php if ($nextPaymentDue): ?>
                                            <?= date('M d', $nextPaymentDue) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </p>
                                    <a href="<?= url('subscriber/payments') ?>" class="btn btn-light btn-sm">Payment History</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5>Recent Statements</h5>
                                    <a href="<?= url('subscriber/statements') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recentStatements)): ?>
                                        <p class="text-muted">No recent statements found.</p>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Statement #</th>
                                                        <th>Amount</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recentStatements as $statement): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?= url('subscriber/statement?id=' . $statement['statement_id']) ?>">
                                                                    <?= htmlspecialchars($statement['statement_no']) ?>
                                                                </a>
                                                            </td>
                                                            <td>₱<?= number_format($statement['total_amount'], 2) ?></td>
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
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5>Recent Payments</h5>
                                    <a href="<?= url('subscriber/payments') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recentPayments)): ?>
                                        <p class="text-muted">No recent payments found.</p>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Receipt #</th>
                                                        <th>Amount</th>
                                                        <th>Date</th>
                                                        <th>Method</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recentPayments as $payment): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?= url('subscriber/payment?id=' . $payment['payment_id']) ?>">
                                                                    <?= htmlspecialchars($payment['or_no']) ?>
                                                                </a>
                                                            </td>
                                                            <td>₱<?= number_format($payment['paid_amount'], 2) ?></td>
                                                            <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                                            <td><?= htmlspecialchars($payment['payment_method']) ?></td>
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
            </div>
            
            <!-- Current Plans Section -->
            <div class="card">
                <div class="card-header">
                    <h5>Your Current Plans</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($activePlans)): ?>
                        <div class="alert alert-warning">
                            <p>You don't have any active plans. Please contact customer support for assistance.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Plan</th>
                                        <th>Monthly Fee</th>
                                        <th>Speed</th>
                                        <th>Start Date</th>
                                        <th>Billing Cycle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($activePlans as $plan): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($plan['plan_name']) ?></strong></td>
                                            <td>₱<?= number_format($plan['monthly_fee'], 2) ?></td>
                                            <td><?= htmlspecialchars($plan['speed_rate']) ?></td>
                                            <td><?= date('M d, Y', strtotime($plan['start_date'])) ?></td>
                                            <td><?= htmlspecialchars($plan['billing_cycle']) ?></td>
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

<?php include __DIR__ . '/../partials/foot.php' ?>