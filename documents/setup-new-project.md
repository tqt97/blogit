# Setup new project

## Public storage link

```bash
php artisan storage:link
```

## Public notification table

```php
php artisan notifications:table
```

## Install laravel debugbar

```bash
composer require barryvdh/laravel-debugbar --dev
php artisan vendor:publish --provider="Barryvdh\Debugbar\ServiceProvider"
```

## Install laravel ide helper

```bash
composer require --dev barryvdh/laravel-ide-helper
php artisan vendor:publish --provider="Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider" --tag=config
```

Add to .gitignore

```
_ide_helper.php
.phpstorm.meta.php
```

## Install larastan

```bash
composer require --dev "larastan/larastan:^3.0"
```

Then, create a phpstan.neon or phpstan.neon.dist file in the root of your application. It might look like this:

```sh
touch phpstan.neon
```

```md
includes:
    - vendor/larastan/larastan/extension.neon
    - vendor/nesbot/carbon/extension.neon

parameters:

    paths:
        - app/

    # Level 10 is the highest level
    level: 5

    checkModelProperties: true
    checkModelMethodVisibility: true
    checkAuthCallsWhenInRequestScope: true
    checkConfigTypes: true

#    ignoreErrors:
#        - '#PHPDoc tag @var#'
#
#    excludePaths:
#        - ./*/*/FileToBeExcluded.php

```

Setup composer

```bash
"scripts": {
  "phpstan": [
    "./vendor/bin/phpstan analyse"
  ]
},
"scripts-descriptions": {
  "phpstan": "Run PHPStan static analysis against your application."
},
```

To setup github workflow, create file **phpstan.yml** in **.github/workflows**

## Install rector laravel

```bash
composer require --dev driftingly/rector-laravel
```

Create file rector.php to setup rules

```bash
touch rector.php
```

## Install laravel sail

```bash
composer require laravel/sail --dev
php artisan sail:install
```

Finally, you may start Sail. To continue learning how to use Sail, please continue reading the remainder of this documentation:

```bash
./vendor/bin/sail up
```

Since Sail is just Docker, you are free to customize nearly everything about it. To publish Sail's own Dockerfiles, you may execute the sail:publish command:

```bash
sail artisan sail:publish
```

### Debugging With Xdebug

Laravel Sail's Docker configuration includes support for Xdebug, a popular and powerful debugger for PHP. To enable Xdebug, ensure you have published your Sail configuration. Then, add the following variables to your application's .env file to configure Xdebug:

```env
SAIL_XDEBUG_MODE=develop,debug,coverage
```

Next, ensure that your published php.ini file includes the following configuration so that Xdebug is activated in the specified modes:

```bash
[xdebug]
xdebug.mode=${XDEBUG_MODE}
```

After modifying the php.ini file, remember to rebuild your Docker images so that your changes to the php.ini file take effect:

```bash
sail build --no-cache
```

## Install laravel horizon

```bash
composer require laravel/horizon
php artisan horizon:install
```

## Install duster

```bash
composer require tightenco/duster --dev
```

## Install phpstan

```bash
composer require --dev phpstan/phpstan
```

## Install husky

```bash
npm install --save-dev husky
npx husky init
```
