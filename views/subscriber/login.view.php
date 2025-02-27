<?php include __DIR__ . '/../partials/head.php' ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/foot.php' ?><div class="card-header bg-primary text-white">
                    </form>

                    <div class="mt-3 text-center">
                        <p>Forgot your password? <a href="<?= url('subscriber/forgot-password') ?>">Reset it here</a></p>
                        <hr>
                        <p>Back to <a href="<?= url('/') ?>">Main Website</a></p>
                    </div>
                <h3 class="text-center mb-0">Subscriber Login</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= url('subscriber/login') ?>">
                        <div class="mb-3">
                            <label for="account_no" class="form-label">Account Number</label>
                            <input type="text" class="form-control" id="account_no" name="account_no"
                                value="<?= htmlspecialchars($accountNo) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>