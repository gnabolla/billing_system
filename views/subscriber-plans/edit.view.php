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
                    <h3>Edit Subscriber Plan</h3>
                    <a href="<?= url('subscribers/view?id=' . $subscriberPlan['subscriber_id']) ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Subscriber
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <strong>Current Plan:</strong> <?= htmlspecialchars($subscriberPlan['plan_name']) ?>
                        <br>
                        <strong>Monthly Fee:</strong> $<?= number_format($subscriberPlan['monthly_fee'], 2) ?>
                        <?php if (!empty($subscriberPlan['speed_rate'])): ?>
                            <br>
                            <strong>Speed Rate:</strong> <?= htmlspecialchars($subscriberPlan['speed_rate']) ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= url('subscriber-plans/edit?id=' . $subscriberPlanId) ?>">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="plan_id" class="form-label">Select Plan *</label>
                                <select class="form-select <?= isset($errors['plan_id']) ? 'is-invalid' : '' ?>" 
                                    id="plan_id" name="plan_id" required>
                                    <?php foreach ($plans as $plan): ?>
                                        <option value="<?= $plan['plan_id'] ?>" <?= $formData['plan_id'] == $plan['plan_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($plan['plan_name']) ?> - $<?= number_format($plan['monthly_fee'], 2) ?>
                                            <?= !empty($plan['speed_rate']) ? ' (' . htmlspecialchars($plan['speed_rate']) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['plan_id'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['plan_id']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" 
                                    id="status" name="status">
                                    <option value="Active" <?= $formData['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Pending" <?= $formData['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Terminated" <?= $formData['status'] === 'Terminated' ? 'selected' : '' ?>>Terminated</option>
                                    <option value="Expired" <?= $formData['status'] === 'Expired' ? 'selected' : '' ?>>Expired</option>
                                </select>
                                <?php if (isset($errors['status'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['status']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Start Date *</label>
                                <input type="date" class="form-control <?= isset($errors['start_date']) ? 'is-invalid' : '' ?>" 
                                    id="start_date" name="start_date" value="<?= htmlspecialchars($formData['start_date']) ?>" required>
                                <?php if (isset($errors['start_date'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['start_date']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control <?= isset($errors['end_date']) ? 'is-invalid' : '' ?>" 
                                    id="end_date" name="end_date" value="<?= htmlspecialchars($formData['end_date']) ?>">
                                <?php if (isset($errors['end_date'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['end_date']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">Required for non-active status</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control <?= isset($errors['notes']) ? 'is-invalid' : '' ?>" 
                                id="notes" name="notes" rows="3"><?= htmlspecialchars($formData['notes']) ?></textarea>
                            <?php if (isset($errors['notes'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['notes']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= url('subscribers/view?id=' . $subscriberPlan['subscriber_id']) ?>" class="btn btn-outline-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Show/hide end date field based on status
    document.getElementById('status').addEventListener('change', function() {
        const endDateField = document.getElementById('end_date');
        const endDateFormText = endDateField.nextElementSibling;
        
        if (this.value !== 'Active') {
            endDateField.setAttribute('required', 'required');
            endDateFormText.textContent = 'Required for non-active status';
        } else {
            endDateField.removeAttribute('required');
            endDateFormText.textContent = 'Leave blank for ongoing subscription';
        }
    });
    
    // Trigger the change event on page load
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('status').dispatchEvent(new Event('change'));
    });
</script>

<?php include  __DIR__ . '/../partials/foot.php' ?>