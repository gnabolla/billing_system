<div class="list-group mb-4">
    <a href="/dashboard" class="list-group-item list-group-item-action <?= getURI('/dashboard') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Subscribers</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="/subscribers" class="list-group-item list-group-item-action <?= getURI('/subscribers') ? 'active' : '' ?>">
            <i class="bi bi-people"></i> All Subscribers
        </a>
        <a href="/subscribers/create" class="list-group-item list-group-item-action <?= getURI('/subscribers/create') ? 'active' : '' ?>">
            <i class="bi bi-person-plus"></i> Add New
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Plans</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="/plans" class="list-group-item list-group-item-action <?= getURI('/plans') ? 'active' : '' ?>">
            <i class="bi bi-list-check"></i> All Plans
        </a>
        <a href="/plans/create" class="list-group-item list-group-item-action <?= getURI('/plans/create') ? 'active' : '' ?>">
            <i class="bi bi-plus-square"></i> Add New
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5>Billing</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="/statements" class="list-group-item list-group-item-action <?= getURI('/statements') ? 'active' : '' ?>">
            <i class="bi bi-file-text"></i> Statements
        </a>
        <a href="/statements/create" class="list-group-item list-group-item-action <?= getURI('/statements/create') ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-plus"></i> Create Statement
        </a>
        <a href="/payments" class="list-group-item list-group-item-action <?= getURI('/payments') ? 'active' : '' ?>">
            <i class="bi bi-cash"></i> Payments
        </a>
        <a href="/payments/create" class="list-group-item list-group-item-action <?= getURI('/payments/create') ? 'active' : '' ?>">
            <i class="bi bi-cash-coin"></i> Record Payment
        </a>
    </div>
</div>

<?php if ($_SESSION['role'] === 'admin'): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5>Administration</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="/users" class="list-group-item list-group-item-action <?= getURI('/users') ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i> Users
        </a>
        <a href="/company-settings" class="list-group-item list-group-item-action <?= getURI('/company-settings') ? 'active' : '' ?>">
            <i class="bi bi-gear"></i> Company Settings
        </a>
    </div>
</div>
<?php endif; ?>