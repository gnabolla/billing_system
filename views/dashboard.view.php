<?php include 'partials/head.php' ?>
<?php include 'partials/nav.php' ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php include 'partials/sidebar.php' ?>
        </div>
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Dashboard</h3>
                </div>
                <div class="card-body">
                    <h4>Welcome, <?= htmlspecialchars($user['first_name']) ?> <?= htmlspecialchars($user['last_name']) ?>!</h4>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Subscribers</h5>
                                    <p class="card-text display-4"><?= $subscriberCount ?></p>
                                    <a href="<?= url('subscribers') ?>" class="btn btn-light btn-sm">View All</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Active Plans</h5>
                                    <p class="card-text display-4"><?= $activePlansCount ?></p>
                                    <a href="<?= url('plans') ?>" class="btn btn-light btn-sm">View All</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Statements</h5>
                                    <p class="card-text display-4"><?= $statementsCount ?></p>
                                    <a href="<?= url('statements') ?>" class="btn btn-light btn-sm">View All</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5>Recent Payments</h5>
                                    <a href="<?= url('payments') ?>" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recentPayments)): ?>
                                        <p class="text-muted">No recent payments found.</p>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>OR #</th>
                                                        <th>Subscriber</th>
                                                        <th>Amount</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recentPayments as $payment): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?= url('payments/view?id=' . $payment['payment_id']) ?>">
                                                                    <?= htmlspecialchars($payment['or_no']) ?>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($payment['company_name'])): ?>
                                                                    <?= htmlspecialchars($payment['company_name']) ?>
                                                                <?php else: ?>
                                                                    <?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>$<?= number_format($payment['paid_amount'], 2) ?></td>
                                                            <td><?= date('M d, Y', strtotime($payment['payment_date'])) ?></td>
                                                            <td>
                                                                <?php if ($payment['payment_status'] === 'Completed'): ?>
                                                                    <span class="badge bg-success">Completed</span>
                                                                <?php elseif ($payment['payment_status'] === 'Pending'): ?>
                                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary"><?= htmlspecialchars($payment['payment_status']) ?></span>
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
                                    <h5>Recent Statements</h5>
                                    <a href="<?= url('statements') ?>" class="btn btn-sm btn-outline-primary">View All</a>
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
                                                        <th>Subscriber</th>
                                                        <th>Amount</th>
                                                        <th>Due Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recentStatements as $statement): ?>
                                                        <tr>
                                                            <td>
                                                                <a href="<?= url('statements/view?id=' . $statement['statement_id']) ?>">
                                                                    <?= htmlspecialchars($statement['statement_no']) ?>
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($statement['company_name'])): ?>
                                                                    <?= htmlspecialchars($statement['company_name']) ?>
                                                                <?php else: ?>
                                                                    <?= htmlspecialchars($statement['first_name'] . ' ' . $statement['last_name']) ?>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>$<?= number_format($statement['total_amount'], 2) ?></td>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/foot.php' ?>