<?php

namespace App\Classes;

use App\Models\User;

class Notifications
{

    protected $facebookAPI;

    private $accessToken;

    public $config;
    public $user;

    public function __construct()
    {
        $this->accessToken  = getenv('PAGE_ACCESS_TOKEN');
        $this->facebookAPI = new FacebookAPI();
    }

    /*
     * Send notifications to all registered users (yes, we spam them)
     */
    public function send()
    {
        $content = "Test d'un envoi de notification. " . "\u{1F30F}";

        $users = User::all();
        foreach ($users as $user) {
            $this->facebookAPI->send($this->accessToken, $user->senderId, $content, 'plaintext');
        }
    }
}
