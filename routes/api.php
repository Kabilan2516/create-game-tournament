<?php

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PwaController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/pwa/room/{join_code}', [PwaController::class, 'roomStatus']);

Route::post('/fcm/token', function (Request $request) {
    $request->validate([
        'token' => 'required|string',
    ]);

    FcmToken::updateOrCreate(
        ['token' => $request->token],
        [
            'user_id' => Auth::id(),
            'device' => 'web'
        ]
    );

    return response()->json(['saved' => true]);
})->middleware('auth');

// if (token) {
//     await fetch('/fcm/token', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document
//                 .querySelector('meta[name="csrf-token"]')
//                 .getAttribute('content'),
//         },
//         body: JSON.stringify({ token }),
//     });

//     console.log("✅ Token saved to backend");
// }
