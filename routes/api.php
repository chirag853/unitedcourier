<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CodController;
use App\Http\Controllers\Api\CustomerManifestController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Close COD/FOC order by AWB: awb_number + type(cod/foc) + remark -> finance_remark
Route::post('/close-cod', [CodController::class, 'close_cod'])->name('api.close-cod');

// Customer manifest APIs (v1): same payload, api_provider + method decide carrier.
Route::prefix('v1')->group(function () {
    Route::post('/auth/token', [CustomerManifestController::class, 'token'])->name('api.v1.token');

    Route::middleware('customer.api')->group(function () {
        Route::get('/services', [CustomerManifestController::class, 'services'])->name('api.v1.services');
        Route::get('/services/{customerCode}', [CustomerManifestController::class, 'servicesByCode'])
            ->where('customerCode', '[A-Za-z0-9]+')->name('api.v1.services.by-code');
        Route::get('/access', [CustomerManifestController::class, 'access'])->name('api.v1.access');
        Route::post('/manifests', [CustomerManifestController::class, 'store'])->name('api.v1.manifests.store');
        Route::post('/manifests/from-draft', [CustomerManifestController::class, 'manifestDraft'])->name('api.v1.manifests.draft');
        Route::get('/manifests/{reference}', [CustomerManifestController::class, 'show'])->name('api.v1.manifests.show');
    });
});
