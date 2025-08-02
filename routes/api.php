<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RentalController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
  // Public for all authenticated users
  Route::get('/books', [BookController::class, 'index']);
  Route::get('/books/{id}', [BookController::class, 'show']);


  // Admin-only actions
  Route::post('/books', [BookController::class, 'store']);
  Route::put('/books/{id}', [BookController::class, 'update']);
  Route::delete('/books/{id}', [BookController::class, 'destroy']);



  Route::post('/rentals', [RentalController::class, 'rent']);
  Route::post('/rentals/{id}/return', [RentalController::class, 'returnBook']);
  Route::get('/my-rentals', [RentalController::class, 'myRentals']);
});



