<?php
    function route_url() {
        //routing
        if(_HTACCESS_) {
            $_qstring = $_SERVER['SCRIPT_NAME'] != $_SERVER['PHP_SELF'] ? str_replace($_SERVER['SCRIPT_NAME'] . "/","",$_SERVER['PHP_SELF']) : "";
            $_tmp = $_qstring ? explode("/",$_qstring) : array();
            $_ctl = isset($_tmp[0]) ? $_tmp[0] : _DEFAULT_CONTROLLER_;
            $_sub = isset($_tmp[1]) && $_tmp[1] ? $_tmp[1] : "index";
        }
        else {
            $_ctl = isset($_getvar['folder']) ? $_getvar['folder'] : _DEFAULT_CONTROLLER_;
            $_sub = isset($_getvar['file']) ? $_getvar['file'] : "index";
            $_tmp = array();
        }
        
        return array(
            "path"      => "app/controller/$_ctl.php",
            "name"      => $_ctl,
            "sub"       => $_sub,
            "qstring"   => $_tmp
        );
    }
    
    function get_instance() {
        global $_sfcontroller;
        return $_sfcontroller;
    }
?>