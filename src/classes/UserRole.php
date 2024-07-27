<?php

namespace App\classes;

enum UserRole : string {
    case ADMIN      = 'admin';
    case CUSTOMER   = 'customer';
}

?>