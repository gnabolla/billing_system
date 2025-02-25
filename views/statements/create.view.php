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
                    <h3>Create New Statement</h3>
                    <a href="<?= url('statements') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Statements
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= url('statements/create' . ($subscriberId ? '?subscriber_id=' . $subscriberId : '')) ?>" id="statementForm">
                        <!-- Subscriber Selection Section -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="subscriber_id" class="form-label">Subscriber *</label>
                                
                                <?php if ($subscriber): ?>
                                    <input type="hidden" name="subscriber_id" value="<?= $subscriber['subscriber_id'] ?>">
                                    <div class="alert alert-info">
                                        <strong>Selected Subscriber:</strong> 
                                        <?php if (!empty($subscriber['company_name'])): ?>
                                            <?= htmlspecialchars($subscriber['company_name']) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($subscriber['first_name'] . ' ' . $subscriber['last_name']) ?>
                                        <?php endif; ?>
                                        <br>
                                        <strong>Account No:</strong> <?= htmlspecialchars($subscriber['account_no']) ?>
                                    </div>
                                <?php else: ?>
                                    <select class="form-select <?= isset($errors['subscriber_id']) ? 'is-invalid' : '' ?>" 
                                        id="subscriber_id" name="subscriber_id" required>
                                        <option value="">-- Select Subscriber --</option>
                                        <?php foreach ($subscribers as $sub): ?>
                                            <option value="<?= $sub['subscriber_id'] ?>" <?= $formData['subscriber_id'] == $sub['subscriber_id'] ? 'selected' : '' ?>>
                                                <?php if (!empty($sub['company_name'])): ?>
                                                    <?= htmlspecialchars($sub['company_name']) ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']) ?>
                                                <?php endif; ?>
                                                (<?= htmlspecialchars($sub['account_no']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($errors['subscriber_id'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['subscriber_id']) ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Statement Details Section -->
                        <h5 class="mb-3">Statement Details</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="statement_no" class="form-label">Statement No.</label>
                                <input type="text" class="form-control <?= isset($errors['statement_no']) ? 'is-invalid' : '' ?>" 
                                    id="statement_no" name="statement_no" value="<?= htmlspecialchars($formData['statement_no']) ?>" 
                                    placeholder="Leave blank for auto-generated">
                                <?php if (isset($errors['statement_no'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['statement_no']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">Leave blank to auto-generate statement number</div>
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label">Due Date *</label>
                                <input type="date" class="form-control <?= isset($errors['due_date']) ? 'is-invalid' : '' ?>" 
                                    id="due_date" name="due_date" value="<?= htmlspecialchars($formData['due_date']) ?>" required>
                                <?php if (isset($errors['due_date'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['due_date']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bill_period_start" class="form-label">Bill Period Start *</label>
                                <input type="date" class="form-control <?= isset($errors['bill_period_start']) ? 'is-invalid' : '' ?>" 
                                    id="bill_period_start" name="bill_period_start" value="<?= htmlspecialchars($formData['bill_period_start']) ?>" required>
                                <?php if (isset($errors['bill_period_start'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['bill_period_start']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="bill_period_end" class="form-label">Bill Period End *</label>
                                <input type="date" class="form-control <?= isset($errors['bill_period_end']) ? 'is-invalid' : '' ?>" 
                                    id="bill_period_end" name="bill_period_end" value="<?= htmlspecialchars($formData['bill_period_end']) ?>" required>
                                <?php if (isset($errors['bill_period_end'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['bill_period_end']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Line Items Section -->
                        <h5 class="mb-3 d-flex justify-content-between">
                            <span>Statement Items</span>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn">
                                <i class="bi bi-plus-circle"></i> Add Item
                            </button>
                        </h5>
                        
                        <?php if (isset($errors['items'])): ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($errors['items']) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="table-responsive mb-3">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 35%">Description</th>
                                        <th style="width: 15%">Amount</th>
                                        <th style="width: 10%">Tax Rate %</th>
                                        <th style="width: 15%">Tax Amount</th>
                                        <th style="width: 15%">Total</th>
                                        <th style="width: 10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($subscriberPlans)): ?>
                                        <?php foreach ($subscriberPlans as $index => $plan): ?>
                                            <tr class="item-row">
                                                <td>
                                                    <input type="text" class="form-control" name="description[]" 
                                                        value="<?= htmlspecialchars($plan['plan_name']) ?> (<?= date('M d, Y', strtotime($plan['start_date'])) ?> - <?= date('M d, Y', strtotime($formData['bill_period_end'])) ?>)" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control amount-input" name="amount[]" 
                                                        value="<?= htmlspecialchars($plan['monthly_fee']) ?>" step="0.01" min="0" required>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control tax-rate-input" name="tax_rate[]" 
                                                        value="0" step="0.01" min="0">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control tax-amount-input" name="tax_amount_item[]" 
                                                        value="0" step="0.01" min="0" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control total-amount-input" name="total_amount_item[]" 
                                                        value="<?= htmlspecialchars($plan['monthly_fee']) ?>" step="0.01" min="0" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="item-row">
                                            <td>
                                                <input type="text" class="form-control" name="description[]" value="" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control amount-input" name="amount[]" 
                                                    value="0" step="0.01" min="0" required>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control tax-rate-input" name="tax_rate[]" 
                                                    value="0" step="0.01" min="0">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control tax-amount-input" name="tax_amount_item[]" 
                                                    value="0" step="0.01" min="0" readonly>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control total-amount-input" name="total_amount_item[]" 
                                                    value="0" step="0.01" min="0" readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Subtotal:</th>
                                        <th id="subtotal">$0.00</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Tax Total:</th>
                                        <th id="tax-total">$0.00</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Grand Total:</th>
                                        <th id="grand-total">$0.00</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?= htmlspecialchars($formData['notes']) ?></textarea>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= url('statements') ?>" class="btn btn-outline-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Statement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Template for new item row
        const newItemRow = `
            <tr class="item-row">
                <td>
                    <input type="text" class="form-control" name="description[]" required>
                </td>
                <td>
                    <input type="number" class="form-control amount-input" name="amount[]" 
                        value="0" step="0.01" min="0" required>
                </td>
                <td>
                    <input type="number" class="form-control tax-rate-input" name="tax_rate[]" 
                        value="0" step="0.01" min="0">
                </td>
                <td>
                    <input type="number" class="form-control tax-amount-input" name="tax_amount_item[]" 
                        value="0" step="0.01" min="0" readonly>
                </td>
                <td>
                    <input type="number" class="form-control total-amount-input" name="total_amount_item[]" 
                        value="0" step="0.01" min="0" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        // Add item button click handler
        document.getElementById('addItemBtn').addEventListener('click', function() {
            const tbody = document.querySelector('#itemsTable tbody');
            tbody.insertAdjacentHTML('beforeend', newItemRow);
            bindEvents();
            updateTotals();
        });
        
        // Initial binding of event handlers
        bindEvents();
        updateTotals();
        
        // Function to bind event handlers to dynamic elements
        function bindEvents() {
            // Handle remove item button
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    if (document.querySelectorAll('.item-row').length > 1) {
                        this.closest('tr').remove();
                        updateTotals();
                    } else {
                        alert('You must have at least one item');
                    }
                });
            });
            
            // Handle change events for amount and tax rate
            document.querySelectorAll('.amount-input, .tax-rate-input').forEach(input => {
                input.addEventListener('input', function() {
                    updateRowTotal(this.closest('tr'));
                    updateTotals();
                });
            });
        }
        
        // Function to update a single row's totals
        function updateRowTotal(row) {
            const amount = parseFloat(row.querySelector('.amount-input').value) || 0;
            const taxRate = parseFloat(row.querySelector('.tax-rate-input').value) || 0;
            const taxAmount = amount * (taxRate / 100);
            const total = amount + taxAmount;
            
            row.querySelector('.tax-amount-input').value = taxAmount.toFixed(2);
            row.querySelector('.total-amount-input').value = total.toFixed(2);
        }
        
        // Function to update all totals
        function updateTotals() {
            let subtotal = 0;
            let taxTotal = 0;
            let grandTotal = 0;
            
            document.querySelectorAll('.item-row').forEach(row => {
                subtotal += parseFloat(row.querySelector('.amount-input').value) || 0;
                taxTotal += parseFloat(row.querySelector('.tax-amount-input').value) || 0;
            });
            
            grandTotal = subtotal + taxTotal;
            
            document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
            document.getElementById('tax-total').textContent = '$' + taxTotal.toFixed(2);
            document.getElementById('grand-total').textContent = '$' + grandTotal.toFixed(2);
        }
    });
</script>

<?php include  __DIR__ . '/../partials/foot.php' ?>