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
        redirect($url);
    }

    // Logout user
    function logout($url = '/') {
        unset($_SESSION['user']);
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
        redirect('/login.php');
    }

    // Global PDO object(PHP DATABASE object)
    $_db = new PDO('mysql:dbname=popmartdb', 'root', '', [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);

    function req($key, $value = null) {
        $value = $_REQUEST[$key] ?? $value;
        return is_array($value) ? array_map('trim', $value) : trim($value);
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
?>