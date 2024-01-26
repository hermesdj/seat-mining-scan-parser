<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web', 'auth', 'locale'],
    'prefix' => '/tools/belt-scan-parser',
    'namespace' => 'HermesDj\Seat\SeatMiningScanParser\Http\Controllers'
], function () {
    Route::get('/')
        ->name('scan-parser::parser')
        ->uses('MiningScanParserController@parser');

    Route::post('/')
        ->name('scan-parser::parse')
        ->uses('MiningScanParserController@parse');
});