<?php
namespace App\Notifications;

use App\Channels\FacebookChannel;
use App\Classes\FacebookAPI;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

class PushToUser extends Notification
{

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FacebookChannel::class];
    }

    public function toFacebook($notifiable)
    {
        try {
            $accessToken = config('app.page_access_token');
            $facebookAPI = new FacebookAPI();

            $client = new Client();
            $res = $client->request('GET', config('app.notification_webhook'));
            $content = $res->getBody();

            if (!empty($content)) {
                $data = json_decode($content);
                // Tease
                $facebookAPI->send($accessToken, $notifiable->routeNotificationFor('facebook'), $data->text, 'plaintext');
                // Image
                $facebookAPI->send($accessToken, $notifiable->routeNotificationFor('facebook'), self::imageAttachement($data->image), 'formatted');
                // CTA
                $facebookAPI->send($accessToken, $notifiable->routeNotificationFor('facebook'), self::buttons($data->url), 'formatted');
            }
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            Log::warning('Send Facebook Guzzle error: ' . $responseBodyAsString);
        }
    }
    /*
     * Format for image attachement
     */

    private static function imageAttachement($url)
    {
        $data = [
            'attachment' => [
                'type' => 'image',
                'payload' => [
                    'url' => $url
                ]
            ]
        ];

        return $data;
    }
    /*
     * Format for buttons
     */

    private static function buttons($url)
    {

        $data = [
            'attachment' => [
                'type' => 'template',
                'payload' => [
                    'template_type' => 'button',
                    'text' => 'Que veux-tu faire ?',
                    'buttons' => [
                        [
                            'type' => 'web_url',
                            'url' => $url,
                            'title' => "\u{1F4F0}" . ' En savoir plus',
                            'webview_height_ratio' => 'compact'
                        ]
                    ]
                ]
            ]
        ];

        return $data;
    }
}
