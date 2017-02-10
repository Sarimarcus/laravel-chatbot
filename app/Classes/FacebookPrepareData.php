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

        $header =  '{
            "recipient":{
                "id":"' . $senderId . '"
            },';


        // Just a plain text response
        if('plaintext' == $type)
        {
            $message = '"message":{
                "text":"' . $content . '"
            }';
        // If we have a formatted response
        }elseif('formatted' == $type)
        {
            $message = '"message": ' . json_encode($content);
        }

        $footer = '
        }';

        return $header . $message . $footer;
    }
}
