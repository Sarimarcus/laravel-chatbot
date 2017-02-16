<?php

namespace App\Classes;


use ApiAi\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ChatbotAI
{

    protected $apiClient;
    protected $config;
    protected $foreignExchangerate;
    protected $witClient;

    /**
     * ChatbotAI constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->apiClient = new Client($this->config['apiai_token'], null, 'fr');
        //$this->witClient = new \Tgallice\Wit\Client($this->config['witai_token']);
        //$this->foreignExchangerate = new ForeignExchangeRate();
    }

    /**
     * Get the answer to the user's message
     * @param $message
     * @return string
     */
    public function getAnswer(string $message)
    {
        // Simple example returning the user's message
        return 'Define your own logic to reply to this message: ' . $message;

        // Do whatever you like to analyze the message
        // Example:
        // if(preg_match('[hi|hey|hello]', strtolower($message))) {
        // return 'Hi, nice to meet you!';
        // }
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
                'sessionId'       => substr(session('_token'),0 , 36),
                'contexts'        => $contexts,
                'originalRequest' => $originalRequest
            ];

            Log::info('Sending to API.AI : ' . json_encode($data));

            $query = $this->apiClient->get('query', $data);

            $response = json_decode((string)$query->getBody(), true);

            Log::info('Response from API.AI : ' . json_encode($response));

            // Detecting if there's a Facebook formatted response
            if(isset($response['result']['fulfillment']['data']['facebook'])){
                return array(
                    'type'    => 'formatted',
                    'content' => $response['result']['fulfillment']['data']['facebook'],
                    'action'  => $response['result']['action']
                );
            } else {
                return array(
                    'type'    => 'plaintext',
                    'content' => $response['result']['fulfillment']['speech'],
                    'action'  => $response['result']['action']
                );
            }
        } catch (\Exception $error) {
            Log::warning($error->getMessage());
        }
    }

    /**
     * Get the answer to the user's message with help from wit.ai
     * @param $message
     * @return string
     */
    public function getWitAIAnswer($message)
    {
        try {

            $response = $this->witClient->get('/message', [
                'q' => $message,
            ]);

            // Get the decoded body
            $response = json_decode((string)$response->getBody(), true);
            $intent = $response['entities']['intent'][0]['value'] ?? 'no intent recognized';
        } catch (\Exception $error) {
            Log::warning($error->getMessage());
        }

        return 'The intent of the message: ' . $intent;
    }

    /**
     * Get the foreign rates based on the users base (EUR, USD...)
     * @param $message
     * @return string
     */
    public function getForeignExchangeRateAnswer($message)
    {
        return $this->foreignExchangerate->getRates($message);
    }


}