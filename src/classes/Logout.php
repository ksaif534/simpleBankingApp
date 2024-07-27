<?php

namespace App\classes;

class Logout{
    public function logout(){
        session_start();
        unset($_SESSION);
        session_destroy();
        header("Location: index.php");
        exit;
    }
}

?>