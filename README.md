## Setup

Read the `setup.md` of [`laravel-example1`](https://github.com/ZetaRet/laravel-example1) and choose the steps missing on your computer to finalize the install.  

## Main Page

`Basket` describes steps to use Postman API.  

## Postman

__get products:__ GET request to list all products from database  
__get csrf:__ GET request to save in collection variables the form token, needed for POST requests  
__get basket:__ GET request to retrieve current items in the basket and user wallet  
__increment basket:__ POST request to set product in the basket plus 1, checks wallet total  
__decrement basket:__ POST request to set product in the basket minus 1  
__clear basket:__ POST request to clear the basket  
__update basket:__ POST request to set products in the basket, real quantity is invisible on this step, checks wallet total, deletes previous basket  
__purchase basket:__ POST request to sum transaction query and set the user purchase from the basket, checks all baskets to ensure 1 quantity left per user and manages maximum per product using stored total (per user max is 5), clears the basket  

## Models

`User` model is extended to support virtual wallet. New model `Product` with table is created.  

## Seeding

Seeding the database using `php artisan db:seed --class=UserSeeder` for 3 records. Change the class to `ProductSeeder` to generate 2 products or type `php artisan db:seed --class=UserProductsReset` to reset user wallet to 100 and initial products count.  
