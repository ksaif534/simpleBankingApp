<?php

require_once __DIR__.'/../vendor/autoload.php';
use App\classes\Cli;
use App\classes\File;
use App\classes\Database;
use App\classes\User;
use App\classes\Helpers;

$helpers        = new Helpers();
$configArray    = require dirname(__DIR__,1).'/src/config/storage.php';
$userFilename   = dirname(__DIR__,1).'/src/files/users.txt';
$newUser        = new User(new File($userFilename));
$adminCliFile   = dirname(__DIR__,1).'/src/files/admin-cli.txt';
$dsn            = $helpers->config('dsn',$configArray);
$username       = $helpers->config('username',$configArray);
$password       = $helpers->config('password',$configArray);
$newPDO         = new PDO($dsn,$username,$password);
$newDB          = new Database($newPDO,$dsn,$username,$password);
$newUserDB      = new User($newDB);
// $app            = new Cli($newUser, new File($adminCliFile), $helpers, []);
// $app->run();
$app            = new Cli($newUserDB, $newDB, $helpers, []);
$app->run();
?>