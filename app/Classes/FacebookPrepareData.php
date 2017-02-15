<?php

namespace App\Classes;

class FacebookPrepareData
{

    /**
     * Create JSON data for the play to facebook
     * @param $senderId
     * @param $content
     * @param $type
     * @return string
     */
    public function prepare($senderId, $content, $type)
    {
        $header =  '{"recipient":{"id":"' . $senderId . '"},';

        // Just a plain text response
        if('plaintext' == $type)
        {
            $message = '"message":{"text":"' . $content . '"}';
        // If we have a formatted response
        }elseif('formatted' == $type)
        {
            $message = '"message": ' . json_encode($content, JSON_UNESCAPED_SLASHES);
        }

        $footer = '}';

        return $header . $message . $footer;
    }

    /*
     * Typing on while waiting for API.AI
     */
    public function typingOn($senderId)
    {
        $header =  '{"recipient":{"id":"' . $senderId . '"},';
        $message = '"sender_action":{"typing_on"}';
        $footer = '}';

        return $header . $message . $footer;
    }

}
