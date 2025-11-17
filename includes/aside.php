<!-- Sidebar -->
<aside class="sidebar p-3">
    <div>
        <h5 class="fw-bold text-white mb-4">Drew Enterprises</h5>
        <nav class="nav flex-column">
            <a href="home.php?tag=dash" class="nav-link active d-flex align-items-center">
                <i data-lucide="layout-dashboard" class="me-2"></i> Dashboard
            </a>
            <a href="home.php?tag=proplist" class="nav-link d-flex align-items-center">
                <i data-lucide="home" class="me-2"></i> Properties
            </a>
            <a href="home.php?tag=unitlist" class="nav-link d-flex align-items-center">
                <i data-lucide="layers" class="me-2"></i> Units
            </a>
            <a href="home.php?tag=tntlist" class="nav-link d-flex align-items-center">
                <i data-lucide="users" class="me-2"></i> Tenants
            </a>
            <a href="home.php?tag=pay" class="nav-link d-flex align-items-center">
                <i data-lucide="credit-card" class="me-2"></i> Payments
            </a>
            <a href="home.php?tag=clientslist" class="nav-link d-flex align-items-center">
                <i data-lucide="credit-card" class="me-2"></i> Clients
            </a>
            <a href="home.php?tag=tentlist" class="nav-link d-flex align-items-center">
                <i data-lucide="tent" class="me-2"></i> Tents Management 
            </a>
            <a href="#" class="nav-link d-flex align-items-center">
                <i data-lucide="bar-chart-2" class="me-2"></i> Reports
            </a>
        </nav>

        <!-- Logged-in User Section -->
        <div class="user-info mt-4 d-flex align-items-center">
            <i data-lucide="user-circle-2" class="me-2"></i>
            <div>
                <h6 class="text-white fw-semibold">
                    <?= isset($logged_in_user['Full_Name']) ? htmlspecialchars($logged_in_user['Full_Name']) : 'User' ?>
                </h6>
                <small class="text-light opacity-75">
                    <?= isset($logged_in_user['Role_ID']) ? 'Role ID: ' . htmlspecialchars($logged_in_user['Role_ID']) : '' ?>
                </small>
            </div>
        </div>

        <!-- Settings -->
        <div class="mt-auto pt-3 border-top border-light">
            <a href="home.php?tag=set" class="nav-link text-light d-flex align-items-center">
                <i data-lucide="settings" class="me-2"></i> Settings
            </a>
        </div>
    </div>
</aside>
