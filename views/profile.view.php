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
                    <h3>My Profile</h3>
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
                        <div class="col-md-3 text-center mb-4">
                            <div class="border rounded-circle p-3 mx-auto mb-3" style="width: 120px; height: 120px; background-color: #f8f9fa;">
                                <i class="bi bi-person" style="font-size: 5rem; color: #6c757d;"></i>
                            </div>
                            <h5><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                            <p class="text-muted"><?= ucfirst(htmlspecialchars($user['role'])) ?></p>
                        </div>
                        
                        <div class="col-md-9">
                            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Profile Info</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Change Password</button>
                                </li>
                            </ul>
                            
                            <div class="tab-content mt-3" id="profileTabsContent">
                                <!-- Profile Info Tab -->
                                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <?php if (isset($errors['general']) && !isset($_POST['change_password'])): ?>
                                        <div class="alert alert-danger">
                                            <?= htmlspecialchars($errors['general']) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="<?= url('profile') ?>">
                                        <input type="hidden" name="update_profile" value="1">
                                        
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
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                                id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required>
                                            <?php if (isset($errors['email'])): ?>
                                                <div class="invalid-feedback">
                                                    <?= htmlspecialchars($errors['email']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($user['username']) ?>" readonly>
                                            <div class="form-text">Username cannot be changed</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Last Login</label>
                                            <p class="form-control-plaintext">
                                                <?= !empty($user['last_login']) ? date('F d, Y g:i A', strtotime($user['last_login'])) : 'Never' ?>
                                            </p>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Change Password Tab -->
                                <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                                    <?php if (isset($errors['general']) && isset($_POST['change_password'])): ?>
                                        <div class="alert alert-danger">
                                            <?= htmlspecialchars($errors['general']) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($passwordUpdated): ?>
                                        <div class="alert alert-success">
                                            Password changed successfully!
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="<?= url('profile') ?>">
                                        <input type="hidden" name="change_password" value="1">
                                        
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password *</label>
                                            <input type="password" class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>" 
                                                id="current_password" name="current_password" required>
                                            <?php if (isset($errors['current_password'])): ?>
                                                <div class="invalid-feedback">
                                                    <?= htmlspecialchars($errors['current_password']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">New Password *</label>
                                            <input type="password" class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" 
                                                id="new_password" name="new_password" required>
                                            <?php if (isset($errors['new_password'])): ?>
                                                <div class="invalid-feedback">
                                                    <?= htmlspecialchars($errors['new_password']) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div class="form-text">Password must be at least 8 characters long</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                            <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                                id="confirm_password" name="confirm_password" required>
                                            <?php if (isset($errors['confirm_password'])): ?>
                                                <div class="invalid-feedback">
                                                    <?= htmlspecialchars($errors['confirm_password']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary">Change Password</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Account Activity Section -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Account Activity</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill"></i> Account activity tracking will be available in a future update.
                    </div>
                    
                    <p>This section will show your recent activity in the system, including:</p>
                    <ul>
                        <li>Login history</li>
                        <li>Records created or modified</li>
                        <li>Payment processing history</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/foot.php' ?>