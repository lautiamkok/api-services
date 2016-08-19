# Twitter API

https://dev.twitter.com/

# Tutorials

http://iag.me/socialmedia/build-your-first-twitter-app-using-php-in-8-easy-steps/
http://stackoverflow.com/questions/12916539/simplest-php-example-for-retrieving-user-timeline-with-twitter-api-version-1-1
https://www.madebymagnitude.com/blog/displaying-latest-tweets-using-the-twitter-api-v11-in-php/

## Wordpress

http://code.tutsplus.com/tutorials/create-a-twitter-widget-with-the-latest-twitter-api--cms-21535

# App & Credentials

https://apps.twitter.com/

App name: Lau Tiam Kok Tweets
Consumer Key (API Key)  6OrXPJcQkxUKSjbyWabwNGn1X
Consumer Secret (API Secret)  rRBvi7r6OTaqqzrRsGwQqPM1swqfADDRjoysn6XSSYVzOQn78j
Access Token  35757833-1TfPkc2TGTeCwAJ0ZVny0u7fvNHYgmj8ol7bKx32I
Access Token Secret nfPqG2KV3Z4C99a0fEEo1bGmQfVWMygoRpYD9zfWxfgmu

# Youtube Channel Config

https://www.youtube.com/account_advanced
https://www.youtube.com/upload_defaults

# Retrieve tweets using j7mbo/TwitterAPIExchange

1. Get a simple “PHP Wrapper” for calls to Twitter’s API v1.1. With this wrapper, you can more easily make calls to the API and then interact with the data. https://github.com/J7mbo/twitter-api-php

    **Using Composer**

    You can install the library by adding it as a dependency to your composer.json.

    "require": {
        "j7mbo/twitter-api-php": "dev-master"
    }

2. Create a PHP file with Composer autoloader at the beginning:

    require_once __DIR__ . '/vendor/autoload.php';

3. Enter your tokens and do stuff!

    ```
    $settings = array(
        'oauth_access_token' => "35757833-1TfPkc2TGTeCwAJ0ZVny0u7fvNHYgmj8ol7bKx32I",
        'oauth_access_token_secret' => "nfPqG2KV3Z4C99a0fEEo1bGmQfVWMygoRpYD9zfWxfgmu",
        'consumer_key' => "6OrXPJcQkxUKSjbyWabwNGn1X",
        'consumer_secret' => "rRBvi7r6OTaqqzrRsGwQqPM1swqfADDRjoysn6XSSYVzOQn78j"
    );
    ...
    ...
    ```

# Retrieve tweets using PHP cURL

    ```
    function buildBaseString($baseURI, $method, $params) {
        $r = array();
        ksort($params);
        foreach($params as $key=>$value){
            $r[] = "$key=" . rawurlencode($value);
        }
        return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
    }

    function buildAuthorizationHeader($oauth) {
        $r = 'Authorization: OAuth ';
        $values = array();
        foreach($oauth as $key=>$value)
            $values[] = "$key=\"" . rawurlencode($value) . "\"";
        $r .= implode(', ', $values);
        return $r;
    }

    $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

    $oauth_access_token = "YOURVALUE";
    $oauth_access_token_secret = "YOURVALUE";
    $consumer_key = "YOURVALUE";
    $consumer_secret = "YOURVALUE";

    $oauth = array( 'oauth_consumer_key' => $consumer_key,
                    'oauth_nonce' => time(),
                    'oauth_signature_method' => 'HMAC-SHA1',
                    'oauth_token' => $oauth_access_token,
                    'oauth_timestamp' => time(),
                    'oauth_version' => '1.0');

    $base_info = buildBaseString($url, 'GET', $oauth);
    $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;

    // Make requests
    $header = array(buildAuthorizationHeader($oauth), 'Expect:');
    $options = array( CURLOPT_HTTPHEADER => $header,
                      //CURLOPT_POSTFIELDS => $postfields,
                      CURLOPT_HEADER => false,
                      CURLOPT_URL => $url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_SSL_VERIFYPEER => false);

    $feed = curl_init();
    curl_setopt_array($feed, $options);
    $json = curl_exec($feed);
    curl_close($feed);

    $twitter_data = json_decode($json);

    //print it out
    print_r ($twitter_data);

    ```

@ref: http://stackoverflow.com/questions/12916539/simplest-php-example-for-retrieving-user-timeline-with-twitter-api-version-1-1

# Convert tweet hashtags, at-tags and urls to links

@ref:
https://www.jacobtomlinson.co.uk/2014/01/22/convert-tweet-hashtags-at-tags-and-urls-to-links-with-php-and-regular-expressions/
http://stackoverflow.com/questions/4289716/turn-urls-into-links-when-showing-the-latest-tweet-from-a-twitter-account-with-p
http://stackoverflow.com/questions/11533214/php-how-to-use-the-twitter-apis-data-to-convert-urls-mentions-and-hastags-in
http://stackoverflow.com/questions/3799440/php-twitter-replace-link-and-hashtag-for-real-link
http://www.webtipblog.com/add-links-to-twitter-mentions-hashtags-and-urls-with-php-and-the-twitter-1-1-oauth-api/
https://davidwalsh.name/linkify-twitter-feed
