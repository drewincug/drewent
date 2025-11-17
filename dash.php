      
    <!-- Display logged-in user info -->
    <?php if ($logged_in_user): ?>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Welcome, <?= htmlspecialchars($logged_in_user['Full_Name']) ?>
            </div>
            <div class="card-body">
                <p><strong>Username:</strong> <?= htmlspecialchars($logged_in_user['Username']) ?></p>
                <p><strong>Role ID:</strong> <?= htmlspecialchars($logged_in_user['Role_ID']) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($logged_in_user['Status']) ?></p>
                <p><strong>Member Since:</strong> <?= htmlspecialchars($logged_in_user['Created_At']) ?></p>
            </div>
        </div>
    <?php endif; ?>



      <div class="container-fluid">
        <!-- Dashboard Summary Cards -->
        <div class="row g-4 mb-4">
          <div class="col-md-3">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <h6 class="text-muted">Total Properties</h6>
                <h4 class="fw-bold text-primary">32</h4>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <h6 class="text-muted">Total Units</h6>
                <h4 class="fw-bold text-success">128555555</h4>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <h6 class="text-muted">Active Tenants</h6>
                <h4 class="fw-bold text-info">95</h4>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <h6 class="text-muted">Monthly Revenue</h6>
                <h4 class="fw-bold text-warning">UGX 12.5M</h4>
              </div>
            </div>
          </div>
        </div>

        <!-- Chart Area -->
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <h6 class="fw-bold mb-3">Revenue Trend</h6>
            <canvas id="revenueChart" height="100"></canvas>
          </div>
        </div>
      </div> 