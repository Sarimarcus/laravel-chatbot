<?php

namespace App\Classes;

use Illuminate\Support\Facades\Log;

class FacebookSend
{

    protected $apiUrl = 'https://graph.facebook.com/v2.6/me/messages';
    protected $profileApiUrl = 'https://graph.facebook.com/v2.6/';
    protected $log;
    protected $facebookPrepareData;

    public function __construct()
    {
        $this->facebookPrepareData = new FacebookPrepareData();
    }

    /**
     * @param string $accessToken
     * @param string $senderId
     * @param $content
     * @param $type
     * @internal param string $jsonDataEncoded
     */
    public function send(string $accessToken, string $senderId, $content, $type)
    {

        $jsonDataEncoded = $this->facebookPrepareData->prepare($senderId, $content, $type);

        Log::info('Sending JSON to Facebook : ' . trim($jsonDataEncoded));


        $url = $this->apiUrl . '?access_token=' . $accessToken;
        $ch = curl_init($url);

        // Tell cURL to send POST request.
        curl_setopt($ch, CURLOPT_POST, 1);

        // Attach JSON string to post fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

        // Set the content type
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Execute
        curl_exec($ch);

        if (curl_error($ch)) {
            Log::warning('Send Facebook Curl error: ' . curl_error($ch));
        }

        curl_close($ch);
    }

    /**
     * @param string $accessToken
     * @param string $senderId
     * @internal param string $jsonDataEncoded
     */
    public function typingOn(string $accessToken, string $senderId)
    {

        $jsonDataEncoded = $this->facebookPrepareData->typingOn($senderId);

        Log::info('Sending JSON to Facebook : ' . trim($jsonDataEncoded));


        $url = $this->apiUrl . '?access_token=' . $accessToken;
        $ch = curl_init($url);

        // Tell cURL to send POST request.
        curl_setopt($ch, CURLOPT_POST, 1);

        // Attach JSON string to post fields.
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

        // Set the content type
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        // Execute
        curl_exec($ch);

        if (curl_error($ch)) {
            Log::warning('Send Facebook Curl error: ' . curl_error($ch));
        }

        curl_close($ch);
    }

    /**
     * @param string $accessToken
     * @param string $senderId
     * @internal param string $jsonDataEncoded
     */
    public function userProfile(string $accessToken, string $senderId)
    {

        $url = $this->profileApiUrl . $senderId . '?access_token=' . $accessToken . '&fields=first_name,last_name,profile_pic,locale,timezone,gender';
        $ch = curl_init($url);

        // Execute
        $content = curl_exec($ch);

        if (curl_error($ch)) {
            Log::warning('Send Facebook Curl error: ' . curl_error($ch));
        }

        curl_close($ch);

        Log::info('Getting user info : ' . trim($content));

        return $content;
    }

}
