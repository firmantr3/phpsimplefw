<?php
    /**
     * SIMPLE PHP FRAMEWORK
     * --------------------
     * author   : Firman T. Nugraha <firmantr3[at]gmail[dot]com>
     * License  : MIT
     */
    
    require("class.db.php");
    require("fn.global.php");
    require("functions.php");
    require("class.controller.php");
    
    $ctl_data = route_url();
    
    //proses routing & siapkan controller
    if(file_exists($ctl_data['path'])) {
        //load controller
        require($ctl_data['path']);
        $_sfcontroller = new $ctl_data['name']($ctl_data['qstring']);
        //get params
        $params = count($_sfcontroller->params) ? $_sfcontroller->params : array();
        $params = count($params) == 1 ? $params[0] : $params;
        //start session
        if(_SESSION_) {
            session_start();
        }
        //autoload helper
        if(defined("_AUTOLOAD_HELPER_") && _AUTOLOAD_HELPER_) {
            $_tmp = explode(",",_AUTOLOAD_HELPER_);
            foreach($_tmp as $val) {
                $_sfcontroller->helper($val);
            }
        }
        //load sub
        $_sfcontroller->{$ctl_data['sub']}($params);
    }
    else {
        die("404 Not Found!");
    }
?>