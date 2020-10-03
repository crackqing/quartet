<?php 

/**
 * 检测特定IP,访问
 */
Route::group(['prefix' => '/ssc/'],function($router){

    $router->post('user','SscController@user');
    $router->post('userBank','SscController@userBank');


    $router->post('userDeduction','SscController@userDeduction');
});

