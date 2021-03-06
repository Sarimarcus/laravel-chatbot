<?php

namespace App\Classes;

use ApiAi\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ChatbotAI
{

    protected $apiClient;
    protected $foreignExchangerate;
    protected $witClient;

    /**
     * ChatbotAI constructor.
     */
    public function __construct()
    {
        $this->apiClient = new Client(config('app.apiai_token'), null, 'fr');
    }

    /**
     * Get the answer to the user's message with help from api.ai
     * @param string message
     * @return string
     */
    public function getApiAIAnswer($message, $contexts = array(), $originalRequest = array())
    {
        try {

            $data = [
                'query'           => $message,
                'sessionId'       => substr(session('_token'), 0, 36),
                'contexts'        => $contexts,
                'originalRequest' => $originalRequest,
            ];

            Log::info('Sending to API.AI : ' . json_encode($data));

            $query = $this->apiClient->get('query', $data);

            $response = json_decode((string) $query->getBody(), true);

            Log::info('Response from API.AI : ' . json_encode($response));

            // Detecting if there's a Facebook formatted response
            if (isset($response['result']['fulfillment']['data']['facebook'])) {
                return array(
                    'type'    => 'formatted',
                    'content' => $response['result']['fulfillment']['data']['facebook'],
                    'action'  => $response['result']['action'],
                );
            } else {
                return array(
                    'type'    => 'plaintext',
                    'content' => $response['result']['fulfillment']['speech'],
                    'action'  => $response['result']['action'],
                );
            }
        } catch (\Exception $error) {
            Log::warning($error->getMessage());
        }
    }
}
