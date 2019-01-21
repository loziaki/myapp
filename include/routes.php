<?php
use \Routing\Route;

Route::get('test1','TestController@abc');
Route::post('test2','TestController@cde');
Route::middleware(['lalala','bububu'])->group(function(){
    Route::get('test3','TestController1@abc');
    Route::post('test4','TestController1@cde');

    Route::middleware(['kekeke'])->group(function(){
        Route::get('test7','TestController1@abc');

        $temp = Route::middleware(['gigigi']);
        $temp->group(function(){
            Route::get('test8','TestController1@abc');
        });
        $temp->group(function(){
            Route::get('test9','TestController1@abc');
        });
    });

    Route::post('test6','TestController1@cde')->middleware(['jiujiujiu']);
});
Route::post('test5','TestController@cde')->middleware(['jiujiujiu']);