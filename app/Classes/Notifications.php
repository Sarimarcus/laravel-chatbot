<?php

namespace App\Classes;

use App\Models\User;

class Notifications
{

    /*
     * Send notifications to all registered users (yes, we spam them)
     */
    public static function send()
    {
        $accessToken = getenv('PAGE_ACCESS_TOKEN');
        $facebookAPI = new FacebookAPI();

        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', getenv('NOTIFICATION_WEBHOOK'));

        if (200 == $res->getStatusCode()) {
            $content = $res->getBody();

            if(!empty($content)){

                $data = json_decode($content);
                $users = User::all();
                foreach ($users as $user) {
                    // Tease
                    $facebookAPI->send($accessToken, $user->senderId, $data->text, 'plaintext');
                    // Image
                    $facebookAPI->send($accessToken, $user->senderId, self::imageAttachement($data->image), 'formatted');
                    // CTA
                    $facebookAPI->send($accessToken, $user->senderId, self::buttons($data->url), 'formatted');
                }
            }
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
                            'type'                 => 'web_url',
                            'url'                  => $url,
                            'title'                => "\u{1F4F0}" . ' En savoir plus',
                            'webview_height_ratio' => 'compact'
                        ]
                    ]
                ]
            ]
        ];

        return $data;
    }
}
