<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- import Css-->
        <?php 
            if (isset($_mainCssFileName) && !empty($_mainCssFileName)){
                echo '<link rel="stylesheet" href="css/'.$_mainCssFileName.'.css">';
            }
        ?>
        <link rel="stylesheet" href="css/header.css">
        <link rel ="stylesheet" href="css/body.css">
        <link rel ="stylesheet" href="css/footer.css">
        <!-- title -->
        <title>POP Mart | <?php echo $_title; ?></title>
    </head>
    <body>
        <header class="header">
            <img src="img/cematrix.png" class="logo" alt="Logo">

            <form action="search.php" method="get" class="search-form">
                <input type="search" name="keyword" class="search-input" placeholder="Search">
                <button type="submit" class="search-btn">
                    <img src="img/icon/searchicon.png" class="search-icon">
                </button>
            </form>
            <div class="header-right">
                <img src="img/icon/my-wish-list.png" class="icon-wishlist">
                <img src="img/icon/shoppingCart.png" class="icon-shoppingcart">
                <img src="img/icon/user.png" class="icon-user">
            </div>
        </header>