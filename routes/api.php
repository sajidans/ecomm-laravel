<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;


Route::get('/homepage-category-banner', [HomeController::class, 'homePageCategoryBanner']);
Route::get('/categories', [HomeController::class, 'storeCategories']);
Route::get('/banners/{type}', [HomeController::class, 'homeSectionBanner']);
Route::get('/trending-products/{limit}', [HomeController::class, 'trendingProducts']);
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('api/admin');

Route::get('/users',function(){
    echo "dfg,dfjg";
});