<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\ShowController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ShowController::class, 'shows'])->name('shows');

Route::get('/{show:year}', [ShowController::class, 'view'])->name('show')->where('show', '[0-9]{4}');

Route::get('/{show:year}/winners', [ResultsController::class, 'winners'])->name('winners');

Route::get('/account', [AuthController::class, 'account'])->name('account');

Route::get('/login/steam', [AuthController::class, 'loginSteam'])->name('login.steam');
Route::get('/login/discord', [AuthController::class, 'loginDiscord'])->name('login.discord');
Route::get('/login/steam/callback', [AuthController::class, 'callbackSteam']);
Route::get('/login/discord/callback', [AuthController::class, 'callbackDiscord']);
Route::get('/login/discord/first-time', [AuthController::class, 'discordFirstTime'])->name('login.discord.first-time');
Route::post('/login/discord/first-time', [AuthController::class, 'discordFirstTimeSubmit'])->name('login.discord.first-time.submit');
Route::get('/login/discord/first-time/callback', [AuthController::class, 'discordFirstTimeCallback'])->name('login.discord.first-time.callback');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/account/connect/steam', [AuthController::class, 'connectSteam'])->name('account.connect.steam');
