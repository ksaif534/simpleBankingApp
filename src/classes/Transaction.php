<?php

namespace App\classes;

use App\classes\TransactionType;

class Transaction{
    protected $storage;
    public $user;
    public $authUser;
    protected $errors;
    protected $helpers;
    public $amount;
    public $configArray;

    public function __construct($storage,User $user, $errors, $helpers){
        $this->storage      = $storage;
        $this->user         = $user;
        $this->errors       = $errors;
        $this->helpers      = $helpers;
        $this->authUser     = $this->getAuthenticatedUserBySession();
        $this->configArray  = require __DIR__.'/../config/storage.php';
    }

    public function getAllTransactions() : array
    {
        if ($this->helpers->config('transactions',$this->configArray) == $this->getFileName()) {
            return $this->storage->getData();
        }
        return $this->storage->getData('transactions');
    }

    public function getTransactionsByUser($requestUri) : array
    {
        $wildcard       = $this->fetchWildCard($requestUri);
        $userId         = 0;
        $email          = ''; 
        $wildCardArr    = $this->fetchWildCardArr($wildcard);
        $userId         = $this->fetchUserId($wildCardArr);
        $email          = $this->fetchEmail($wildCardArr);
        $query = [];
        $transactions   = $this->getAllTransactions();
        foreach ($transactions as $transaction) {
            if ($transaction['user_id'] == $userId || ($transaction['receiver_email'] == $email && $transaction['type'] == 3)) {
                array_push($query,$transaction);
            }
        }
        return $query;
    }

    public function fetchWildCard($requestUri) : string
    {
        $wildcard           = null;
        if (strpos($requestUri,'.php') !== false) {
            $position = strpos($requestUri,'.php') + 4;
            $wildcard = substr($requestUri,$position);
        }
        return $wildcard;
    }

    public function fetchWildCardArr($wildcard) : array
    {
        return explode("/",$wildcard);
    }

    public function fetchUserId($wildCardArr){
        return $wildCardArr[1];
    }

    public function fetchEmail($wildCardArr) : string
    {
        return $wildCardArr[2];
    }

    public function getHelpers(){
        return $this->helpers;
    }

    public function getAuthenticatedUserBySession() : array
    {
        return $this->user->getAuthenticatedUserBySession();
    }

    public function getAllUserSpecificTransactions() : array
    {
        $query          = [];
        foreach ($this->getAllTransactions() as $transaction) {
            if ($transaction['user_id'] == $this->authUser['id'] || ($transaction['receiver_email'] == $this->authUser['email'] && $transaction['type'] == TransactionType::TRANSFER->value)) {
                array_push($query,$transaction);
            }
        }
        return $query;
    }

    public function calculateCurrentUserBalance() : int
    {
        $sum = 0;
        foreach ($this->getAllTransactions() as $transaction) {
            if ($transaction['user_id'] != $this->authUser['id']) {
                //If User Receives Transfer
                if ($transaction['receiver_email'] == $this->authUser['email'] && $transaction['type'] == TransactionType::TRANSFER->value) {
                    $sum += $transaction['amount'] * -1;
                }
            }else{
                $sum += $transaction['amount'];
            }
        }
        return round($sum);
    }

    public function getFileName() : string
    {
        return $this->storage->filename;
    }

