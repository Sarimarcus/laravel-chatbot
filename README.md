## Messenger Platform

https://developers.facebook.com/docs/messenger-platform

Add domain to whitelist :

```
curl -X POST -H "Content-Type: application/json" -d '{
  "setting_type" : "domain_whitelisting",
  "whitelisted_domains" : ["http://DOMAIN"],
  "domain_action_type": "add"
}' "https://graph.facebook.com/v2.6/me/thread_settings?access_token=PAGE_ACCESS_TOKEN"
```

Set greetings message :

```
curl -X POST -H "Content-Type: application/json" -d '{
  "greeting":[
    {
      "locale":"default",
      "text":"Hello!"
    }, {
      "locale":"en_US",
      "text":"Timeless apparel for the masses."
    }
  ]
}' "https://graph.facebook.com/v2.6/me/messenger_profile?access_token=PAGE_ACCESS_TOKEN"
```

Elements limits

Title: 80 characters

Subtitle: 80 characters

Call-to-action title: 20 characters

Call-to-action items: 3 buttons

Bubbles per message (horizontal scroll): 10 elements

## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
