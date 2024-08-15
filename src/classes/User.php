<?php

namespace App\classes;

use App\classes\Helpers;
use App\classes\UserRole;

class User{
    protected $storage  = null;
    protected $helpers;
    public $configArray;

    public function __construct($storage){
        $this->storage      = $storage;
        $this->configArray  = require dirname(__DIR__,2).'/src/config/storage.php';
    }

    public function getUsers() : array
    {
        $this->helpers = new Helpers();
        if ($this->helpers->config('users',$this->configArray) == $this->storage->getFileName()) {
            //File
            return $this->storage->getData();
        }
        //DB
        return $this->storage->getData('users');
    }

    public function getPDO(){
        return $this->storage->getPDO();
    }

    public function getAllRegisteredCustomers(): array
    {
        $allUsers   = $this->getUsers();
        $query      = [];
        foreach ($allUsers as $user) {
            if ($user['role'] == UserRole::CUSTOMER->value) {
                array_push($query, $user);
            }
        }
        return $query;
    }

    public function getAuthenticatedUserBySession(){
        $this->helpers  = new Helpers();
        $query          = [];
        if ($this->helpers->checkSession()) {
            foreach ($this->getUsers() as $user) {
                if ($user['id'] == $_SESSION['user_id']) {
                    $query = $user;
                }
            }
        }
        return $query;
    }

    public function getFileName() : string
    {
        return $this->storage->filename;
    }

    public function getProcessedFileContent($filename){
        return $this->storage->getProcessedFileContent($filename);
    }

    public function putProcessedFileContent($filename,$data){
        return $this->storage->putProcessedFileContent($filename,$data);
    }

    public function getUserByEmail($filename,$email){
        $unserializedFileContent = $this->getProcessedFileContent($filename);
        $query = [];
        foreach ($unserializedFileContent as $user) {
            if ($user['email'] == $email) {
                $query = $user;
                break;
            }
        }
        return $query;
    }

    public function isValidEmailFile($filename,$email) : bool{
        $fileData = $this->getUserByEmail($filename,$email);
        if(empty($fileData)){
            return false;
        }
        return true;
    }

    public function getUserByEmailDB($email){
        $query  = 'SELECT * FROM users WHERE email = :email';
        $stmt   = $this->storage->getPDO()->prepare($query);
        $params = [
            ':email'    => $email
        ];
        $stmt->execute($params); 
        return $stmt->fetch();
    }

    public function isValidEmailDB($email) : bool{
        $emailData = $this->getUserByEmailDB($email);
        if(empty($emailData)){
            return false;
        }
        return true;
    }

    public function updatedFileInputWithAutoIncrement($users,$user){
        $max_id = 0;
        foreach ($users as $item) {
            if ($item['id'] > $max_id) {
                $max_id = $item['id'];
            }
        }
        $new_id = $max_id + 1;
        $updatedUser = [
            'id'        => $new_id,
            'name'      => $user['name'],
            'email'     => $user['email'],
            'password'  => $user['password'],
            'role'      => $user['role']
        ];
        return $updatedUser;
    }
}

?>