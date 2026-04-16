<?php
    date_default_timezone_set('Asia/Kuala_Lumpur');
    
    $_db = new mysqli("localhost", "root", "", "cematrixdb");
    function getCount($_db, $sql) {
        $stmt = $_db->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'];
    }

?>