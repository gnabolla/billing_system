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
                    <h3>Dashboard</h3>
                </div>
                <div class="card-body">
                    <h4>Welcome, <?= htmlspecialchars($user['first_name']) ?> <?= htmlspecialchars($user['last_name']) ?>!</h4>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Subscribers</h5>
                                    <p class="card-text display-4"><?= $subscriberCount ?></p>
                                    <a href="<?= url('subscribers') ?>" class="btn btn-light btn-sm">View All</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Active Plans</h5>
                                    <p class="card-text display-4"><?= $activePlansCount ?></p>
                                    <a href="<?= url('plans') ?>" class="btn btn-light btn-sm">View All</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Statements</h5>
                                    <p class="card-text display-4"><?= $statementsCount ?></p>
                                    <a href="<?= url('statements') ?>" class="btn btn-light btn-sm">View All</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Payments</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">No recent payments found.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Statements</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">No recent statements found.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/foot.php' ?>