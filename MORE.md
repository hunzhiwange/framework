```
_____________                           _______________
 ______/     \__  _____  ____  ______  / /_  _________
  ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
   __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
     \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
        \_\                /_/_/         /_/
```

## Run Tests

```
cd /data/codes/queryphp/vendor/hunzhiwange/framework      
composer install
cp tests/config.php tests/config.local.php // Modify the config
php vendor/bin/phinx migrate
php build/phpunit tests
```

## Framework Command

```
cd /data/codes/queryphp
php leevel make:docwithin vendor/hunzhiwange/framework/tests
```

## Unified Code Style

### Install PHP Coding Standards Fixer

<https://github.com/friendsofphp/php-cs-fixer>

It can be used without installation,we download a version for you.

### Base use

```
cd /data/codes/queryphp/vendor/hunzhiwange/framework
php build/php-cs-fixer fix --config=.php_cs.dist
```

### With Git hooks

Add a pre-commit for it.

```
cp build/pre-commit.sh .git/hooks/pre-commit
chmod 777 .git/hooks/pre-commit
```

Pass hook

```
git commit -h
git commit -n -m 'pass hook' # bypass pre-commit and commit-msg hooks
```

## PHPStan 

```
php build/phpstan analyse
```

## License

The QueryPHP framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
