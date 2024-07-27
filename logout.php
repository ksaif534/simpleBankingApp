<?php

require_once __DIR__.'/vendor/autoload.php';
use App\classes\Logout;

$logout = new Logout();
$logout->logout();

?>