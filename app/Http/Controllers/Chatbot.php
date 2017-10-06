<?php

namespace App\Http\Controllers;

use App\Classes\ChatbotHelper;
use Illuminate\Support\Facades\Log;

class Chatbot extends Controller
{

    protected $chatbotHelper;

    public function __construct()
    {
        $this->chatbotHelper = new ChatbotHelper();
    }

    public function index()
    {
        // Facebook webhook verification
        $this->chatbotHelper->verifyWebhook($_REQUEST);

        // Get the fb users data
        $input = json_decode(file_get_contents('php://input'), true);

        // OK, at this step, we can have a postback from Facebook or some content from the user
        // Let's handle this
        if ($this->chatbotHelper->isMessage($input)) {
            $this->handleMessage($input);
        } elseif ($this->chatbotHelper->isPostback($input)) {
            $this->handlePostback($input);
        }
    }

    /*
     * Handling a user message
     */
    private function handleMessage($input)
    {
        // Handling user
        $senderId = $this->chatbotHelper->getSenderId($input);
        $this->chatbotHelper->getUserProfile($senderId);

        // Check if there's a payload
        if ($this->chatbotHelper->isQuickReplyPayload($input)) {
            // I know, it's dirty
            $input['entry'][0]['messaging'][0]['message']['text'] = $input['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];
        }

        // Get the user's message
        $message = $this->chatbotHelper->getMessage($input);

        Log::info('User sending message : ' . trim($message));

        // Show typing indicators
        $this->chatbotHelper->typingOn();

        // API.AI call
        $data = $this->chatbotHelper->getAnswer($message, 'apiai');

        // Send the answer back to the Facebook chat
        $this->chatbotHelper->send($data['content'], $data['type'], $data['action']);
    }

    /*
     * Handling a Facebook Postback
     */
    private function handlePostback($input)
    {
        Log::info('Facebook Postback : ' . $input['postback']['payload']);

        // Handling user
        $this->chatbotHelper->getUserProfile($input['sender']['id']);

        // API.AI call
        $data = $this->chatbotHelper->getAnswer($input['postback']['payload'], 'apiai');

        // Send the answer back to the Facebook chat
        $this->chatbotHelper->send($data['content'], $data['type'], $data['action']);
    }
}
