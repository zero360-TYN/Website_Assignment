<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- import Css-->
    <?php
    if (isset($_mainCssFileName) && !empty($_mainCssFileName)) {
        if (is_array($_mainCssFileName)) {
            foreach($_mainCssFileName as $cssName){
                echo '<link rel="stylesheet" href="/css/'.$cssName.'.css">';
            }
        } else {
            echo '<link rel="stylesheet" href="/css/'.$_mainCssFileName.'.css">';
        }
    }
    ?>
    <link rel="stylesheet" href="/css/header.css">
    <link rel="stylesheet" href="/css/body.css">
    <link rel="stylesheet" href="/css/footer.css">
    <!-- title -->
    <title>POP Mart | <?php echo $_title; ?></title>
</head>

<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>
    
    <header class="header">
        <a href="/index.php">
            <img src="/img/cematrix.png" class="logo" alt="Logo">
        </a>


        <form action="/search.php" method="get" class="search-form" id='Search'>
            <input type="search" name="keyword" class="search-input" placeholder="Search">
            <button type="submit" class="search-btn">
                <img src="/img/icon/searchicon.png" class="search-icon">
            </button>
        </form>

        <div class="header-right">
            <img src="/img/icon/my-wish-list.png" class="icon-wishlist">

            <a href="/shoppingCart.php">
                <img src="/img/icon/shoppingCart.png" class="icon-shoppingcart">
            </a>

            <div class="user-menu-wrapper" style="display:inline-block; position:relative;">
                <?php if (!$_user): ?>
                    <a href="/user/login.php">
                        <img src="/img/icon/user.png" class="icon-user">
                    </a>
                <?php else: ?>
                    <img src="/img/icon/user.png" class="icon-user" id="userAvatar" style="cursor:pointer;">

                    <div id="userDropdown" class="dropdown-menu" style="display:none;">
                        <div class="menu-info">Hi, username</div>
                        <hr>
                        <a href="/user/profile.php">My Profile</a>
                        <a href="">My Orders</a>
                        <a href="/adminDashboard.php" style="color:red;">Admin Panel</a>
                        <hr>
                        <a href="/user/logout.php">Logout</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>