    public function storeTransaction() : void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->sanitizeAmount();
            if (empty($this->errors)) {
                if ($_POST['type'] == 'deposit') {
                    $this->store();
                }else{
                    if ($this->isWithdrawable($this->amount)) {
                        $this->store();
                    }else{
                        if ($this->isTransferrable($this->amount)) {
                            $this->store();
                        }else{
                            $this->errors['transfer_error'] = 'Sorry, not enough current balance to transfer';
                            $this->helpers->flash('transfer_error',$this->errors['transfer_error']);
                        }
                        $this->errors['withdraw_error'] = 'Sorry, not enough current balance to withdraw';
                        $this->helpers->flash('withdraw_error',$this->errors['withdraw_error']);
                    }
                }
            }
        }
    }

    public function store() : void 
    {
        $authUser       = $this->user->getAuthenticatedUserBySession();
        $email          = isset($_POST['type']) ? ($_POST['type'] != TransactionType::TRANSFER->value ? $authUser['email'] : $_POST['email']) : '';
        $sender         = $this->authUser['name'];
        $amountSignature= isset($_POST['type']) ? ($_POST['type'] == TransactionType::DEPOSIT->value ? 1 : -1) : 0; 
        $userByEmail    = ($this->helpers->config('users',$this->configArray) == $this->user->getFileName()) ? $this->user->getUserByEmail($this->user->getFileName(),$email) : $this->user->getUserByEmailDB($email);
        $receiver       = isset($_POST['type']) ? ($_POST['type'] != TransactionType::TRANSFER->value ? $authUser['name'] : (isset($userByEmail['name']) ?? null)) : '';
        $emailValidOrNot= false;
        if ($this->helpers->config('transactions',$this->configArray) == $this->getFileName()) {
            //File
            $transaction    = [
                'user_id'           => $_SESSION['user_id'],
                'sender_name'       => $sender,
                'receiver_email'    => $email,
                'receiver_name'     => $receiver,
                'type'              => $_POST['type'],
                'amount'            => $this->amount * $amountSignature,
                'date'              => date('Y-m-d H:i:s')
            ];
            $transactions   = $this->getAllTransactions();
            if ($this->isValidEmail($email)) {
                array_push($transactions,$transaction);
                $emailValidOrNot = true;
            }
        }else{
            //DB
            $query          = 'INSERT INTO transactions (user_id, sender_name, receiver_email, receiver_name, type, amount, date) VALUES(:user_id, :sender_name, :receiver_email, :receiver_name, :type, :amount, :date)';
            $stmt           = $this->storage->getPDO()->prepare($query);
            $params         = [
                ':user_id'          => $_SESSION['user_id'],
                ':sender_name'      => $sender,
                ':receiver_email'   => $email,
                ':receiver_name'    => $receiver,
                ':type'             => $_POST['type'],
                ':amount'           => $this->amount * $amountSignature,
                ':date'             => date('Y-m-d H:i:s')
            ];
            if ($this->isValidEmail($email)) {
                $emailValidOrNot = true;
            }
        }
        if ($emailValidOrNot) {
            $hasProcessedFileOrDB = ($this->helpers->config('transactions',$this->configArray) == $this->getFileName()) ? $this->putProcessedFileContent($this->getFileName(),$transactions) : $stmt->execute($params);
        }else{
            $hasProcessedFileOrDB = false;
        }
        if ($hasProcessedFileOrDB) {
            switch ($_POST['type']) {
                case 'deposit':
                    $this->helpers->flash('success', 'You have successfully deposited the transaction amount');
                    header('Location: dashboard.php');
                    exit;
                    break;
                case 'withdraw':
                    $this->helpers->flash('success', 'You have successfully withdrawn the transaction amount');
                    header('Location: dashboard.php');
                    exit;
                    break;
                case 'transfer':
                    $this->helpers->flash('success', 'You have successfully transferred the transaction amount');
                    header('Location: dashboard.php');
                    exit;
                    break;
                
                default:
                    # code...
                    break;
            }
        }else{
            $this->errors['amount_error'] = 'A Transaction Error Occured. Please Try Again.';
            $this->helpers->flash('amount_error',$this->errors['amount_error']);
        }
    }

    public function putProcessedFileContent($filename, $data){
        return $this->storage->putProcessedFileContent($filename, $data);
    }

    public function sanitizeAmount() : void
    {
        if (empty($_POST['amount'])) {
            $this->errors['amount'] = 'Please provide a valid amount';
        }else{
            $this->amount = $this->helpers->sanitize($_POST['amount']);
        }
    }

    public function isWithdrawable($amountToWithdraw) : bool
    {
        $currentUserBalance = $this->calculateCurrentUserBalance();
        if ($amountToWithdraw > $currentUserBalance) {
            return false;
        }
        return true;
    }

    public function isTransferrable($amountToTransfer) : bool
    {
        $currentUserBalance = $this->calculateCurrentUserBalance();
        if ($amountToTransfer > $currentUserBalance) {
            return false;
        }
        return true;
    }

    public function isValidEmail($email){
        if (file_exists($this->storage->getFileName())) {
            return $this->user->isValidEmailFile(dirname(__DIR__,1).'/files/users.txt',$email);
        }
        return $this->user->isValidEmailDB($email);
    }
}

?>