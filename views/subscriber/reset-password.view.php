<?php include __DIR__ . '/../partials/head.php' ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center mb-0">Create New Password</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($success) ?>
                        </div>
                        <div class="text-center mt-4">
                            <a href="<?= url('subscriber/login') ?>" class="btn btn-primary">Go to Login</a>
                        </div>
                    <?php elseif ($validToken): ?>
                        <p class="mb-4">Please enter your new password below.</p>
                        
                        <form method="POST" action="<?= url('subscriber/reset-password?id=' . $subscriberId . '&token=' . $token) ?>">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       minlength="8" required>
                                <div class="form-text">Password must be at least 8 characters long.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                       minlength="8" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Reset Password</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center mt-3">
                            <a href="<?= url('subscriber/forgot-password') ?>" class="btn btn-primary">Request New Reset Link</a>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3 text-center">
                        <p>Back to <a href="<?= url('subscriber/login') ?>">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/foot.php' ?>