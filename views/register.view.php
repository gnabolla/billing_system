<?php include 'partials/head.php' ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Register New Company</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($errors['general']) ?>
                            </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= url('register') ?>">
                        <h4>Company Information</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name *</label>
                                <input type="text" class="form-control <?= isset($errors['company_name']) ? 'is-invalid' : '' ?>" 
                                    id="company_name" name="company_name" value="<?= htmlspecialchars($formData['company_name']) ?>" required>
                                <?php if (isset($errors['company_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['company_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                    value="<?= htmlspecialchars($formData['contact_person']) ?>">
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
                                <input type="text" class="form-control" id="contact_phone" name="contact_phone" 
                                    value="<?= htmlspecialchars($formData['contact_phone']) ?>">
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h4>Admin Account</h4>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" 
                                    id="username" name="username" value="<?= htmlspecialchars($formData['username']) ?>" required>
                                <?php if (isset($errors['username'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['username']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                    id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required>
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['email']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                                    id="first_name" name="first_name" value="<?= htmlspecialchars($formData['first_name']) ?>" required>
                                <?php if (isset($errors['first_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['first_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                                    id="last_name" name="last_name" value="<?= htmlspecialchars($formData['last_name']) ?>" required>
                                <?php if (isset($errors['last_name'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['last_name']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                    id="password" name="password" required>
                                <?php if (isset($errors['password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                    id="confirm_password" name="confirm_password" required>
                                <?php if (isset($errors['confirm_password'])): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($errors['confirm_password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <p>Already have an account? <a href="/login">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/foot.php' ?>