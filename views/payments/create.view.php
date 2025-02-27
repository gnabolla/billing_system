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
                    <h3>Record Payment</h3>
                    <a href="<?= url('payments') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Payments
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= url('payments/create' . ($statementId ? '?statement_id=' . $statementId : '')) ?>" id="paymentForm">
                        <!-- Statement Selection Section -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="statement_id" class="form-label">Statement *</label>

                                <?php if ($statement): ?>
                                    <input type="hidden" name="statement_id" value="<?= $statement['statement_id'] ?>">
                                    <div class="alert alert-info">
                                        <strong>Selected Statement:</strong> <?= htmlspecialchars($statement['statement_no']) ?><br>
                                        <strong>Subscriber:</strong>
                                        <?php if (!empty($statement['company_name'])): ?>
                                            <?= htmlspecialchars($statement['company_name']) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($statement['first_name'] . ' ' . $statement['last_name']) ?>
                                        <?php endif; ?>
                                        <br>
                                        <strong>Account No:</strong> <?= htmlspecialchars($statement['account_no']) ?><br>
                                        <strong>Total Amount:</strong> $<?= number_format($statement['total_amount'], 2) ?><br>
                                        <strong>Unpaid Amount:</strong> $<?= number_format($statement['unpaid_amount'], 2) ?>
                                    </div>
                                <?php else: ?>
                                    <select class="form-select <?= isset($errors['statement_id']) ? 'is-invalid' : '' ?>"
                                        id="statement_id" name="statement_id" required>
                                        <option value="">-- Select Statement --</option>
                                        <?php foreach ($unpaidStatements as $stmt): ?>
                                            <option value="<?= $stmt['statement_id'] ?>" <?= $formData['statement_id'] == $stmt['statement_id'] ? 'selected' : '' ?>
                                                data-unpaid="<?= $stmt['unpaid_amount'] ?>">
                                                <?= htmlspecialchars($stmt['statement_no']) ?> -
                                                <?php if (!empty($stmt['company_name'])): ?>
                                                    <?= htmlspecialchars($stmt['company_name']) ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($stmt['first_name'] . ' ' . $stmt['last_name']) ?>
                                                <?php endif; ?>
                                                (Due: $<?= number_format($stmt['unpaid_amount'], 2) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['statement_id'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['statement_id']) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Payment Details Section -->
                        <h5 class="mb-3">Payment Details</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="or_no" class="form-label">OR Number</label>
                                <input type="text" class="form-control <?= isset($errors['or_no']) ? 'is-invalid' : '' ?>"
                                    id="or_no" name="or_no" value="<?= htmlspecialchars($formData['or_no']) ?>"
                                    placeholder="Leave blank for auto-generated">
                                <?php if (isset($errors['or_no'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['or_no']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">Leave blank to auto-generate OR number</div>
                            </div>
                            <div class="col-md-6">
                                <label for="or_date" class="form-label">OR Date</label>
                                <input type="date" class="form-control <?= isset($errors['or_date']) ? 'is-invalid' : '' ?>"
                                    id="or_date" name="or_date" value="<?= htmlspecialchars($formData['or_date']) ?>">
                                <?php if (isset($errors['or_date'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['or_date']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="payment_date" class="form-label">Payment Date *</label>
                                <input type="date" class="form-control <?= isset($errors['payment_date']) ? 'is-invalid' : '' ?>"
                                    id="payment_date" name="payment_date" value="<?= htmlspecialchars($formData['payment_date']) ?>" required>
                                <?php if (isset($errors['payment_date'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['payment_date']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_method" class="form-label">Payment Method *</label>
                                <select class="form-select <?= isset($errors['payment_method']) ? 'is-invalid' : '' ?>"
                                    id="payment_method" name="payment_method" required>
                                    <option value="Cash" <?= $formData['payment_method'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
                                    <option value="Check" <?= $formData['payment_method'] === 'Check' ? 'selected' : '' ?>>Check</option>
                                    <option value="Bank Transfer" <?= $formData['payment_method'] === 'Bank Transfer' ? 'selected' : '' ?>>Bank Transfer</option>
                                    <option value="Credit Card" <?= $formData['payment_method'] === 'Credit Card' ? 'selected' : '' ?>>Credit Card</option>
                                    <option value="Online Payment" <?= $formData['payment_method'] === 'Online Payment' ? 'selected' : '' ?>>Online Payment</option>
                                </select>
                                <?php if (isset($errors['payment_method'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['payment_method']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="paid_amount" class="form-label">Amount Paid *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control <?= isset($errors['paid_amount']) ? 'is-invalid' : '' ?>"
                                        id="paid_amount" name="paid_amount" value="<?= htmlspecialchars($formData['paid_amount']) ?>" required>
                                    <?php if (isset($errors['paid_amount'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['paid_amount']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="adv_payment" class="form-label">Advance Payment</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control <?= isset($errors['adv_payment']) ? 'is-invalid' : '' ?>"
                                        id="adv_payment" name="adv_payment" value="<?= htmlspecialchars($formData['adv_payment']) ?>">
                                    <?php if (isset($errors['adv_payment'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['adv_payment']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-text">Optional: Amount paid in advance for future bills</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($formData['notes']) ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= url('payments') ?>" class="btn btn-outline-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-success">Record Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// Temporary error logging for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle statement selection to update paid amount
        const statementSelect = document.getElementById('statement_id');
        const paidAmountInput = document.getElementById('paid_amount');
        const paymentForm = document.getElementById('paymentForm');

        if (statementSelect) {
            statementSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const unpaidAmount = parseFloat(selectedOption.getAttribute('data-unpaid'));
                    if (!isNaN(unpaidAmount)) {
                        paidAmountInput.value = unpaidAmount.toFixed(2);
                    }
                } else {
                    paidAmountInput.value = '0.00';
                }
            });
        }

        // Form validation
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                const statementId = document.querySelector('input[name="statement_id"], select[name="statement_id"]').value;
                const paidAmount = parseFloat(paidAmountInput.value);

                if (!statementId) {
                    e.preventDefault();
                    alert('Please select a statement');
                    return false;
                }

                if (isNaN(paidAmount) || paidAmount <= 0) {
                    e.preventDefault();
                    alert('Please enter a valid payment amount greater than zero');
                    return false;
                }

                return true;
            });
        }
    });
</script>

<?php include  __DIR__ . '/../partials/foot.php' ?>