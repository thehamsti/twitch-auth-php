# Twitch Auth Example - PHP

Super simple, slightly messy (threw it together for someone) example of how to retrieve oauth token from Twitch

## Setup

1. Setup MySQL database to save information in.
2. Setup someway to host a php file to the web (I recommend Nginx)
3. Register your app on Twitch - https://dev.twitch.tv/docs/authentication#registration (Save your client_id, client_secret, and redirect url here)
4. Open `public/index.php` and:
   1. Replace database information with your own
   2. Replace client_id, client_secret and the redirect url
   3. Setup scopes for whatever you want permission to do

## Questions?
Twitter - https://twitter.com/thecuriouseng
Email - jack@jackc.io
