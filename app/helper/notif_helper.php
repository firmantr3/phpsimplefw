<?php
    function set_notif($pesan,$jenis = 'success') {
        if(!isset($_SESSION)) {
            session_start();
        }
        
        if(!isset($_SESSION['notif'][$jenis])) {
            $_SESSION['notif'][$jenis] = array();
        }
        $_SESSION['notif'][$jenis][] = $pesan;
    }
    
    function get_notif($jenis = '') {
        if(!isset($_SESSION)) {
            session_start();
        }
        
        $jenis_alert = array(
            "success" => "success",
            "error" => "danger",
            "warning" => "warning"
        );
        
        if(isset($_SESSION['notif'])) {
            if($jenis) {
                foreach($_SESSION['notif'][$jenis] as $key => $val) {
                    echo "<p class=\"alert alert-" . $jenis_alert[$jenis] . " alert-dismissable\">$val <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button></p>";
                }
                $_SESSION['notif'][$jenis] = array();
            }
            else {
                foreach($_SESSION['notif'] as $key => $val) {
                    foreach($val as $val2) {
                        echo "<p class=\"alert alert-" . $jenis_alert[$key] . " alert-dismissable\">$val2 <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</button></p>";
                    }
                }
                unset($_SESSION['notif']);
            }
        }
    }
?>