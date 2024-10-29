## Setup

Read the `setup.md` of `laravel-example1` and choose the steps missing on your computer to finalize the install.  

## Main Page

`Basket` describes steps to use Postman API.  

## Models

`User` model is extended to support virtual wallet. New model `Product` with table is created.  

## Seeding

Seeding the database using `php artisan db:seed --class=UserSeeder` for 3 records. Change the class to `ProductSeeder` to generate 2 products or type `php artisan db:seed --class=UserProductsReset` to reset user wallet to 100 and initial products count.  
