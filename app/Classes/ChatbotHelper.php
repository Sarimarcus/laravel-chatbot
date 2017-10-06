<?php

namespace App\Classes;

use App\Models\User;

class ChatbotHelper
{

    protected $chatbotAI;
    protected $facebookAPI;

    private $accessToken;

    public $user;

    public function __construct()
    {
        $this->accessToken = config('app.page_access_token');
        $this->chatbotAI   = new ChatbotAI();
        $this->facebookAPI = new FacebookAPI();
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
        return isset($input['entry'][0]['messaging'][0]['sender']['id']) ? $input['entry'][0]['messaging'][0]['sender']['id'] : false;
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
     * Check if the message is a postback
     * @param $input
     * @return bool
     */
    public function isPostback($input)
    {
        return isset($input['postback']);
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
     * @param $content
     * @param $type
     * @param $action
     */
    public function send($content, $type, $action)
    {
        // Apply some custom message
        $content = $this->returnCustomMessage($action, $content);
        return $this->facebookAPI->send($this->accessToken, $this->user['senderId'], $content, $type);
    }

    /**
     * Show typing indicators
     */
    public function typingOn()
    {
        return $this->facebookAPI->typingOn($this->accessToken, $this->user['senderId']);
    }

    /**
     * Get user profile
     * @param $senderId
     */
    public function getUserProfile($senderId)
    {
        if (!isset($this->user)) {
            if ($user = $this->facebookAPI->userProfile($this->accessToken, $senderId)) {
                $data       = json_decode($user, true);
                $this->user = User::updateOrCreate(['senderId' => $senderId], $data);
            }
        }
    }

    /**
     * Return custom message
     * @param $action
     * @param $content
     */
    public function returnCustomMessage($action, $content)
    {
        switch ($action) {

            // Let's be polite and say hello with the firstname
            case 'welcome':

                if (isset($this->user)) {
                    return $content . ' ' . $this->user['first_name'] . ' !';
                } else {
                    return $content;
                }

                break;

            // Let's introduce the chatbot
            case 'get_started':

                if (isset($this->user)) {
                    return $content . ' ' . $this->user['first_name'] . ' ! Je m\'appelle Laura, je peux t\'aider sur plusieurs sujets, comme par exemple te proposer des articles, ou t\'aider à trouver un nouveau look. Qu\'est-ce que tu peux préfères ?';
                } else {
                    return $content;
                }

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
        $hubChallenge   = $request['hub_challenge'];

        if (isset($hubChallenge) && $hubVerifyToken == config('app.webhook_verify_token')) {

            echo $hubChallenge;
        }
    }
}
