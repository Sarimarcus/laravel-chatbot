<?php

namespace App\Classes;


use Dotenv\Dotenv;

class ChatbotHelper
{

    protected $chatbotAI;
    protected $facebookSend;
    protected $log;

    private $accessToken;

    public $config;
    public $user;

    public function __construct()
    {
        $this->accessToken = getenv('PAGE_ACCESS_TOKEN');
        $this->config = include('config.php');
        $this->chatbotAI = new ChatbotAI($this->config);
        $this->facebookSend = new FacebookSend();
    }

    public function __isset($property)
    {
        return isset($this->$property);
    }

    /**
     * Get the sender id of the message
     * @param $input
     * @return mixed
     */
    public function getSenderId($input)
    {
        return $input['entry'][0]['messaging'][0]['sender']['id'];
    }

    /**
     * Get the user's message from input
     * @param $input
     * @return mixed
     */
    public function getMessage($input)
    {
        return $input['entry'][0]['messaging'][0]['message']['text'];
    }

    /**
     * Check if the callback is a user message
     * @param $input
     * @return bool
     */
    public function isMessage($input)
    {
        return isset($input['entry'][0]['messaging'][0]['message']['text']) && !isset
        ($input['entry'][0]['messaging'][0]['message']['is_echo']);

    }

    /**
     * Check if the callback is a quick reply payload
     * @param $input
     * @return bool
     */
    public function isQuickReplyPayload($input)
    {
        return isset($input['entry'][0]['messaging'][0]['message']['quick_reply']['payload']);
    }

    /**
     * Get the answer to a given user's message
     * @param null $api
     * @param string $message
     * @return string
     */
    public function getAnswer($message, $api = null)
    {

        if ($api === 'apiai') {
            return $this->chatbotAI->getApiAIAnswer($message, $this->setContexts(), $this->setOriginalRequest());
        } elseif ($api === 'witai') {
            return $this->chatbotAI->getWitAIAnswer($message);
        } elseif ($api === 'rates') {
            return $this->chatbotAI->getForeignExchangeRateAnswer($message);
        } else {
            return $this->chatbotAI->getAnswer($message);
        }

    }

    /**
     * Send a reply back to Facebook chat
     * @param $senderId
     * @param $content
     * @param $type
     */
    public function send($senderId, $content, $type, $action)
    {
        // Apply some custom message
        $content = $this->returnCustomMessage($action, $content);
        return $this->facebookSend->send($this->accessToken, $senderId, $content, $type);
    }

    /**
     * Show typing indicators
     * @param $senderId
     */
    public function typingOn($senderId)
    {
        return $this->facebookSend->typingOn($this->accessToken, $senderId);
    }

    /**
     * Get user profile
     * @param $senderId
     */
    public function getUserProfile($senderId)
    {
        if(!isset($this->user)){
            $user = $this->facebookSend->userProfile($this->accessToken, $senderId);
            $this->user = json_decode($user, true);
        }
    }

    /**
     * Return custom message
     * @param $message
     */
    public function returnCustomMessage($action, $content)
    {
        switch ($action) {

            // Let's be polite and say hello with the firstname
            case 'input.welcome':
            case 'smalltalk.greetings':
                if (isset($this->user)) $firstname = $this->user->first_name;
                return $content . $firstname . ' !';
                break;

            default:
                return $content;
                break;
        }
    }

    /**
     * Set Contexts Parameters
     */
    public function setContexts()
    {
        // Set contexts
        $contexts = array();

        // User
        // if (isset($this->user)) {
        //     foreach ($this->user as $key => $value) {
        //         $parameters[$key] = $value;
        //     }
        // }

        // $contexts[] = array(
        //     'name' => 'user',
        //     'parameters' => $parameters);

        return $contexts;
    }

      /**
     * Set originalRequest data
     * @param $message
     */
    public function setOriginalRequest()
    {
        // Set originalRequest
        $originalRequest = array();

        // User
        // if (isset($this->user)) {
        //     foreach ($this->user as $key => $value) {
        //         $data[$key] = $value;
        //     }
        // }

        // $originalRequest[] = array('data' => $data);

        return $originalRequest;
    }

    /**
     * Verify Facebook webhook
     * This is only needed when you setup or change the webhook
     * @param $request
     * @return mixed
     */
    public function verifyWebhook($request)
    {
        if (!isset($request['hub_challenge'])) {
            return false;
        };

        $hubVerifyToken = null;
        $hubVerifyToken = $request['hub_verify_token'];
        $hubChallenge = $request['hub_challenge'];

        if (isset($hubChallenge) && $hubVerifyToken == $this->config['webhook_verify_token']) {

            echo $hubChallenge;
        }
    }
}