<?php
require '../_base.php'; 


auth('Admin'); 

global $_user, $_db;

$product_count = $_db->query("SELECT COUNT(*) FROM product")->fetchColumn() ?: 0;
$order_count = $_db->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 0;
$member_count = $_db->query("SELECT COUNT(*) FROM user")->fetchColumn() ?: 0; 

$_title = 'Admin Dashboard';
$_mainCssFileName = 'adminDashboard'; 
include root('_header.php');
?>

<main>
    <header class="admin-header">
        <div>
            <h2 class="admin-title">Admin Dashboard</h2>
            <p class="admin-subtitle">Welcome back, <b><?= htmlspecialchars($_user->name ?? 'Administrator') ?></b>. Here's what's happening today.</p>
        </div>
        <a href="/user/logout.php" class="btn-logout">Logout</a>
    </header>

    <section class="stats-overview">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-info">
                <h3><?= $product_count ?></h3>
                <p>Total Products</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🛒</div>
            <div class="stat-info">
                <h3><?= $order_count ?></h3>
                <p>Total Orders</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
                <h3><?= $member_count ?></h3>
                <p>Registered Members</p>
            </div>
        </div>
    </section>

    <h3 class="section-heading">System Management</h3>
    <nav class="management-grid">
        <a href="product_maintenance.php" class="manage-card">
            <div class="manage-icon">🏷️</div>
            <div class="manage-text">
                <h4>Product Maintenance</h4>
                <p>Add, edit, or delete products and update inventory stock.</p>
            </div>
            <div class="manage-arrow">→</div>
        </a>

        <a href="/admin/orderMaintenance.php" class="manage-card">
            <div class="manage-icon">🧾</div>
            <div class="manage-text">
                <h4>Order Maintenance</h4>
                <p>View customer orders</p>
            </div>
            <div class="manage-arrow">→</div>
        </a>

        <a href="/admin/memberMaintenance.php" class="manage-card">
            <div class="manage-icon">🛡️</div>
            <div class="manage-text">
                <h4>Member Maintenance</h4>
                <p>Manage user accounts, reset passwords, and assign roles.</p>
            </div>
            <div class="manage-arrow">→</div>
        </a>
    </nav>
</main>

<?php
include root('_footer.php');
?>