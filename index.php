<?php

require_once 'layout/header.php';
require_once 'layout/left_menu.php';

$action = $_GET['action'] ?? 'main';

$path = "views/" . $action . ".php";

if (file_exists($path)) {
    require_once $path; 
} else {
    require_once 'views/main.php'; 
}

require_once 'layout/footer.php';
?>