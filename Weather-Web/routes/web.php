<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\NicknameLocationController;
use Illuminate\Support\Facades\Route;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root to current weather
Route::get('/', function () { 
    return redirect('/current');
});

// Weather routes (protected by auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/current', [WeatherController::class, 'current'])->name('weather.current');
    Route::get('/forecast', [WeatherController::class, 'forecast'])->name('weather.forecast');
    Route::get('/history', [WeatherController::class, 'history'])->name('weather.history');
    Route::get('/search', [WeatherController::class, 'search'])->name('weather.search');
    Route::post('/save-location', [WeatherController::class, 'saveLocation'])->name('weather.save-location');
    
    // Nickname locations
    Route::get('/nicknames', [NicknameLocationController::class, 'index'])->name('nicknames.index');
    Route::post('/nicknames', [NicknameLocationController::class, 'store'])->name('nicknames.store');
    Route::put('/nicknames/{nickname}', [NicknameLocationController::class, 'update'])->name('nicknames.update');
    Route::delete('/nicknames/{nickname}', [NicknameLocationController::class, 'destroy'])->name('nicknames.destroy');
    // Add this route for getting weather data for nickname locations
    Route::get('/nicknames/{nickname}/weather', [NicknameLocationController::class, 'getWeather'])->name('nicknames.weather');
});