<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReturnTpl;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () { return view('./user/index'); });

Route::get('user',[ReturnTpl::class,'Return_tpluser']);

Route::get('admin',[ReturnTpl::class,'Return_tpladmin']);