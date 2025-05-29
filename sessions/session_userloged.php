<?php
require_once 'session_start.php';
$session_auth_sidebalocataire = ['personnel', "admin","medecin"];

if ( (isset($_SESSION['evaans_users_auth']) && is_numeric($_SESSION['evaans_users_auth'])) && (!empty($_SESSION['evaans_users_auth_type']) and !is_numeric($_SESSION['evaans_users_auth_type']))) {
    if(strtolower(in_array($_SESSION['evaans_users_auth_type']['type_user'],$session_auth_sidebalocataire))){
        header('Location: lantern/');
    }
}