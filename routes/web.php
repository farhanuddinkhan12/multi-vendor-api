<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    dd(app()->getBindings());
});


Route::get('/success', function () {
    // redirect frontend Vue success page
    return redirect('http://localhost:8080/success');
});

Route::get('/cancel', function () {
    // redirect frontend Vue cancel page
    return redirect('http://localhost:8080/cancel');
});