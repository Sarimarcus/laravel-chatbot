<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class FacebookChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $notification->toFacebook($notifiable);
    }
}