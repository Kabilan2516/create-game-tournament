<?php
namespace App\Services;

use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Models\User;

class FcmService
{
    public static function send(User $user, string $title, string $body, array $data = [])
    {
        $messaging = Firebase::messaging();

        foreach ($user->fcmTokens as $token) {
            $message = [
                'token' => $token->token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $data,
            ];

            $messaging->send($message);
        }
    }
}
