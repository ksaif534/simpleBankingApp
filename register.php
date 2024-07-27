<?php
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
$auth         = new Auth($helpers,[],new User($newDB));
$auth->register();
// $auth         = new Auth($helpers,[],new User(new File($filename)));
// $auth->register();
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

    <title>Create A New Account</title>
  </head>
  <body class="h-full bg-slate-100">
    <div class="flex flex-col justify-center min-h-full py-6 sm:px-6 lg:px-8">
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2
          class="mt-3 text-2xl font-bold leading-9 tracking-tight text-center text-gray-900">
          Create A New Account
        </h2>
      </div>

      <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="px-6 py-12 bg-white shadow sm:rounded-lg sm:px-12">
          <?php
            $msg = $auth->getHelpers()->flash('error');
            if ($msg) : ?>
              <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                  <span class="font-bold"><?= $msg; ?></span>
              </div>
          <?php endif; ?>
          <form
            class="space-y-6"
            action="register.php"
            method="POST">
            <div>
              <label
                for="name"
                class="block text-sm font-medium leading-6 text-gray-900"
                >Name</label
              >
              <div class="mt-2">
                <input
                  id="name"
                  name="name"
                  type="text"
                  novalidate
                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2" />
              </div>
            </div>
            <?php
              $nameErr = $auth->getHelpers()->flash('name');
              if ($nameErr) : ?>
                <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                    <span class="font-bold"><?= $nameErr; ?></span>
                </div>
            <?php endif; ?>
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
                  novalidate
                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2" />
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
                  novalidate
                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2" />
              </div>
            </div>
            <?php
              $passErr = $auth->getHelpers()->flash('password');
              if ($passErr) : ?>
                <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                    <span class="font-bold"><?= $passErr; ?></span>
                </div>
            <?php endif; ?>
            <div>
              <label
                for="confirm_password"
                class="block text-sm font-medium leading-6 text-gray-900"
                >Confirm Password</label
              >
              <div class="mt-2">
                <input
                  id="confirm_password"
                  name="confirm_password"
                  type="password"
                  novalidate
                  class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2" />
              </div>
            </div>
            <?php
              $confirmPass = $auth->getHelpers()->flash('confirm_password');
              if ($confirmPass) : ?>
                <div class="mt-2 bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4" role="alert">
                    <span class="font-bold"><?= $confirmPass; ?></span>
                </div>
            <?php endif; ?>
            <div>
              <label 
              for="role"
              class="block text-sm font-medium leading-6 text-gray-900"
              >
                Role
              </label>
              <div class="mt-2">
                <select 
                name="role" 
                id="role"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2"
                >
                  <option value="customer">User</option>
                </select>
              </div>
            </div>

            <div>
              <button
                type="submit"
                class="flex w-full justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                Register
              </button>
            </div>
          </form>
        </div>

        <p class="mt-10 text-sm text-center text-gray-500">
          Already a customer?
          <a
            href="./login.php"
            class="font-semibold leading-6 text-emerald-600 hover:text-emerald-500"
            >Sign-in</a
          >
        </p>
      </div>
    </div>
  </body>
</html>
