<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoteController;

Route::prefix('notes')->middleware('web')->group(function () {
    Route::post('/', [NoteController::class, 'store']); // create note
    Route::get('/', [NoteController::class, 'index']); //fetch note
    Route::get('/{id}', [NoteController::class, 'show']); //tes specific finding by specific note id
    Route::put('/{id}', [NoteController::class, 'update']); // update data with specific id
    Route::delete('/{id}', [NoteController::class, 'destroy']); // delete with specific id
});
