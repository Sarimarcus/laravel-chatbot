<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\FacebookAPI;

class ChatbotAddDomainWhitelist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatbot:add-domain-whitelist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a domain to the whitelisted list';

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
     * See : https://developers.facebook.com/docs/messenger-platform/thread-settings/domain-whitelisting
     * @return mixed
     */
    public function handle()
    {
        $domain = $this->ask('Please input the domain to whitelist (with http(s)://)');
        $accessToken = config('app.page_access_token');
        $facebookAPI = new FacebookAPI();
        $facebookAPI->AddDomainWhitelist($accessToken, $domain);
    }
}
