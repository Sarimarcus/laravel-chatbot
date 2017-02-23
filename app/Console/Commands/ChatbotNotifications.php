<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ChatbotNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatbot:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users';

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
     *
     * @return mixed
     */
    public function handle()
    {
        \App\Classes\Notifications::send();
    }
}
