<?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    session_start();

    //return url path
    function base($path = '') {
        return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
    }

    //return local path(use for include)
    function root($path = '') {
        return "$_SERVER[DOCUMENT_ROOT]/$path";
    }

    //return to ? page
    function redirect($url = null){
        $url ??= $_SERVER['REQUEST_URI'];
        header("Location: $url");
        exit();
    }

    function is_post(){
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    function is_get(){
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    //get value if empty set the default value to second parameter
    function req($key, $value = null) {
        $value = $_REQUEST[$key] ?? $value;
        return is_array($value) ? array_map('trim', $value) : trim($value);
    }
    //show a flash message
    function temp($key, $value = null) {
        if ($value !== null) {
            $_SESSION["temp_$key"] = $value;
        }
        else {
            $value = $_SESSION["temp_$key"] ?? null;
            unset($_SESSION["temp_$key"]);
            return $value;
        }
    }
    
    //user
    $_user = $_SESSION['user'] ?? null;
    // Login user
    function login($user, $url = '/') {
        $_SESSION['user'] = $user;
        global $_user;
        $_user = $user; 

        $guest_cart = $_SESSION['cart'] ?? [];

        unset($_SESSION['cart']);
        $db_cart = get_cart();

        foreach ($guest_cart as $id => $quantity) {
            if (isset($db_cart[$id])) {
                $db_cart[$id] += $quantity;
            } else {
                $db_cart[$id] = $quantity;
            }
        }
        ksort($db_cart);

        set_cart($db_cart);
        redirect($url);
    }

    // Logout user
    function logout($url = '/') {
        unset($_SESSION['user']);
        unset($_SESSION['cart']);//clear session name cart
        redirect($url);
    }

    // Authorization
    function auth(...$roles) {
        global $_user;
        if ($_user) {
            if ($roles) {
                if (in_array($_user->role, $roles)) {
                    return; // OK
                }
                temp('info',"You not allow to access this page ! ");
                redirect('/');
            }
            else {
                return; // OK
            }
        }
        redirect('/user/login.php');
    }
//=========================================shopping cart======================================
    
    function get_cart() {
        global $_user, $_db;
        
        if (isset($_SESSION['cart'])) {
            return $_SESSION['cart'];
        }

        if ($_user) {
            $stm = $_db->prepare("SELECT product_id, quantity FROM cart WHERE user_id = ?");
            $stm->execute([$_user->user_id]);
            $cart = $stm->fetchAll(PDO::FETCH_KEY_PAIR);
            return $_SESSION['cart'] = $cart;
        }
        
        return $_SESSION['cart'] = [];
    }

    function set_cart($cart = []) {
        $_SESSION['cart'] = $cart;
        save_cart(); 
    }

    function get_quantity_from($id) {
        $cart = get_cart(); 
        return $cart[$id] ?? 0;
    }

    function add_cart($id, $unit = 1) {
        $cart = get_cart();

        if (is_exists($id, 'product', 'product_id') && $unit >= 1) {
            if (!isset($cart[$id])) {
                $cart[$id] = 0;
            }
            $cart[$id] += $unit; 
            ksort($cart);
        }

        set_cart($cart);
    }

    function update_cart($id, $unit) {
        $cart = get_cart();

        if (is_exists($id, 'product', 'product_id') && $unit >= 1) {
            $cart[$id] = $unit;
            ksort($cart);
        }
        else {
            unset($cart[$id]);
        }

        set_cart($cart);
    }

    function save_cart() {
        global $_user, $_db;
        if (!$_user) {
            return;
        }
        
        $user_id = $_user->user_id;
        $cart = $_SESSION['cart'] ?? []; 

        $del_stm = $_db->prepare("DELETE FROM cart WHERE user_id = ?");
        $del_stm->execute([$user_id]);

        if (empty($cart)) {
            return; 
        }

        $ins_stm = $_db->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        foreach ($cart as $product_id => $quantity) {
            $ins_stm->execute([$user_id, $product_id, $quantity]);
        }
    }
    //===============================wishlist=============================
    function is_in_wishlist($product_id) {
        global $_user, $_db;
        if (!$_user) return false;

        $stm = $_db->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stm->execute([$_user->user_id, $product_id]);
        return (bool)$stm->fetch();
    }

    function toggle_wishlist($product_id) {
        global $_user, $_db;
        auth();

        if (is_in_wishlist($product_id)) {
            $stm = $_db->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
            $stm->execute([$_user->user_id, $product_id]);
            return "removed";
        } else {
            $stm = $_db->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
            $stm->execute([$_user->user_id, $product_id]);
            return "added";
        }
    }

    function get_wishlist_items() {
        global $_user, $_db;
        if (!$_user) return [];

        $stm = $_db->prepare("
            SELECT p.* FROM product p
            JOIN wishlist w ON p.product_id = w.product_id
            WHERE w.user_id = ?
            ORDER BY w.added_date DESC
        ");
        $stm->execute([$_user->user_id]);
        return $stm->fetchAll();
    }

    // Global PDO object(PHP DATABASE object)
    $_db = new PDO('mysql:dbname=cematrixdb', 'root', '', [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);


    function is_exists($value, $table, $field) {
        global $_db;

        $safe_table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $safe_field = preg_replace('/[^a-zA-Z0-9_]/', '', $field);

        $stm = $_db->prepare("SELECT COUNT(*) FROM $safe_table WHERE $safe_field = ?");
        $stm->execute([$value]);

        return $stm->fetchColumn() > 0;
    }

    // Global error array
    $_err = [];

    // Generate <span class='err'>
    function err($key) {
        global $_err;
        if ($_err[$key] ?? false) {
            echo "<span class='err'>$_err[$key]</span>";
        }
        else {
            echo '<span></span>';
        }
    }
    //HTML HELPER
    function encode($value) {
        return htmlentities($value);
    }
    // Generate <input type='search'>
    function html_search($key, $attr = '') {
        $value = encode($GLOBALS[$key] ?? '');
        echo "<input type='search' id='$key' name='$key' value='$value' $attr>";
    }
?>