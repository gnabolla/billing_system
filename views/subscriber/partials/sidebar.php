<div class="list-group mb-4">
    <a href="<?= url('subscriber/dashboard') ?>" class="list-group-item list-group-item-action <?= getURI('/subscriber/dashboard') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>My Account</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="<?= url('subscriber/profile') ?>" class="list-group-item list-group-item-action <?= getURI('/subscriber/profile') ? 'active' : '' ?>">
            <i class="bi bi-person"></i> Profile
        </a>
        <a href="<?= url('subscriber/plans') ?>" class="list-group-item list-group-item-action <?= getURI('/subscriber/plans') ? 'active' : '' ?>">
            <i class="bi bi-list-check"></i> My Plans
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Billing</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="<?= url('subscriber/statements') ?>" class="list-group-item list-group-item-action <?= getURI('/subscriber/statements') ? 'active' : '' ?>">
            <i class="bi bi-file-text"></i> Statements
        </a>
        <a href="<?= url('subscriber/payments') ?>" class="list-group-item list-group-item-action <?= getURI('/subscriber/payments') ? 'active' : '' ?>">
            <i class="bi bi-cash"></i> Payment History
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Support</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="#" class="list-group-item list-group-item-action">
            <i class="bi bi-question-circle"></i> Help Center
        </a>
        <a href="#" class="list-group-item list-group-item-action">
            <i class="bi bi-headset"></i> Contact Support
        </a>
    </div>
</div>