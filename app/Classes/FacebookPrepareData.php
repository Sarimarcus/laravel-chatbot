<?php

namespace App\Classes;

class FacebookPrepareData
{

    /**
     * Create JSON data for the play to facebook
     * @param $senderId
     * @param $data
     * @return string
     */
    public function prepare($senderId, $data)
    {

        $header =  '{
            "recipient":{
                "id":"' . $senderId . '"
            },';


        // Just a plain text response
        if(is_string($data))
        {
            $message = '"message":{
                "text":"' . $data . '"
            }';
        } else if(is_array($data))
        {
            $message = '"message":{
                "attachement":"' . json_encode($data) . '"
            }';
        }

        $footer = '
        }';

        return $header . $message . $footer;
    }
}
