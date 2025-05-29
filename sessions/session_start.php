<?php
//demarage de la sessions
if (session_status() === PHP_SESSION_NONE) {
    // La session n'est pas active, démarre une nouvelle session
    session_start();
}
    
?>