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
                    <h3>Terminate Plan</h3>
                    <a href="<?= url('subscribers/view?id=' . $subscriberId) ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Subscriber
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-4">
                        <h5><i class="bi bi-exclamation-triangle-fill"></i> Warning</h5>
                        <p>You are about to terminate the following plan:</p>
                        <ul>
                            <li><strong>Plan:</strong> <?= htmlspecialchars($subscriberPlan['plan_name']) ?></li>
                            <li><strong>Monthly Fee:</strong> $<?= number_format($subscriberPlan['monthly_fee'], 2) ?></li>
                            <li><strong>Start Date:</strong> <?= date('F d, Y', strtotime($subscriberPlan['start_date'])) ?></li>
                        </ul>
                        <p>This action will mark the plan as terminated and set an end date. It cannot be undone.</p>
                    </div>
                    
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= url('subscriber-plans/terminate?id=' . $subscriberPlan['subscriber_plan_id']) ?>">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Termination Date *</label>
                            <input type="date" class="form-control <?= isset($errors['end_date']) ? 'is-invalid' : '' ?>" 
                                id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required>
                            <?php if (isset($errors['end_date'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['end_date']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">The date when this plan will end. Defaults to today.</div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= url('subscribers/view?id=' . $subscriberId) ?>" class="btn btn-outline-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-danger">Terminate Plan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include  __DIR__ . '/../partials/foot.php' ?>