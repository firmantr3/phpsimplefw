<?php
    class SF_Controller {
        public $db;
        public $input;
        public $params;
        public $controller;
        
        private $_db_inited = 0;
        
        public function __construct($_qstring = array()) {
            //init konfig server
            date_default_timezone_set(_TIME_ZONE_);
            if(!_DEBUG_MODE_) {
                error_reporting(0);
            }
            
            //init db
            if(_DB_AUTOLOAD_) {
                $this->init_db();
            }
            
            //read input dari $_POST & $_GET
            $this->read_input();
            
            //get extra parameter
            $this->get_param($_qstring);
        }
        
        public function init_db() {
            if(!$this->_db_inited) {
                $this->db = new db(_DB_DRIVER_,_SERVER_,_DB_,_USER_,_PASS_);
            }
        }
        
        private function read_input() {
            $_postvar = isset($_POST) ? bersihkan($_POST) : array();
            $_getvar = isset($_GET) ? bersihkan($_GET) : array();
            
            $_input = array(
                "post" => $_postvar,
                "get" => $_getvar
            );
            
            $this->input = (object) $_input;
        }
        
        // buat nampilin view
        function view($view_path,$data = array()) {
            if(is_array($data)) {
                foreach($data as $key => $val) {
                    ${$key} = $val;
                }
            }
            
            $_vpath = "app/view/$view_path";
            $_not_found_msg = "404 not found!";
            
            if(file_exists($_vpath)) {
                if(is_dir($_vpath)) {
                    $_vpath .= "/index.php";
                    if(file_exists($_vpath)) {
                        require($_vpath);
                    }
                    else {
                        die($_not_found_msg);
                    }
                }
                else {
                    require("$_vpath.php");
                }
            }
            else {
                $_vpath .= ".php";
                if(file_exists($_vpath)) {
                    require($_vpath);
                }
                else {
                    die($_not_found_msg);
                }
            }
        }
        
        public function helper($helper_path) {
            $_hpath = "app/helper/$helper_path";
            $_not_found_msg = "helper:$helper_path ga bisa di load!";
            
            if(file_exists($_hpath)) {
                if(is_dir($_hpath)) {
                    $_hpath .= "/index.php";
                    if(file_exists($_hpath)) {
                        require($_hpath);
                    }
                    else {
                        die($_not_found_msg);
                    }
                }
                else {
                    require("$_hpath.php");
                }
            }
            else {
                $_hpath .= ".php";
                if(file_exists($_hpath)) {
                    require($_hpath);
                }
                else {
                    die($_not_found_msg);
                }
            }
        }
        
        public function get_param($q_string = array()) {
            $this->params = array();
            //ambil parameter yang ada
            if(_HTACCESS_) {
                foreach($q_string as $key => $val) {
                    if(!in_array($key,array(0,1))) {
                        if($key == 2) {
                            $_id = $val;
                        }
                        $this->params[] = $val;
                    }
                }
            }
            else {
                foreach($this->input->get as $key => $val) {
                    if(!in_array($key,array('folder','file'))) {
                        ${"_$key"} = $val;
                        $this->params[$key] = $val;
                    }
                }
            }
        }
    }
?>