# Run MapMaker on your computer

This is a simple guide for opening MapMaker locally so you can see and test
changes. You do not need to know Laravel.

## 1. Get the project

The easiest option is to open [the GitHub repository](https://github.com/FinnWiel/Mappy),
click **Code**, choose **Download ZIP**, and unzip it somewhere easy to find
(such as your Desktop).

If you already use Git, you can instead run:

~~~bash
git clone https://github.com/FinnWiel/Mappy.git
~~~

## 2. Install PHP and Composer

MapMaker needs two separate tools: **PHP** and **Composer**. You only need to
install them once on your computer.

### Mac

If you have [Homebrew](https://brew.sh/) installed, open Terminal and run:

~~~bash
brew install php composer
~~~

If you do not have Homebrew yet, install it from its website first, then run
the command above.

### Windows

1. Download PHP from [PHP for Windows](https://windows.php.net/download/),
   unzip it somewhere simple such as `C:\php`, and add that folder to your
   Windows `Path` environment variable.
2. Download and run the [Composer installer](https://getcomposer.org/download/).
   When it asks where PHP is, choose the `php.exe` file from your PHP folder.

### Linux

Install the `php` and `composer` packages using your distribution's app store
or package manager.

After installing them, close and reopen Terminal (or PowerShell), then check
that both commands work:

~~~bash
php -v
composer --version
~~~

They should each print a version number.

## 3. Open a terminal in the project folder

Open the unzipped `Mappy` folder in Terminal:

- **Mac:** right-click the folder and choose **New Terminal at Folder**.
- **Windows:** right-click the folder and choose **Open in Terminal**.

## 4. Set it up (first time only)

Run these commands one at a time:

~~~bash
composer install
~~~

On **Mac/Linux**, run:

~~~bash
cp .env.example .env
touch database/database.sqlite
~~~

On **Windows PowerShell**, run instead:

~~~powershell
Copy-Item .env.example .env
New-Item -ItemType File -Path database/database.sqlite -Force
~~~

Then, on any computer, run:

~~~bash
php artisan key:generate
php artisan migrate --seed
~~~

## 5. View the website

Start MapMaker:

~~~bash
php artisan serve
~~~

Keep that terminal window open. Open this address in your browser:

<http://127.0.0.1:8000>

To stop the website later, return to Terminal and press `Ctrl + C`.

You can sign in with any of these demo accounts (all use password `password`):

| Email | What it can do |
| --- | --- |
| `admin@example.com` | Owns maps and can invite people |
| `editor@example.com` | Can edit an invited map |
| `viewer@example.com` | Can view an invited map |

## When someone makes changes

Refresh the browser page to see most visual changes. If they added a database
change, stop the server with `Ctrl + C`, then run:

~~~bash
php artisan migrate
php artisan serve
~~~

There is normally no need to install Node, npm, or run a front-end build.

## Start over with fresh sample data

Only on your own local copy, this deletes your local maps and recreates the
demo accounts and sample maps:

~~~bash
php artisan migrate:fresh --seed
~~~

Never run that command on the live Forge site.

## If something goes wrong

- `php: command not found` or `composer: command not found`: install PHP and
  Composer, then reopen Terminal.
- `no such table`: run `php artisan migrate --seed`.
- The page will not open: make sure `php artisan serve` is still running, then
  try <http://127.0.0.1:8000> again.
