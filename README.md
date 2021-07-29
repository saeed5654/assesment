<h1>Table of Contents</h1>
<ul>
<li>About</li>
<li>Getting Started</li>
<li>Prerequisites</li>
<li>Dependencies</li>
<li>Usage</li>
<li>API EndPoints</li>
</ul>

<h1>About</h1>
<p>Application for user Profile and SignUp Invitation.</p>

<h1>Getting Started</h1>
<h2>Prerequisites</h2>
<ul>
<li>PHP 7.3.12</li>
<li>MySql latest stable version: 5.1.1</li>
<li>Laravel 8.0</li>
<li>Composer</li>
</ul>
<h1>Dependencies</h1>
<li>SocketLabs</li>
<li>Passport</li>

<h1>Usage</h1>
Clone the project via git clone or download the zip file.<br><br> 
<strong>.env</strong><br><br>
Copy contents of .env.example file to .env file. Create a database and connect your database in .env file.
<h3>Run commands</h3> 
<ul>
<li>composer install</li> 
<li>php artisan key:generate</li> 
<li>php artisan migrate</li> 
<li>php artisan db:seed --class=AdminSeeder</li>
<li>php artisan passport:install</li> 
<li>php artisan serve</li>
</ul>
