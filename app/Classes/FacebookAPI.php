<?php
namespace App\Classes;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class FacebookAPI
{

    protected $apiUrl = 'https://graph.facebook.com/v2.8/me/messages';
    protected $profileApiUrl = 'https://graph.facebook.com/v2.8/';
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

        try{
            $client = new Client([
                'headers' => ['Content-Type' => 'application/json']
            ]);
            $res = $client->request('POST', $url, ['body' => $jsonDataEncoded]);
            echo $res->getBody();
            return true;
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            Log::warning('Send Facebook Guzzle error: ' . $responseBodyAsString);
            return false;
        }
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
        
        try{
            $client = new Client([
                'headers' => ['Content-Type' => 'application/json']
            ]);
            $res = $client->request('POST', $url, ['body' => $jsonDataEncoded]);
            echo $res->getBody();
            return true;
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            Log::warning('Send Facebook Guzzle error: ' . $responseBodyAsString);
            return false;
        }
    }

    /**
     * @param string $accessToken
     * @param string $senderId
     * @internal param string $jsonDataEncoded
     */
    public function userProfile(string $accessToken, string $senderId)
    {
        $url = $this->profileApiUrl . $senderId . '?access_token=' . $accessToken . '&fields=first_name,last_name,profile_pic,locale,timezone,gender';
       
        try{
            $client = new Client();
            $res = $client->request('GET', $url);
            $content = $res->getBody();
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            Log::warning('Send Facebook Guzzle error: ' . $responseBodyAsString);
        }
        
        Log::info('Getting user info : ' . trim($content));

        return $content;
    }
}
