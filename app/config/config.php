<?php
    // SERVER DB CONFIG
    define("_SERVER_","localhost");
    define("_USER_","root");
    define("_PASS_","");
    define("_DB_","test");
    define("_DB_DRIVER_","mysql");
    define("_DB_AUTOLOAD_",false); // otomatis membuat $_db
    
    // APP CONFIG
    define("_BASE_PATH_","/public_html");
    define("_BASE_URL_","http://localhost");
    define("_DEBUG_MODE_",true); //produksi = false | develop = true
    define("_TIME_ZONE_","Asia/Jakarta");
    define("_SESSION_",false);
    
    // APP ROUTING
    define("_HTACCESS_",true);
    define("_DEFAULT_CONTROLLER_","halo");
    
    //APP AUTOLOAD
    define("_AUTOLOAD_HELPER_",""); //login_helper,abc_helper,xyz_helper
?>
