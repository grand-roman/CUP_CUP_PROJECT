<?php
require_once("functions.php");


if(file_exists('config.php')) {
    require_once 'config.php';
} else {
    require_once 'config.default.php';
}

session_start();

$user_id = ($_SESSION['user']['id'] ?? NULL);


?>
