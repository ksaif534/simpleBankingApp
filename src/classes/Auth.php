<?php

namespace App\classes;

use App\classes\UserRole;

class Auth{
    public $name;
    public $email;
    protected $password;
    protected $role;
    protected $storage;
    protected $errors;
    protected $helpers;
    public $configArray;

    public function __construct($helpers,$errors,$storage){
        $this->helpers      = $helpers;
        $this->errors       = $errors;
        $this->storage      = $storage;
        $this->password     = '';
        $this->role         = 0;
        $this->configArray  = require __DIR__.'/../config/storage.php';
    }

    public function register(){
        $this->name     = '';
        $this->email    = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->sanitizeName();
            $this->sanitizeEmail();
            $this->sanitizePassword();
            $this->sanitizeRole();
            if (empty($this->errors)) {
                if ($this->helpers->config('users',$this->configArray) == $this->storage->getFileName()) {
                    //File
                    $user = [
                        'name'              => $this->name,
                        'email'             => $this->email,
                        'password'          => $this->password,
                        'role'              => $this->role
                    ];
                    $user   = $this->storage->updatedFileInputWithAutoIncrement($this->storage->getUsers(),$user);
                    $users  = $this->storage->getUsers();
                    array_push($users,$user);
                    if ($this->storage->putProcessedFileContent($this->storage->getFileName(),$users)) {
                        $this->helpers->flash('success', 'You have successfully registered. Please log in to continue');
                        header('Location: login.php');
                        exit;
                    } else {
                        $this->errors['auth_error'] = 'An error occurred. Please try again';
                        $this->helpers->flash('error',$this->errors['auth_error']);
                    }
                }else{
                    //DB
                    $query  = 'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)';
                    $stmt   = $this->storage->getPDO()->prepare($query);
                    $params = [
                        ':name'     => $this->name,
                        ':email'    => $this->email,
                        ':password' => $this->password,
                        ':role'     => $this->role
                    ];
                    if ($stmt->execute($params)) {
                        $this->helpers->flash('success', 'You have successfully registered. Please log in to continue');
                        header('Location: login.php');
                        exit;
                    } else {
                        $this->errors['auth_error'] = 'An error occurred. Please try again';
                    }
                }
            }
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->sanitizeEmail();
            $this->sanitizePassword();
            $flag = '';
            if ($this->helpers->config('users',$this->configArray) == $this->storage->getFileName()) {
                $flag = 'file';
                $user   = $this->storage->getUserByEmail($this->storage->getFileName(),$this->email);
            }else{
                $flag = 'db';
                $user   = $this->storage->getUserByEmailDB($this->email);
            }
            if (!empty($user)) {
                if ($user && password_verify($this->password,$user['password'])) {
                    $_SESSION['user_id']    = $user['id'];
                    $_SESSION['username']   = $user['name'];
                    switch ($user['role']) {
                        case UserRole::ADMIN->value:
                            header('Location: admin/customers.php');
                            exit;
                            break;
                        case UserRole::CUSTOMER->value:
                            header('Location: customer/dashboard.php');
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }else {
                    $this->errors['auth_error'] = 'Invalid email or password';
                    $this->helpers->flash('error',$this->errors['auth_error']);
                }    
            }else{
                $this->errors['auth_error'] = 'An error occurred. Please try again';
                $this->helpers->flash('error',$this->errors['auth_error']);
            }
        }
    }

    public function sanitizeName(){
        if (empty($_POST['name'])) {
            $this->errors['name'] = 'Please provide a name';
            $this->helpers->flash('name',$this->errors['name']);
        } else {
            $this->name = $this->helpers->sanitize($_POST['name']);
        }
    }

    public function sanitizeEmail(){
        if (empty($_POST['email'])) {
            $this->errors['email'] = 'Please provide an email address';
            $this->helpers->flash('email',$this->errors['email']);
        } else {
            $this->email = $this->helpers->sanitize($_POST['email']);
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $this->errors['email'] = 'Please provide a valid email address';
                $this->helpers->flash('email',$this->errors['email']);
            }
        }
    }

    public function sanitizePassword(){
        if (empty($_POST['password'])) {
            $this->errors['password'] = 'Please provide a password';
            $this->helpers->flash('password',$this->errors['password']);
        } elseif (strlen($_POST['password']) < 8) {
            $this->errors['password'] = 'Password must be at least 8 characters';
            $this->helpers->flash('password',$this->errors['password']);
        } else {
            if (isset($_POST['confirm_password'])) {
                $this->checkPasswordConfirmation();
                $this->password = $this->helpers->sanitize($_POST['password']);
                $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            }else{
                $this->password = $this->helpers->sanitize($_POST['password']);
            }
        }
    }

    public function checkPasswordConfirmation(){
        if (($_POST['password']) !== $_POST['confirm_password']) {
            $this->errors['confirm_password'] = 'Password and Confirm Password do not match';
            $this->helpers->flash('confirm_password',$this->errors['confirm_password']);
        }
    }

    public function sanitizeRole(){
        if (empty($_POST['role'])) {
            $this->errors['role']   = 'Please provide a role';
            $this->helpers->flash('role',$this->errors['role']);
        }else{
            $this->role             = $this->helpers->sanitize($_POST['role']);
        }
    }

    public function getHelpers(){
        return $this->helpers;
    }
}

?>