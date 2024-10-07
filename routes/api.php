<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\GroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1.0.0')->group(function () {

    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('logout', [AuthController::class, 'logout']);

        // les routes de groupes et d'ajout de membres
        Route::post('group', [GroupController::class, 'registerGroup']);
        Route::post('group/{id}/members', [GroupController::class, 'addMember']);
        Route::get('groups', [GroupController::class, 'index']);
        Route::get('/user-groups', [GroupController::class, 'getGroupByUser']);


        // les routes pour les fichiers
        Route::post('uploadfile/{id}', [FileController::class, 'upload']);
        Route::get('groupfiles/{groupId}', [FileController::class, 'getFilesForGroup']);
        Route::get('groups/{groupId}/{fileId}/download', [FileController::class, 'download']);
        Route::delete('groups/{groupId}/{fileId}/delete', [FileController::class, 'delete']);
    });
});
