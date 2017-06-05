<?php
    function cek_login() {
        session_start();
			if(!isset($_SESSION['login'])){
				redirect(_BASE_URL_ . "/login");
				exit;
        }
    }
?>