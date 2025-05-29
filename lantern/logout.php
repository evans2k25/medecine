<?php
session_start();
$_SESSION['evaans_users_auth'] = "";
$_SESSION['evaans_users_auth_type']['auth'] = "";
$_SESSION['evaans_users_auth_type']['type_user'] = "";

header('Location: ../login.php');