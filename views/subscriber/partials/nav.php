<nav class="navbar navbar-expand-lg navbar-dark bg-subscriber">
    <div class="container">
        <a class="navbar-brand" href="<?= url('subscriber/dashboard') ?>">Subscriber Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= getURI('/subscriber/dashboard') ? 'active' : '' ?>" href="<?= url('subscriber/dashboard') ?>">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= getURI('/subscriber/statements') ? 'active' : '' ?>" href="<?= url('subscriber/statements') ?>">Statements</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= getURI('/subscriber/payments') ? 'active' : '' ?>" href="<?= url('subscriber/payments') ?>">Payments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= getURI('/subscriber/plans') ? 'active' : '' ?>" href="<?= url('subscriber/plans') ?>">My Plans</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <?= isset($_SESSION['subscriber_name']) ? htmlspecialchars($_SESSION['subscriber_name']) : 'Account' ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= url('subscriber/profile') ?>">My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= url('subscriber/logout') ?>">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>