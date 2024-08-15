<?php

namespace App\classes;

class Cli{
    protected User $storage;
    protected $admin;
    protected $helpers;
    protected $errors;
    protected $adminArr;
    protected $adminCliPath;
    public $configArray;

    public function __construct(User $storage, $admin, $helpers, $errors){
        $this->storage      = $storage;
        $this->admin        = $admin;
        $this->helpers      = $helpers;
        $this->errors       = $errors;
        $this->adminArr     = array();
        $this->configArray  = require dirname(__DIR__,2).'/src/config/storage.php';
        $this->adminCliPath = dirname(__DIR__,1).'/files/admin-cli.txt';
    }

    public function run() : int {
        global $argv;
        $commandName = $argv[1] ?? null;
        if ($commandName == 'create-admin') {
            while(true){
                echo "Enter Admin Name:\n";
                $this->addName();
                echo "Enter Admin Email:\n";
                $this->addEmail();
                echo "Enter Admin Password:\n";
                $this->addPassword();
                $this->submitForm();
                $this->exitApp();
                return 0;
            }
        }else{
            $this->showHelp();
        }
    }

    private function showHelp() : void {
        echo "Usage: cli.php <command>\n";
        echo "Avaialble Commands: \n";
        echo " create-admin\n";
    }

    private function readAdditionalInfo() : string {
        $handle = fopen("php://stdin","r");
        $line = fgets($handle);
        fclose($handle);
        return (string) trim($line);
    }

    private function exitApp() : void {
        echo "Exiting the application, goodbye!\n";
    }

    private function addName() : void {
        $name = $this->sanitizeName($this->readAdditionalInfo());
        if (!file_exists($this->admin->filename)) {
            $this->adminArr = $this->admin->getData('users');
        }else{
            if (filesize($this->admin->filename) > 0) {
                $this->adminArr = $this->admin->getProcessedFileContent($this->adminCliPath);
            }
        }
        if (is_array($this->adminArr)) {
            $this->adminArr['name'] = $name;
        }
        if (file_exists($this->admin->filename)) {
            $this->admin->putProcessedFileContent($this->adminCliPath,$this->adminArr);
        }else{
            $this->admin->storeAdminCliData('name',$name);
        }
    }

    private function sanitizeName($name) : string {
        if (empty($name)) {
            $this->errors['name'] = 'please provide a name';
            $this->helpers->flash('name',$this->errors['name']);
        }else{
            $name = $this->helpers->sanitize($name);
        }
        return $name;
    }

    private function addEmail() : void {
        $email = $this->sanitizeEmail($this->readAdditionalInfo());
        if (!file_exists($this->admin->filename)) {
            $this->adminArr = $this->admin->getData('users');
        }else{
            if (filesize($this->admin->filename) > 0) {
                $this->adminArr = $this->admin->getProcessedFileContent($this->adminCliPath);
            }
        }
        if (is_array($this->adminArr)) {
            $this->adminArr['email'] = $email;
        }
        if (file_exists($this->admin->filename)) {
            $this->admin->putProcessedFileContent($this->adminCliPath,$this->adminArr);
        }else{
            $this->admin->storeAdminCliData('email',$email);
        }
    }

    private function sanitizeEmail($email) : string {
        if (empty($email)) {
            $this->errros['email'] = 'please provide an email';
            $this->helpers->flash('email',$this->errors['email']);
        }else{
            $email = $this->helpers->sanitize($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors['email'] = 'please provide a valid email address';
                $this->helpers->flash('email',$this->errors['email']);
            }
        }
        return $email;
    }

    private function addPassword() : void {
        $password = $this->sanitizePassword($this->readAdditionalInfo());
        if (!file_exists($this->admin->filename)) {
            $this->adminArr = $this->admin->getData('users');
        }else{
            if (filesize($this->admin->filename) > 0) {
                $this->adminArr = $this->admin->getProcessedFileContent($this->adminCliPath);
            }
        }
        if (is_array($this->adminArr)) {
            $this->adminArr['password'] = $password;
        }
        if (file_exists($this->admin->filename)) {
            $this->admin->putProcessedFileContent($this->adminCliPath,$this->adminArr);
        }else{
            $this->admin->storeAdminCliData('password',$password);
        }
    }

    private function sanitizePassword($password) : string {
        if (empty($password)) {
            $this->errors['password'] = 'please provide a password';
            $this->helpers->flash('password',$this->errors['password']);
        }elseif (strlen($password) < 8) {
            $this->errors['password'] = 'password must be at least 8 characters';
            $this->helpers->flash('password',$this->errors['password']);
        }else{
            $password = $this->helpers->sanitize($password);
            $password = password_hash($password,PASSWORD_DEFAULT);
        }
        return $password;
    }

    private function addRole() : void {
        if (!file_exists($this->admin->filename)) {
            $this->adminArr = $this->admin->getData('users');
        }else{
            if (filesize($this->admin->filename) > 0) {
                $this->adminArr = $this->admin->getProcessedFileContent($this->adminCliPath);
            }
        }
        if (is_array($this->adminArr)) {
            $this->adminArr['role'] = 'admin';
        }
        if (file_exists($this->admin->filename)) {
            $this->admin->putProcessedFileContent($this->adminCliPath,$this->adminArr);
        }else{
            $this->admin->storeAdminCliData('role',$this->adminArr['role']);
        }
    }

    private function submitForm() : void {
        $this->addRole();
        $users = $this->storage->getUsers();
        if (!file_exists($this->admin->filename)) {
            $this->adminArr = $this->admin->getData('users');
        }else{
            if (filesize($this->admin->filename) > 0) {
                $this->adminArr = $this->admin->getProcessedFileContent($this->adminCliPath);
            }
        }
        if (file_exists($this->admin->filename)) {
            $this->adminArr = $this->storage->updatedFileInputWithAutoIncrement($users,$this->adminArr);
            array_push($users,$this->adminArr);
            $this->storage->putProcessedFileContent('../src/files/users.txt',$users);
        }
        echo "Form is Submitted\n";
    }
}

?>