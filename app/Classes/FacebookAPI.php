<?php
namespace App\Classes;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Log;

class FacebookAPI
{

    protected $messagesApiUrl = 'https://graph.facebook.com/v2.8/me/messages';
    protected $graphApiUrl    = 'https://graph.facebook.com/v2.8/';
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
        $jsonDataEncoded = $this->facebookPrepareData->send($senderId, $content, $type);

        Log::info('Sending JSON to Facebook : ' . trim($jsonDataEncoded));

        $url = $this->messagesApiUrl . '?access_token=' . $accessToken;

        try {
            $client = new Client([
                'headers' => ['Content-Type' => 'application/json'],
            ]);
            $res = $client->request('POST', $url, ['body' => $jsonDataEncoded]);
            echo $res->getBody();
            return true;
        } catch (BadResponseException $e) {
            $response             = $e->getResponse();
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

        $url = $this->messagesApiUrl . '?access_token=' . $accessToken;

        try {
            $client = new Client([
                'headers' => ['Content-Type' => 'application/json'],
            ]);
            $res = $client->request('POST', $url, ['body' => $jsonDataEncoded]);
            echo $res->getBody();
            return true;
        } catch (BadResponseException $e) {
            $response             = $e->getResponse();
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
        $url = $this->graphApiUrl . $senderId . '?access_token=' . $accessToken . '&fields=first_name,last_name,profile_pic,locale,timezone,gender';

        try {
            $client  = new Client();
            $res     = $client->request('GET', $url);
            $content = $res->getBody();
        } catch (BadResponseException $e) {
            $response             = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            Log::warning('Send Facebook Guzzle error: ' . $responseBodyAsString);
        }

        if (isset($content)) {
            Log::info('Getting user info : ' . trim($content));
            return $content;
        } else {
            return false;
        }
    }

    /*
     * Set the welcome message of the bot
     * @param $message
     * @return boolean
     */
    public function setWelcomeMessage(string $accessToken, string $message)
    {
        $url = $this->graphApiUrl . 'me/messenger_profile?access_token=' . $accessToken;

        try {
            $client = new Client([
                'headers' => ['Content-Type' => 'application/json'],
            ]);

            $jsonDataEncoded = $this->facebookPrepareData->greetingsMessage($message);

            $res = $client->request('POST', $url, ['body' => $jsonDataEncoded]);
            echo $res->getBody();
            return true;
        } catch (BadResponseException $e) {
            $response             = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            Log::warning('Send Facebook Guzzle error: ' . $responseBodyAsString);
            return false;
        }
    }

    /*
     * Add a domain to the whitelist
     * @param $domain
     * @return boolean
     */
    public function addDomainWhitelist(string $accessToken, string $domain)
    {
        $url = $this->graphApiUrl . 'me/thread_settings?access_token=' . $accessToken;

        try {
            $client = new Client([
                'headers' => ['Content-Type' => 'application/json'],
            ]);

            $jsonDataEncoded = $this->facebookPrepareData->domainWhitelist($domain);

            $res = $client->request('POST', $url, ['body' => $jsonDataEncoded]);
            echo $res->getBody();
            return true;
        } catch (BadResponseException $e) {
            $response             = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            Log::warning('Send Facebook Guzzle error: ' . $responseBodyAsString);
            return false;
        }
    }

    /*
     * Set the Get Started Button payload
     * @param $payload
     * @return boolean
     */
    public function setGetStartedButton(string $accessToken, string $payload)
    {
        $url = $this->graphApiUrl . 'me/messenger_profile?access_token=' . $accessToken;

        try {
            $client = new Client([
                'headers' => ['Content-Type' => 'application/json'],
            ]);

            $jsonDataEncoded = $this->facebookPrepareData->getStartedButton($payload);

            $res = $client->request('POST', $url, ['body' => $jsonDataEncoded]);
            echo $res->getBody();
            return true;
        } catch (BadResponseException $e) {
            $response             = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            Log::warning('Send Facebook Guzzle error: ' . $responseBodyAsString);
            return false;
        }
    }
}
