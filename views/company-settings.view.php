<?php include 'partials/head.php' ?>
<?php include 'partials/nav.php' ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-3">
            <?php include 'partials/sidebar.php' ?>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3>Company Settings</h3>
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
                    
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?= url('company-settings') ?>">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="company_name" class="form-label">Company Name *</label>
                                <input type="text" class="form-control <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>" 
                                    id="company_name" name="company_name" value="<?= htmlspecialchars($formData['company_name']) ?>" required>
                                <?php if (isset($errors['company_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['company_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control <?= isset($errors['contact_person']) ? 'is-invalid' : '' ?>" 
                                    id="contact_person" name="contact_person" value="<?= htmlspecialchars($formData['contact_person']) ?>">
                                <?php if (isset($errors['contact_person'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['contact_person']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label">Contact Email *</label>
                                <input type="email" class="form-control <?= isset($errors['contact_email']) ? 'is-invalid' : '' ?>" 
                                    id="contact_email" name="contact_email" value="<?= htmlspecialchars($formData['contact_email']) ?>" required>
                                <?php if (isset($errors['contact_email'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['contact_email']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label">Contact Phone</label>
                                <input type="text" class="form-control <?= isset($errors['contact_phone']) ? 'is-invalid' : '' ?>" 
                                    id="contact_phone" name="contact_phone" value="<?= htmlspecialchars($formData['contact_phone']) ?>">
                                <?php if (isset($errors['contact_phone'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['contact_phone']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>" 
                                id="address" name="address" rows="3"><?= htmlspecialchars($formData['address'] ?? '') ?></textarea>
                            <?php if (isset($errors['address'])): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($errors['address']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Subscription Status</label>
                                <p class="form-control-plaintext">
                                    <?php if ($company['subscription_status'] === 'Active'): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($company['subscription_status']) ?></span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subscription Plan</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($company['subscription_plan']) ?></p>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Company Preferences Section (placeholder) -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Company Preferences</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="mb-3">Invoice Settings</h5>
                            <div class="mb-3">
                                <label class="form-label">Default Due Date</label>
                                <select class="form-select" disabled>
                                    <option selected>15 days after statement date</option>
                                    <option>30 days after statement date</option>
                                    <option>End of month</option>
                                </select>
                                <div class="form-text">Coming soon - Set the default due date for new statements</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="mb-3">Email Settings</h5>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="sendInvoiceEmail" disabled>
                                <label class="form-check-label" for="sendInvoiceEmail">Send statement emails automatically</label>
                                <div class="form-text">Coming soon - Automatically email statements to subscribers</div>
                            </div>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="sendPaymentReminders" disabled>
                                <label class="form-check-label" for="sendPaymentReminders">Send payment reminders</label>
                                <div class="form-text">Coming soon - Send reminders for upcoming and overdue payments</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill"></i> Additional company preferences will be available in a future update.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/foot.php' ?>