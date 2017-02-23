<?php

namespace App\Classes;

use App\Models\User;

class Notifications
{

    /*
     * Send notifications to all registered users (yes, we spam them)
     */
    public static function send()
    {
        $accessToken = getenv('PAGE_ACCESS_TOKEN');
        $content = "Test d'un envoi de notification. " . "\u{1F30F}";

        $users = User::all();
        foreach ($users as $user) {
            new FacebookAPI()->send($this->accessToken, $user->senderId, $content, 'plaintext');
        }
    }
}
