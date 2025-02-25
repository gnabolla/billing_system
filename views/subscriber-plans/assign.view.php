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
                    <h3>Assign Plan to Subscriber</h3>
                    <a href="<?= url('subscribers/view?id=' . $subscriber['subscriber_id']) ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Subscriber
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <strong>Subscriber:</strong> 
                        <?php if (!empty($subscriber['company_name'])): ?>
                            <?= htmlspecialchars($subscriber['company_name']) ?>
                        <?php else: ?>
                            <?= htmlspecialchars($subscriber['first_name'] . ' ' . $subscriber['last_name']) ?>
                        <?php endif; ?>
                        <br>
                        <strong>Account No:</strong> <?= htmlspecialchars($subscriber['account_no']) ?>
                    </div>
                    
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($plans)): ?>
                        <div class="alert alert-warning">
                            <p>No active plans available. Please <a href="<?= url('plans/create') ?>">create a plan</a> first.</p>
                        </div>
                        <?php else: ?>
                            <form method="POST" action="<?= isset($_GET['id']) ? url('subscribers/assign-plan?id=' . $subscriber['subscriber_id']) : url('subscriber-plans/assign?subscriber_id=' . $subscriber['subscriber_id']) ?>">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="plan_id" class="form-label">Select Plan *</label>
                                    <select class="form-select <?= isset($errors['plan_id']) ? 'is-invalid' : '' ?>" 
                                        id="plan_id" name="plan_id" required>
                                        <option value="">-- Select a plan --</option>
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
                                    <div class="form-text">Leave blank for ongoing subscription</div>
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
                                <a href="<?= url('subscribers/view?id=' . $subscriber['subscriber_id']) ?>" class="btn btn-outline-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Assign Plan</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include  __DIR__ . '/../partials/foot.php' ?>