<?php include __DIR__ . '/../partials/head.php' ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center mb-0">Reset Your Password</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?= $success ?>
                        </div>
                    <?php else: ?>
                        <p class="mb-4">Enter your account number or email address below. We'll send you a link to reset your password.</p>
                        
                        <form method="POST" action="<?= url('subscriber/forgot-password') ?>">
                            <div class="mb-3">
                                <label for="account_no_or_email" class="form-label">Account Number or Email</label>
                                <input type="text" class="form-control" id="account_no_or_email" name="account_no_or_email"
                                    value="<?= htmlspecialchars($accountNoOrEmail) ?>" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Send Reset Link</button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <div class="mt-3 text-center">
                        <p>Remember your password? <a href="<?= url('subscriber/login') ?>">Back to Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/foot.php' ?>