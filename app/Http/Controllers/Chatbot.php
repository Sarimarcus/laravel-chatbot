<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Classes\ChatbotHelper;

class Chatbot extends Controller
{
    public function index()
    {
        // Create the chatbot helper instance
        $chatbotHelper = new ChatbotHelper();

        // Facebook webhook verification
        $chatbotHelper->verifyWebhook($_REQUEST);

        // Get the fb users data
        $input = json_decode(file_get_contents('php://input'), true);

        $senderId = $chatbotHelper->getSenderId($input);

        if ($senderId && $chatbotHelper->isMessage($input)) {

            // Check if there's a payload
            if($chatbotHelper->isQuickReplyPayload($input)){
                // I know, it's dirty
                $input['entry'][0]['messaging'][0]['message']['text'] = $input['entry'][0]['messaging'][0]['message']['quick_reply']['payload'];
            }

            // Get the user's message
            $message = $chatbotHelper->getMessage($input);

            Log::info('Sending message : ' . trim($message));

            // Show typing indicators
            $chatbotHelper->typingOn($senderId);

            // API.AI call
            $data = $chatbotHelper->getAnswer($message, 'apiai');

            // Send the answer back to the Facebook chat
            $chatbotHelper->send($senderId, $data['content'], $data['type'], $data['action']);
        }
    }
}
