<?php
require '../_base.php';

auth();
$user_id = $_user->user_id;

//claim
if (is_post() && isset($_POST['action']) && $_POST['action'] === 'claim_voucher') {
    $code = trim($_POST['promo_code']);

    //check valid
    $check_v = $_db->prepare("SELECT * FROM voucher WHERE promo_code = ? AND is_active = 1");
    $check_v->execute([$code]);
    if (!$check_v->fetch()) {
        temp('info', 'Voucher is invalid or inactive.');
        redirect('voucher.php');
        exit;
    }

    //check is claimed or not
    $check_claim = $_db->prepare("SELECT * FROM voucher_handle WHERE user_id = ? AND promo_code = ?");
    $check_claim->execute([$user_id, $code]);
    if ($check_claim->fetch()) {
        temp('info', 'You have already claimed this voucher!');
        redirect('voucher.php');
        exit;
    }

    //insert data to voucher handle
    try {
        $ins = $_db->prepare("INSERT INTO voucher_handle (user_id, promo_code, is_used) VALUES (?, ?, 0)");
        $ins->execute([$user_id, $code]);
        temp('info', 'Voucher claimed successfully!');
    } catch (Exception $e) {
        temp('info', 'Failed to claim voucher.');
    }
    
    redirect('voucher.php');
    exit;
}

//show what vouchers user handle 
$sql = "
    SELECT 
        v.promo_code, 
        v.discount_price,
        vh.is_used,
        CASE WHEN vh.promo_code IS NOT NULL THEN 1 ELSE 0 END AS is_claimed
    FROM voucher v
    LEFT JOIN voucher_handle vh 
        ON v.promo_code = vh.promo_code AND vh.user_id = ?
    WHERE v.is_active = 1;
";
$stm = $_db->prepare($sql);
$stm->execute([$user_id]);
$vouchers = $stm->fetchAll();

$activeVouchers = [];
$usedVouchers = [];

foreach ($vouchers as $v) {
    if ($v->is_claimed && $v->is_used) {
        $usedVouchers[] = $v;
    } else {
        $activeVouchers[] = $v;
    }
}

$_title = 'Voucher Center';
$_mainCssFileName = 'voucher';
include root('_header.php');
?>
<main>
    <div class="voucher-header">
        <h2 class="voucher-title">🎟️ Voucher Center</h2>
        <button id="viewUsedBtn" class="btn-history">History (<?= count($usedVouchers) ?>)</button>
    </div>

    <?php if (empty($activeVouchers)): ?>
        <p style="text-align:center; color:#666;">No active vouchers available right now. Check back later!</p>
    <?php else: ?>
        <div class="voucher-grid">
            <?php foreach ($activeVouchers as $v): ?>
                <div class="voucher-card">
                    <div>
                        <h3 class="v-code"><?= htmlspecialchars($v->promo_code) ?></h3>
                        <p class="v-discount">RM <?= number_format($v->discount_price, 2) ?> OFF</p>
                    </div>

                    <?php if (!$v->is_claimed): ?>
                        <form method="POST">
                            <input type="hidden" name="action" value="claim_voucher">
                            <input type="hidden" name="promo_code" value="<?= htmlspecialchars($v->promo_code) ?>">
                            <button type="submit" class="btn-claim">Claim Now</button>
                        </form>
                    <?php elseif ($v->is_claimed && !$v->is_used): ?>
                        <a href="/product/shoppingCart.php" class="btn-use">Ready to Use</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div id="usedModal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <div class="modal-top">
                <h3>Used Vouchers</h3>
                <button id="closeModal" class="close-btn">✖</button>
            </div>
            <div class="modal-body">
                <?php if (empty($usedVouchers)): ?>
                    <p class="empty-msg">You haven't used any vouchers yet.</p>
                <?php else: ?>
                    <ul class="used-list">
                        <?php foreach ($usedVouchers as $u): ?>
                            <li>
                                <div class="used-info">
                                    <span class="u-code"><?= htmlspecialchars($u->promo_code) ?></span>
                                    <span class="u-price">-RM <?= number_format($u->discount_price, 2) ?></span>
                                </div>
                                <span class="u-status">Used</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
$_jsFileName = 'voucher';
include root('_footer.php');
?>