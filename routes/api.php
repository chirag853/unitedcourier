<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CodController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Close COD/FOC order by AWB: awb_number + type(cod/foc) + remark -> finance_remark
Route::post('/close-cod', [CodController::class, 'close_cod'])->name('api.close-cod');
