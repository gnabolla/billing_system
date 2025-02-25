<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= url('dashboard') ?>">Billing Platform</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= getURI('/dashboard') ? 'active' : '' ?>" href="<?= url('dashboard') ?>">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= getURI('/subscribers') ? 'active' : '' ?>" href="<?= url('subscribers') ?>">Subscribers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= getURI('/plans') ? 'active' : '' ?>" href="<?= url('plans') ?>">Plans</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= getURI('/statements') ? 'active' : '' ?>" href="<?= url('statements') ?>">Statements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= getURI('/payments') ? 'active' : '' ?>" href="<?= url('payments') ?>">Payments</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <?= htmlspecialchars($_SESSION['username']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= url('profile') ?>">Profile</a></li>
                        <li><a class="dropdown-item" href="<?= url('company-settings') ?>">Company Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= url('logout') ?>">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>