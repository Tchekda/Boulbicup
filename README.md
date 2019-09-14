# Boulbicup

Management website made for Ice Hockey tournaments at Boulogne-Billancourt (France)

[Not deployed yet](http://boulbicup.fr)

## Install development environnement

### Requirements
* Working Web Server with PHP >= 7.2 (Apache, Nginx, other...)
* [Composer](https://getcomposer.org/doc/00-intro.md) installed

### Installation
* Clone the repository 
```bash
git clone https://github.com/Tchekda/Boulbicup.git
```

* Go inside the directory 
```bash
cd Boulbicup
```

* Install PHP dependencies
```bash
composer install --dev
```

* Define environment variable `DATABASE_URL` (like `mysql://root:@localhost/boulbicup`)

* Create initial Admin account with a PHP script at the root of the project
```php
<?php
// adminaccount.php

require_once 'vendor/autoload.php';
require_once 'bootstrap.php';

$user = new Entity\User();
$user->setUsername('Username');
$user->setPassword(password_hash('YourPassword', PASSWORD_ARGON2ID));

$entityManager->persist($user);
$entityManager->flush();

echo "Created user " . $user->getId() . " with ID " . $user->getId() . "\r\n";
```
* Run the admin script
```bash
php adminaccount.php
```
* Visit the website as configured in your webserver and enjoy!