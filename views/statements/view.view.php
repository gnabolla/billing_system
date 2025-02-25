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
                    <h3>Statement Details</h3>
                    <div>
                        <?php if ($statement['status'] !== 'Paid'): ?>
                            <a href="<?= url('payments/create?statement_id=' . $statement['statement_id']) ?>" class="btn btn-success me-2">
                                <i class="bi bi-cash"></i> Record Payment
                            </a>
                        <?php endif; ?>
                        <a href="<?= url('statements') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Statements
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
                            <h5 class="border-bottom pb-2">Statement Information</h5>
                            
                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Statement #</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext fw-bold"><?= htmlspecialchars($statement['statement_no']) ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Billing Period</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?= date('M d, Y', strtotime($statement['bill_period_start'])) ?> - 
                                        <?= date('M d, Y', strtotime($statement['bill_period_end'])) ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Due Date</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?= date('M d, Y', strtotime($statement['due_date'])) ?>
                                        <?php if (strtotime($statement['due_date']) < time() && $statement['status'] !== 'Paid'): ?>
                                            <span class="badge bg-danger ms-2">Overdue</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Status</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?php if ($statement['status'] === 'Paid'): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php elseif ($statement['status'] === 'Partially Paid'): ?>
                                            <span class="badge bg-warning text-dark">Partially Paid</span>
                                        <?php elseif ($statement['status'] === 'Overdue'): ?>
                                            <span class="badge bg-danger">Overdue</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Unpaid</span>
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
                                    <p class="form-control-plaintext"><?= htmlspecialchars($statement['account_no']) ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Name</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?php if (!empty($statement['company_name'])): ?>
                                            <?= htmlspecialchars($statement['company_name']) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($statement['first_name'] . ' ' . $statement['last_name']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Address</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext"><?= nl2br(htmlspecialchars($statement['address'])) ?></p>
                                </div>
                            </div>
                            
                            <div class="mb-2 row">
                                <label class="col-sm-4 col-form-label text-muted">Contact</label>
                                <div class="col-sm-8">
                                    <p class="form-control-plaintext">
                                        <?php if (!empty($statement['phone_number'])): ?>
                                            <?= htmlspecialchars($statement['phone_number']) ?><br>
                                        <?php endif; ?>
                                        <?php if (!empty($statement['email'])): ?>
                                            <?= htmlspecialchars($statement['email']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Statement Items</h5>
                            
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
                                        <?php if (empty($statementItems)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No items found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($statementItems as $item): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($item['description']) ?></td>
                                                    <td class="text-end">$<?= number_format($item['amount'], 2) ?></td>
                                                    <td class="text-end">$<?= number_format($item['tax_amount'], 2) ?></td>
                                                    <td class="text-end">$<?= number_format($item['total_amount'], 2) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Subtotal</th>
                                            <th class="text-end">$<?= number_format($statement['amount'], 2) ?></th>
                                        </tr>
                                        <?php if ($statement['tax_amount'] > 0): ?>
                                            <tr>
                                                <th colspan="3" class="text-end">Tax</th>
                                                <th class="text-end">$<?= number_format($statement['tax_amount'], 2) ?></th>
                                            </tr>
                                        <?php endif; ?>
                                        <?php if ($statement['discount_amount'] > 0): ?>
                                            <tr>
                                                <th colspan="3" class="text-end">Discount</th>
                                                <th class="text-end">-$<?= number_format($statement['discount_amount'], 2) ?></th>
                                            </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th colspan="3" class="text-end">Total</th>
                                            <th class="text-end">$<?= number_format($statement['total_amount'], 2) ?></th>
                                        </tr>
                                        <?php if ($statement['status'] !== 'Paid' && $statement['unpaid_amount'] < $statement['total_amount']): ?>
                                            <tr>
                                                <th colspan="3" class="text-end">Amount Paid</th>
                                                <th class="text-end">$<?= number_format($statement['total_amount'] - $statement['unpaid_amount'], 2) ?></th>
                                            </tr>
                                            <tr>
                                                <th colspan="3" class="text-end">Balance Due</th>
                                                <th class="text-end">$<?= number_format($statement['unpaid_amount'], 2) ?></th>
                                            </tr>
                                        <?php endif; ?>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($statement['notes'])): ?>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h5 class="border-bottom pb-2">Notes</h5>
                                <p><?= nl2br(htmlspecialchars($statement['notes'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2">Payment History</h5>
                            
                            <!-- Placeholder for payment history - will be implemented later -->
                            <p class="text-muted">Payment history will be available soon.</p>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="<?= url('statements') ?>" class="btn btn-outline-secondary me-md-2">Back to Statements</a>
                        <a href="#" class="btn btn-outline-primary me-md-2" onclick="window.print();">
                            <i class="bi bi-printer"></i> Print Statement
                        </a>
                        <?php if ($statement['status'] !== 'Paid'): ?>
                            <a href="<?= url('payments/create?statement_id=' . $statement['statement_id']) ?>" class="btn btn-success">
                                <i class="bi bi-cash"></i> Record Payment
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include  __DIR__ . '/../partials/foot.php' ?>