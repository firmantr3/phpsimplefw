<?php
    if(!defined("_DEBUG_MODE_")) die("Not Allowed!");
    
    class halo extends SF_Controller {
        // kode disini:
        public function index() {
            $this->view("halo_view");
        }
    }
?>