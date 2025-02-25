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
                    <h3>Edit Plan</h3>
                    <div>
                        <a href="<?= url('plans/view?id=' . $planId) ?>" class="btn btn-info me-2">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                        <a href="<?= url('plans') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Plans
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= url('plans/edit?id=' . $planId) ?>">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="plan_name" class="form-label">Plan Name *</label>
                                <input type="text" class="form-control <?= isset($errors['plan_name']) ? 'is-invalid' : '' ?>" 
                                    id="plan_name" name="plan_name" value="<?= htmlspecialchars($formData['plan_name']) ?>" required>
                                <?php if (isset($errors['plan_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['plan_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" 
                                    id="status" name="status">
                                    <option value="Active" <?= $formData['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= $formData['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                                <?php if (isset($errors['status'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['status']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="plan_description" class="form-label">Description</label>
                            <textarea class="form-control <?= isset($errors['plan_description']) ? 'is-invalid' : '' ?>" 
                                id="plan_description" name="plan_description" rows="3"><?= htmlspecialchars($formData['plan_description']) ?></textarea>
                            <?php if (isset($errors['plan_description'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['plan_description']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="monthly_fee" class="form-label">Monthly Fee *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" class="form-control <?= isset($errors['monthly_fee']) ? 'is-invalid' : '' ?>" 
                                        id="monthly_fee" name="monthly_fee" value="<?= htmlspecialchars($formData['monthly_fee']) ?>" required>
                                    <?php if (isset($errors['monthly_fee'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['monthly_fee']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="speed_rate" class="form-label">Speed Rate</label>
                                <input type="text" class="form-control <?= isset($errors['speed_rate']) ? 'is-invalid' : '' ?>" 
                                    id="speed_rate" name="speed_rate" value="<?= htmlspecialchars($formData['speed_rate']) ?>" placeholder="e.g. 50 Mbps">
                                <?php if (isset($errors['speed_rate'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['speed_rate']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="billing_cycle" class="form-label">Billing Cycle</label>
                                <select class="form-select <?= isset($errors['billing_cycle']) ? 'is-invalid' : '' ?>" 
                                    id="billing_cycle" name="billing_cycle">
                                    <option value="Monthly" <?= $formData['billing_cycle'] === 'Monthly' ? 'selected' : '' ?>>Monthly</option>
                                    <option value="Quarterly" <?= $formData['billing_cycle'] === 'Quarterly' ? 'selected' : '' ?>>Quarterly</option>
                                    <option value="Semi-Annual" <?= $formData['billing_cycle'] === 'Semi-Annual' ? 'selected' : '' ?>>Semi-Annual</option>
                                    <option value="Annual" <?= $formData['billing_cycle'] === 'Annual' ? 'selected' : '' ?>>Annual</option>
                                </select>
                                <?php if (isset($errors['billing_cycle'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['billing_cycle']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= url('plans') ?>" class="btn btn-outline-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include  __DIR__ . '/../partials/foot.php' ?>