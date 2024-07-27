<?php

namespace App\classes;

enum TransactionType : string{
    case DEPOSIT    = 'deposit';
    case WITHDRAW   = 'withdraw';
    case TRANSFER   = 'transfer';
}

?>