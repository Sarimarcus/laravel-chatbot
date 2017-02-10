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
                "text":"' . $data . '"
            }';
        // If we have a formatted response
        if('formatted' == $type)
        {
            $message = '"message":
                ' . $data . '
            ';
        }

        $footer = '
        }';

        return $header . $message . $footer;
    }
}
