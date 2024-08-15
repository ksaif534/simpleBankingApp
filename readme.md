# BanguBank

BanguBank is a simple banking application with features for both 'Admin' and 'Customer' users. It's a HTML template starter pack for Laravel Career Path by Interactive Cares students.

![Admin View](screenshots/admin_preview.png)

![Customer View](screenshots/customers_preview.png)

# General Guideline

1. The commented 2-3 lines of the files: `register.php`, `login.php`, `dashboard.php`, `customers.php`, `customer_transactions.php` , `cli.php`, `deposit.php`, `withdraw.php`, `transfer.php` are the File logic and the active 2-3 equivalent lines are DB logic. If you want to successfully test the application, comment the 2-3 lines of DB logic from each of the above mentioned files and uncommment the 2-3 lines of the File logic from each of the files mentioned above(and vice-versa).

2. I have changed the CLI Admin registration to make it simpler by just prompting for `username`, `email` & `password` and the admin registration will be done. To run the cli script, simple go to the `admin` directory and run `php cli.php create-admin` and rest will follow.
