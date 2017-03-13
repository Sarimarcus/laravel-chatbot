<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Classes\ChatbotAI;
use App\Classes\FacebookAPI;

class ChatbotHelperTest extends TestCase
{
    protected $input;
    protected $chatbotAI;
    protected $facebookAPI;
    
    public function setUp()
    {
        parent::setUp();
        $input = '{
            "object":"page",
            "entry":[{
                "id":"718511138248476","time":1470310180982,
                "messaging":[{
                    "sender":{"id":"1208097179306052"},
                    "recipient":{"id":"718511138248476"},
                    "timestamp":1470310180901,
                    "message":{
                        "mid":"mid.1470310180891:612cfb6aead5fca278",
                        "seq":468,
                        "text":"show me article"
                    }
                }]
            }]
        }';

        $this->input = json_decode($input, true);
        $this->chatbotAI = new ChatbotAI();
        $this->facebookAPI = new FacebookAPI();
        session(array('_token' => 'bqlssH0l4MldiCDraLV0gV4zvqveheaUObZoi4Im'));
    }
    
    public function testGetSenderId()
    {
        $this->assertArrayHasKey("id", $this->input['entry'][0]['messaging'][0]['sender']);
    }
    
    public function testGetMessage()
    {
        $this->assertArrayHasKey("text", $this->input['entry'][0]['messaging'][0]['message']);
    }
    
    public function testGetAnswer()
    {
        $message = $this->input['entry'][0]['messaging'][0]['message']['text'];
        $response = $this->chatbotAI->getApiAIAnswer($message, array(), array());
        $this->assertArrayHasKey("content", $response);
    }
    
    public function testSend()
    {
        $message = $this->input['entry'][0]['messaging'][0]['message']['text'];
        $data = $this->chatbotAI->getApiAIAnswer($message, array(), array());
        $senderId = $this->input['entry'][0]['messaging'][0]['sender']['id'];
        
        $response = $this->facebookAPI->send(getenv('PAGE_ACCESS_TOKEN'), $senderId, $data['content'], $data['type']);
        $this->assertTrue($response);
    }
    
    public function testTypingOn()
    {
        $senderId = $this->input['entry'][0]['messaging'][0]['sender']['id'];
        
        $response = $this->facebookAPI->typingOn(getenv('PAGE_ACCESS_TOKEN'), $senderId);
        $this->assertTrue($response);
    }
    
    public function testGetUserProfile()
    {
        $senderId = $this->input['entry'][0]['messaging'][0]['sender']['id'];
        
        $user = $this->facebookAPI->userProfile(getenv('PAGE_ACCESS_TOKEN'), $senderId);
        $response = json_decode($user, true);
            
        $this->assertNotEmpty($response);
    }
    
    public function testReturnCustomMessage()
    {
        $senderId = $this->input['entry'][0]['messaging'][0]['sender']['id'];
        
        $user = $this->facebookAPI->userProfile(getenv('PAGE_ACCESS_TOKEN'), $senderId);
        $response = json_decode($user, true);
        $this->assertArrayHasKey("first_name", $response);
        
    }
    
}