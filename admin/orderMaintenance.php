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
    if ($order_id): 
        global $_db;
        $order_stm = $_db->prepare("
            SELECT o.*, u.name AS user_name, u.email 
            FROM orders o 
            JOIN user u ON o.user_id = u.user_id 
            WHERE o.order_id = ?
        ");
        $order_stm->execute([$order_id]);
        $order = $order_stm->fetch();

        if (!$order) {
            echo "<p>Order not found.</p>";
            exit;
        }

        $item_stm = $_db->prepare("
            SELECT oi.*, p.name AS product_name, p.image 
            FROM order_item oi 
            JOIN product p ON oi.product_id = p.product_id 
            WHERE oi.order_id = ?
        ");
        $item_stm->execute([$order_id]);
        $items = $item_stm->fetchAll();
    ?>
        <div class="detail-container">
            <div class="detail-header">
                <h2>Order #<?= $order->order_id ?></h2>
                <a href="order_maintenance.php" class="btn-back">← Back to List</a>
            </div>
            
            <div class="customer-info-card">
                <h3>Customer Info</h3>
                <p><strong>Name:</strong> <?= htmlspecialchars($order->user_name ?? 'N/A') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order->email ?? 'N/A') ?></p>
                <p><strong>Order Date:</strong> <?= date('d M Y, H:i A', strtotime($order->order_date)) ?></p>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Name</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): 
                        $subtotal = $item->unit_price * $item->quantity;
                    ?>
                    <tr>
                        <td><img src="/img/product_img/<?= $item->image ?>" class="table-img"></td>
                        <td><?= htmlspecialchars($item->product_name) ?></td>
                        <td>RM <?= number_format($item->unit_price, 2) ?></td>
                        <td><?= $item->quantity ?></td>
                        <td class="font-bold">RM <?= number_format($subtotal, 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                        <td class="grand-total-amount">RM <?= number_format($order->total_amount, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    <?php 
    else: 
        global $_db;
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
                            <td class="font-bold text-danger">RM <?= number_format($o->total_amount, 2) ?></td>
                            <td>
                                <a href="orderDetail.php?id=<?= $o->order_id ?>" class="btn-action view">View Details</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</main>

<?php
include root('_footer.php');
?>