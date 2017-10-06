<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\FacebookAPI;

class ChatbotSetGetStartedButton extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatbot:set-get-started-button';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the payload of the Get Started Button';

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
     * See : https://developers.facebook.com/docs/messenger-platform/messenger-profile/get-started-button
     * @return mixed
     */
    public function handle()
    {
        $payload = $this->ask('Please input the payload');
        $accessToken = config('app.page_access_token');
        $facebookAPI = new FacebookAPI();
        $facebookAPI->setGetStartedButton($accessToken, $payload);
    }
}
