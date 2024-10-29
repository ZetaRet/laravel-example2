<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/welcome/', function () {
    return view('welcome');
});
Route::get('/', [
    PageController::class,
    'show'
]);
Route::get('/basket/', [
    PageController::class,
    'basket'
]);
Route::get('/products/get', [
    PageController::class,
    'getProducts'
]);
Route::get('/basket/get', [
    PageController::class,
    'getBasket'
]);
Route::post('/basket/update', [
    PageController::class,
    'updateBasket'
]);
Route::post('/basket/purchase', [
    PageController::class,
    'purchase'
]);
Route::get('/token/csrf', [
    PageController::class,
    'getCSRF'
]);

?>