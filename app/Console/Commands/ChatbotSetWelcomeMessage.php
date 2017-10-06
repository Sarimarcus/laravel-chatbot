<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\FacebookAPI;

class ChatbotSetWelcomeMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatbot:set-welcome-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the welcome message of the chatbot';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * See : https://developers.facebook.com/docs/messenger-platform/messenger-profile/greeting-text
     * @return mixed
     */
    public function handle()
    {
        $message = $this->ask('Please input the greetings message');
        $accessToken = config('app.page_access_token');
        $facebookAPI = new FacebookAPI();
        $facebookAPI->setWelcomeMessage($accessToken, $message);
    }
}
