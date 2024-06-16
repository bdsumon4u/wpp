<?php

use App\Http\Controllers\CustomTextController;
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

Route::middleware(['splade'])->group(function () {
    Route::get('/', fn () => view('home'))->name('home');
    Route::get('/docs', fn () => view('docs'))->name('docs');

    Route::resource('/devices', \App\Http\Controllers\DeviceController::class);
    Route::get('/user/device/chats/{device}', [\App\Http\Controllers\DeviceController::class, 'chats'])->name('user.device.chats');
    Route::get('/user/device/groups/{device}', [\App\Http\Controllers\DeviceController::class, 'groups'])->name('user.device.groups');
    Route::post('/user/send-message/{uuid}', [App\Http\Controllers\DeviceController::class, 'sendMessage'])->name('user.chat.send-message');

    Route::get('ct', [CustomTextController::class, 'sentCustomText']);

    Route::view('chat', 'chat');

    // Registers routes to support the interactive components...
    Route::spladeWithVueBridge();

    // Registers routes to support password confirmation in Form and Link components...
    Route::spladePasswordConfirmation();

    // Registers routes to support Table Bulk Actions and Exports...
    Route::spladeTable();

    // Registers routes to support async File Uploads with Filepond...
    Route::spladeUploads();
});
