<?php

namespace App\classes;

class Dashboard{
    public User $user;
    protected Transaction $transaction;
    protected $transactions;

    public function __construct(User $user, Transaction $transaction){
        $this->user         = $user;
        $this->transaction  = $transaction;
        $this->transactions = $this->getAllTransactions();
    }

    public function getAllTransactions(){
        return $this->transaction->getAllTransactions();
    }

    public function getAuthenticatedUserBySession(){
        return $this->transaction->getAuthenticatedUserBySession();
    }

    public function getAllUserSpecificTransactions() : array
    {
        return $this->transaction->getAllUserSpecificTransactions();
    }

    public function calculateCurrentUserBalance() : int
    {
        return $this->transaction->calculateCurrentUserBalance();
    }
}

?>