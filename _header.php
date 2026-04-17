<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    if (isset($_mainCssFileName) && !empty($_mainCssFileName)) {
        $cssFiles = is_array($_mainCssFileName) ? $_mainCssFileName : [$_mainCssFileName];
        foreach ($cssFiles as $cssName) {
            echo '<link rel="stylesheet" href="/css/' . $cssName . '.css">';
        }
    }
    ?>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/css/header.css">
    <link rel="stylesheet" href="/css/body.css">
    <link rel="stylesheet" href="/css/footer.css">
    <title>Cematrix | <?php echo $_title; ?></title>
</head>
<body>
    <div id="info"><?= temp('info') ?></div>

    <header class="header">
        <div class="header-left">
            <a href="/index.php">
                <img src="/img/cematrix.png" class="logo" alt="Logo">
            </a>
        </div>

        <form action="/product/search.php" method="get" class="search-form" id='Search'>
            <?php html_search('keyword','class="search-input" placeholder="Search product..."') ?>
            <button type="submit" class="search-btn">
                <img src="/img/icon/searchicon.png" class="search-icon">
            </button>
        </form>

        <div class="header-right">
            <a href="/user/voucher.php" title="Vouchers">
                <img src="/img/icon/voucher.png" class="icon-voucher">
            </a>
            <a href="/user/wishList.php" title="Wishlist">
                <img src="/img/icon/my-wish-list.png" class="icon-wishlist">
            </a>
            <a href="/product/shoppingCart.php" title="Cart">
                <img src="/img/icon/shoppingCart.png" class="icon-shoppingcart">
            </a>

            <div class="user-menu-wrapper">
                <?php if (!$_user): ?>
                    <img src="/img/icon/user.png" class="icon-user" id="userAvatar">
                    <div id="userDropdown" class="dropdown-menu">
                        <a href="/user/login.php">Log In</a>
                        <hr>
                        <a href="/user/register.php">Register</a>
                    </div>
                <?php else: ?>
                    <img src="/img/user_Icon/<?= $_user->photo ?>" class="icon-user" id="userAvatar">
                    <div id="userDropdown" class="dropdown-menu">
                        <div class="menu-info">Hi, <?= htmlspecialchars($_user->name) ?></div>
                        <hr>
                        <a href="/user/profile.php">My Profile</a>
                        <a href="/user/historyOrder.php">Orders</a>
                        <?php if ($_user->role == 'Admin'): ?>
                            <a href="/admin/adminDashboard.php" class="admin-link">Admin Panel</a>
                        <?php endif; ?>
                        <hr>
                        <a href="/user/logout.php">Logout</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>