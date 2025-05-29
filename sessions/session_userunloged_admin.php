<?php
require_once 'session_start.php';
$session_auth_sidebalocataire = ['medecin', "admin","medecin"];

if ( (!isset($_SESSION['evaans_users_auth']) || !is_numeric($_SESSION['evaans_users_auth']) || empty($_SESSION['evaans_users_auth'])) && (empty($_SESSION['evaans_users_auth_type']['type_user']) || is_numeric($_SESSION['evaans_users_auth_type']['type_user']) || empty($_SESSION['evaans_users_auth_type']['auth']) || $_SESSION['evaans_users_auth_type']['auth'] != 'admin')) {
    header('Location: ../');
    exit;
}


?>