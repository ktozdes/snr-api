<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="100"></a></p>

## About Laravel

Астар, ты меня видишь???

## Project setup
1) git clone repository
2) cd into `snc-api` directory
3) run `composer install`
4) copy `env.example` as `.env` and change database connection accordingly
5) run `php artisan passport:install` command
6) run `php artisan migrate`. This command will populate database
7) run `php artisan key:generate`
8) run `php artisan passport:keys`
9) run `php artisan passport:client --personal`


In some cases (Linux, Mac) you might have to change permission for directory `storage` to 777 recursively.
<br/>
Also you might have to create folders as `cache`, `testing`, `views`, `sessions` inside `storage/framework` directory
<br/>
If you reseed or start with clean database, please run following after migration, but before db:seed
`php artisan passport:client --personal`

## Side notes

Used libraries
* laravel passport
