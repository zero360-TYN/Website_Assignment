<?php
require '../_base.php'; 

auth('Admin'); 

$order_id = req('id');

$_title = 'Order Maintenance';
$_mainCssFileName = 'orderMaintenance';
include root('_header.php');
?>
<main>
    <div class="admin-breadcrumb">
        <a href="adminDashboard.php">Dashboard</a> 
        <span>/</span> 
        <?php if ($order_id): ?>
            <a href="order_maintenance.php">Orders</a>
            <span>/</span> Order #<?= htmlspecialchars($order_id) ?>
        <?php else: ?>
            <span>Order Maintenance</span>
        <?php endif; ?>
    </div>
    <?php
        $list_stm = $_db->query("
            SELECT o.*, u.name AS user_name 
            FROM orders o 
            JOIN user u ON o.user_id = u.user_id 
            ORDER BY o.order_date DESC
        ");
        $orders = $list_stm->fetchAll();
    ?>
        <div class="list-container">
            <h2 class="page-title">Order Maintenance</h2>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date & Time</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($orders)): ?>
                        <tr><td colspan="5" class="text-center">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><strong>#<?= $o->order_id ?></strong></td>
                            <td><?= date('d M Y, H:i', strtotime($o->order_date)) ?></td>
                            <td><?= htmlspecialchars($o->user_name ?? 'Unknown') ?></td>
                            <td class="font-bold text-danger">RM <?= number_format($o->grand_total, 2) ?></td>
                            <td>
                                <a href="orderDetail.php?id=<?= $o->order_id ?>" class="btn-action view">View Details</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

</main>

<?php
include root('_footer.php');
?>