## Messenger Platform

https://developers.facebook.com/docs/messenger-platform

Add domain to whitelist :

```
curl -X POST -H "Content-Type: application/json" -d '{
  "setting_type" : "domain_whitelisting",
  "whitelisted_domains" : ["http://www.get-the-look.fr"],
  "domain_action_type": "add"
}' "https://graph.facebook.com/v2.6/me/thread_settings?access_token=PAGE_ACCESS_TOKEN"


Elements limits

Title: 80 characters

Subtitle: 80 characters

Call-to-action title: 20 characters

Call-to-action items: 3 buttons

Bubbles per message (horizontal scroll): 10 elements

```
## License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
