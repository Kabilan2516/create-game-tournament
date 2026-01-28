<?php

namespace App\Http\Controllers;

use App\Models\TournamentJoin;
use Illuminate\Support\Facades\Crypt;

class PwaController extends Controller
{
    public function roomStatus($join_code)
    {
        try {

            // 🔹 FIND JOIN WITH RELATIONS
            $join = TournamentJoin::with(['tournament', 'messages'])
                ->where('join_code', $join_code)
                ->first();

            if (!$join) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid join code'
                ], 404);
            }

            // 🔹 BASE RESPONSE DATA
            $data = [
                'success'      => true,
                'tournament'  => $join->tournament?->title,
                'start_time'  => optional($join->tournament?->start_time)->format('d M Y, h:i A'),
                'status'      => $join->status,
                'room_visible'=> (bool) $join->room_visible,

                // Messages
                'messages' => $join->messages->map(function ($msg) {
                    return [
                        'time' => $msg->created_at ? $msg->created_at->format('h:i A') : null,
                        'text' => $msg->message,
                        'sender' => $msg->sender,
                    ];
                }),
            ];

            // 🔐 ONLY SEND ROOM DETAILS IF APPROVED + RELEASED
            if (
                $join->status === 'approved' &&
                $join->room_visible &&
                $join->tournament &&
                $join->tournament->room_id &&
                $join->tournament->room_password
            ) {

                try {
                    $password = Crypt::decryptString($join->tournament->room_password);
                } catch (\Throwable $e) {
                    // If decrypt fails, don't crash app
                    $password = null;
                }

                $data['room'] = [
                    'room_id' => $join->tournament->room_id,
                    'room_password' => $password,
                ];
            } else {
                $data['room'] = null;
            }

            return response()->json($data);

        } catch (\Throwable $e) {

            // 🔥 RETURN REAL ERROR (for debugging now)
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
                'file'  => $e->getFile(),
            ], 500);
        }
    }
}
