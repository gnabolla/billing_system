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
                    <h3>Edit Subscriber</h3>
                    <div>
                        <a href="<?= url('subscribers/view?id=' . $subscriberId) ?>" class="btn btn-info me-2">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                        <a href="<?= url('subscribers') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= url('subscribers/edit?id=' . $subscriberId) ?>">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="account_no" class="form-label">Account Number</label>
                                <input type="text" class="form-control <?= isset($errors['account_no']) ? 'is-invalid' : '' ?>" 
                                    id="account_no" name="account_no" value="<?= htmlspecialchars($formData['account_no']) ?>">
                                <?php if (isset($errors['account_no'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['account_no']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="registration_date" class="form-label">Registration Date</label>
                                <input type="date" class="form-control <?= isset($errors['registration_date']) ? 'is-invalid' : '' ?>" 
                                    id="registration_date" name="registration_date" value="<?= htmlspecialchars($formData['registration_date']) ?>">
                                <?php if (isset($errors['registration_date'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['registration_date']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company/Business Name</label>
                                <input type="text" class="form-control <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>" 
                                    id="company_name" name="company_name" value="<?= htmlspecialchars($formData['company_name']) ?>">
                                <?php if (isset($errors['company_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['company_name']) ?>
                                    </div>
                                <?php endif; ?>
                                <div class="form-text">Required if no individual name is provided</div>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" 
                                    id="status" name="status">
                                    <option value="Active" <?= $formData['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                                    <option value="Inactive" <?= $formData['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                                    <option value="Suspended" <?= $formData['status'] === 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                                </select>
                                <?php if (isset($errors['status'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['status']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                                    id="first_name" name="first_name" value="<?= htmlspecialchars($formData['first_name']) ?>">
                                <?php if (isset($errors['first_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['first_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control <?= isset($errors['middle_name']) ? 'is-invalid' : '' ?>" 
                                    id="middle_name" name="middle_name" value="<?= htmlspecialchars($formData['middle_name']) ?>">
                                <?php if (isset($errors['middle_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['middle_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                                    id="last_name" name="last_name" value="<?= htmlspecialchars($formData['last_name']) ?>">
                                <?php if (isset($errors['last_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['last_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" 
                                id="address" name="address" rows="2"><?= htmlspecialchars($formData['address']) ?></textarea>
                            <?php if (isset($errors['address'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['address']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control <?= isset($errors['phone_number']) ? 'is-invalid' : '' ?>" 
                                    id="phone_number" name="phone_number" value="<?= htmlspecialchars($formData['phone_number']) ?>">
                                <?php if (isset($errors['phone_number'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['phone_number']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                    id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>">
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['email']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?= url('subscribers') ?>" class="btn btn-outline-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Subscriber</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include  __DIR__ . '/../partials/foot.php' ?>