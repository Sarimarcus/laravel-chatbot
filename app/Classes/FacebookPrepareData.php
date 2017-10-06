<?php

namespace App\Classes;

class FacebookPrepareData
{

    /**
     * Create JSON data for the play to facebook
     * See : https://developers.facebook.com/docs/messenger-platform/send-api-reference
     * @param $senderId
     * @param $content
     * @param $type
     * @return string
     */
    public function send($senderId, $content, $type)
    {
        // Just a plain text response
        if ('plaintext' == $type) {
            return view('json-facebook/send-plaintext', ['content' => $content, 'senderId' => $senderId])->render();
            // If we have a formatted response
        } elseif ('formatted' == $type) {
            return view('json-facebook/send-formatted', ['content' => json_encode($content, JSON_UNESCAPED_SLASHES), 'senderId' => $senderId])->render();
        }
    }

    /*
     * Typing on while waiting for API.AI
     */
    public function typingOn($senderId)
    {
        return view('json-facebook/typing-on', ['senderId' => $senderId])->render();
    }

    /*
     * Grettings message (only handling default for now)
     */
    public function greetingsMessage($message)
    {
        return view('json-facebook/greeting', ['message' => $message])->render();
    }

    /*
     * Add a domain to whitelist
     */
    public function domainWhitelist($domain)
    {
        return view('json-facebook/domain-white-list', ['domain' => $domain])->render();
    }

    /*
     * Set the Get Started Button payload
     */
    public function getStartedButton($payload)
    {
        return view('json-facebook/get-started', ['payload' => $payload])->render();
    }
}
