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
                    <h3>Payment Receipt</h3>
                    <div>
                        <a href="<?= url('statements/view?id=' . $payment['statement_id']) ?>" class="btn btn-info me-2">
                            <i class="bi bi-file-text"></i> View Statement
                        </a>
                        <a href="<?= url('payments') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Payments
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
                            <h5 class="border-bottom pb-2">Payment Information</h5>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">OR Number</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext fw-bold"><?= htmlspecialchars($payment['or_no']) ?></p>
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">OR Date</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= date('F d, Y', strtotime($payment['or_date'])) ?></p>
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Payment Date</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= date('F d, Y', strtotime($payment['payment_date'])) ?></p>
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Payment Method</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= htmlspecialchars($payment['payment_method']) ?></p>
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Status</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?php if ($payment['payment_status'] === 'Completed'): ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php elseif ($payment['payment_status'] === 'Pending'): ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php elseif ($payment['payment_status'] === 'Cancelled'): ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($payment['payment_status']) ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2">Subscriber Information</h5>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Account #</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= htmlspecialchars($payment['account_no']) ?></p>
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Name</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?php if (!empty($payment['company_name'])): ?>
                                            <?= htmlspecialchars($payment['company_name']) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Statement #</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= htmlspecialchars($payment['statement_no']) ?></p>
                                </div>
                            </div>

                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Statement Total</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">$<?= number_format($payment['statement_amount'], 2) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Payment Details</h5>

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted">Amount Paid:</p>
                                            <h3 class="text-success">$<?= number_format($payment['paid_amount'], 2) ?></h3>
                                        </div>
                                        <?php if ($payment['adv_payment'] > 0): ?>
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Advance Payment:</p>
                                                <h3>$<?= number_format($payment['adv_payment'], 2) ?></h3>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($payment['notes'])): ?>
                                <div class="mb-3">
                                    <h6>Notes:</h6>
                                    <p><?= nl2br(htmlspecialchars($payment['notes'])) ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($statement)): ?>
                                <div class="alert <?= $statement['unpaid_amount'] > 0 ? 'alert-warning' : 'alert-success' ?>">
                                    <p class="mb-1">
                                        <strong>Statement Status:</strong>
                                        <?php if ($statement['status'] === 'Paid'): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php elseif ($statement['status'] === 'Partially Paid'): ?>
                                            <span class="badge bg-warning text-dark">Partially Paid</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($statement['status']) ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Remaining Balance:</strong>
                                        $<?= number_format($statement['unpaid_amount'], 2) ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- In views/payments/view.view.php, add this to the buttons section -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="<?= url('payments') ?>" class="btn btn-outline-secondary me-md-2">Back to Payments</a>
                        <a href="<?= url('payments/receipt?id=' . $payment['payment_id']) ?>" class="btn btn-primary me-md-2">
                            <i class="bi bi-download"></i> Download Receipt
                        </a>
                        <a href="#" class="btn btn-outline-primary me-md-2" onclick="window.print();">
                            <i class="bi bi-printer"></i> Print Receipt
                        </a>
                        <?php if ($payment['payment_status'] === 'Pending'): ?>
                            <a href="<?= url('payments/complete?id=' . $payment['payment_id']) ?>" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Mark as Completed
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Statement Summary Card -->
            <?php if (!empty($statementItems)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5>Statement Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-end">Amount</th>
                                        <th class="text-end">Tax</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($statementItems as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['description']) ?></td>
                                            <td class="text-end">$<?= number_format($item['amount'], 2) ?></td>
                                            <td class="text-end">$<?= number_format($item['tax_amount'], 2) ?></td>
                                            <td class="text-end">$<?= number_format($item['total_amount'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Total</th>
                                        <th class="text-end">$<?= number_format($statement['total_amount'], 2) ?></th>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Amount Paid (This Payment)</th>
                                        <th class="text-end text-success">$<?= number_format($payment['paid_amount'], 2) ?></th>
                                    </tr>
                                    <?php if ($statement['unpaid_amount'] > 0): ?>
                                        <tr>
                                            <th colspan="3" class="text-end">Remaining Balance</th>
                                            <th class="text-end <?= $statement['unpaid_amount'] > 0 ? 'text-danger' : '' ?>">
                                                $<?= number_format($statement['unpaid_amount'], 2) ?>
                                            </th>
                                        </tr>
                                    <?php endif; ?>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include  __DIR__ . '/../partials/foot.php' ?>