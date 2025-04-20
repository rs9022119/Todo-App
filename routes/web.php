<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [TaskController::class, 'index']);

Route::post('/task', [TaskController::class, 'addTask']);
Route::post('/complete_task', [TaskController::class, 'completeTask']);
Route::post('/delete_task', [TaskController::class, 'deleteTask']);
Route::get('/show_all_tasks', [TaskController::class, 'showAllTask']);
