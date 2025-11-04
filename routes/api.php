<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\Api\SubmissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/users', function () {
    return response()->json(User::all());
});

/*
Route::post('/users', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string',
        'email' => 'required|email|unique:users,email',
    ]);

    $user = User::create($validated);
    return response()->json($user, 201);
});
*/

/*Route::post('/submit', [SubmissionController::class, 'store']);*/


Route::match(['get', 'post'], '/submit', [SubmissionController::class, 'handle']);


Route::delete('/users/{id}', [SubmissionController::class, 'destroy']); // حذف مستخدم واحد


Route::delete('/users', [SubmissionController::class, 'destroyAll']);   // حذف الكل أو مجموعة حسب body



