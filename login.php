<?php
ob_start();
session_start();
require_once __DIR__.'/vendor/autoload.php';
use App\classes\Auth;
use App\classes\Helpers;
use App\classes\User;
use App\classes\File;
use App\classes\Database;
$helpers      = new Helpers();
$configArray  = require_once __DIR__.'/src/config/storage.php';
$dsn          = $helpers->config('dsn',$configArray);
$username     = $helpers->config('username',$configArray);
$password     = $helpers->config('password',$configArray);
$filename     = __DIR__.'/src/files/users.txt';
$newPDO       = new PDO($dsn,$username,$password);
$newDB        = new Database($newPDO,$dsn,$username,$password);
$auth         = new Auth(new Helpers(),[],new User($newDB));
$auth->login();
// $auth         = new Auth(new Helpers(),[],new User(new File($filename)));
// $auth->login();
?>

<!DOCTYPE html>
<html
  class="h-full bg-white"
  lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>

    <link
      rel="preconnect"
      href="https://fonts.googleapis.com" />
    <link
      rel="preconnect"
      href="https://fonts.gstatic.com"
      crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
      rel="stylesheet" />

    <style>
      * {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont,
          'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans',
          'Helvetica Neue', sans-serif;
      }
    </style>

    <title>Sign-In To Your Account</title>
  </head>
  <body class="h-full bg-slate-100">
    <div class="flex flex-col justify-center min-h-full py-12 sm:px-6 lg:px-8">
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2
          class="mt-6 text-2xl font-bold leading-9 tracking-tight text-center text-gray-900">
          Sign In To Your Account
        </h2>
      </div>

      <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="px-6 py-12 bg-white shadow sm:rounded-lg sm:px-12">
          <?php
            $message = $auth->getHelpers()->flash('success');
            if ($message) : ?>
              <div class="mt-2 bg-teal-100 border border-teal-200 text-sm text-teal-800 rounded-lg p-4" role="alert">
                  <span class="font-bold"><?= $message; ?></span>
              </div>
          <?php endif; ?>
          <?php 
            $msg = $auth->getHelpers()->flash('error');
            if ($msg) : ?>
              <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                  <span class="font-bold"><?= $msg; ?></span>
              </div>
          <?php endif; ?>
          <form
            class="space-y-6"
            action="login.php"
            method="POST">
            <div>
              <label
                for="email"
                class="block text-sm font-medium leading-6 text-gray-900"
                >Email address</label
              >
              <div class="mt-2">
                <input
                  id="email"
                  name="email"
                  type="email"
                  autocomplete="email"
                  novalidate
                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 p-2 sm:text-sm sm:leading-6" />
              </div>
            </div>
            <?php 
              $emailErr = $auth->getHelpers()->flash('email');
              if ($emailErr) : ?>
                <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                    <span class="font-bold"><?= $emailErr; ?></span>
                </div>
            <?php endif; ?>
            <div>
              <label
                for="password"
                class="block text-sm font-medium leading-6 text-gray-900"
                >Password</label
              >
              <div class="mt-2">
                <input
                  id="password"
                  name="password"
                  type="password"
                  autocomplete="current-password"
                  novalidate
                  class="block w-full p-2 text-gray-900 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" />
              </div>
            </div>
            <?php 
              $passwordErr = $auth->getHelpers()->flash('password');
              if ($passwordErr) : ?>
                <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                    <span class="font-bold"><?= $passwordErr; ?></span>
                </div>
            <?php endif; ?>
            <div>
              <button
                type="submit"
                class="flex w-full justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                Sign in
              </button>
            </div>
          </form>
        </div>

        <p class="mt-10 text-sm text-center text-gray-500">
          Don't have an account?
          <a
            href="./register.html"
            class="font-semibold leading-6 text-emerald-600 hover:text-emerald-500"
            >Register</a
          >
        </p>
      </div>
    </div>
  </body>
</html>
