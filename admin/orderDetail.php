<?php
require '../_base.php'; 

auth('Admin'); 

$order_id = req('id');

if (!$order_id) {
    redirect('/admin/orderMaintenance.php');
    exit;
}

$order_stm = $_db->prepare("
    SELECT o.*, u.name AS user_name, u.email 
    FROM orders o 
    JOIN user u ON o.user_id = u.user_id 
    WHERE o.order_id = ?
");
$order_stm->execute([$order_id]);
$order = $order_stm->fetch();

if (!$order) {
    temp('info', 'Order not found.');
    redirect('order_maintenance.php');
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

$_title = 'Order Detail #' . $order->order_id;
$_mainCssFileName = 'orderMaintenanceDetail'; 
include root('_header.php');
?>

<main>
    <div class="admin-breadcrumb">
        <a href="/admin/adminDashboard.php">Dashboard</a> 
        <span>/</span> 
        <a href="/admin/orderMaintenance.php">Orders</a>
        <span>/</span> Order #<?= htmlspecialchars($order->order_id) ?>
    </div>

    <div>
        <div class="detail-header">
            <h2>Order Details (ID: #<?= $order->order_id ?>)</h2>
            <a href="/admin/orderMaintenance.php" class="btn-back">← Back to List</a>
        </div>
        
        <div class="customer-info-card">
            <h3>Customer Information</h3>
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
                    <td><img src="/img/product_img/<?= $item->image ?>" class="table-img" alt="Product"></td>
                    <td><?= htmlspecialchars($item->product_name) ?></td>
                    <td>RM <?= number_format($item->unit_price, 2) ?></td>
                    <td><?= $item->quantity ?></td>
                    <td class="font-bold">RM <?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>Total Amount:</strong></td>
                    <td>RM <?= number_format($order->total_amount, 2) ?></td>
                </tr>

                <?php if ($order->discount_amount > 0): ?>
                <tr class="discount-row">
                    <td colspan="4" class="text-right">
                        <strong>Discount (<?= htmlspecialchars($order->promo_code) ?>):</strong>
                    </td>
                    <td>- RM <?= number_format($order->discount_amount, 2) ?></td>
                </tr>
                <?php endif; ?>
                
                <tr class="grand-total-row">
                    <td colspan="4" class="text-right"><strong>Grand Total:</strong></td>
                    <td class="grand-total-amount">
                        RM <?= number_format($order->grand_total, 2) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</main>

<?php
include root('_footer.php');
?>