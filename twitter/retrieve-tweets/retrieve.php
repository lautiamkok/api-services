<!DOCTYPE html>
<html>
    <head>
        <title>Tweets</title>
        <meta charset="UTF-8">
    </head>
    <body>

<?php
require_once __DIR__ . '/vendor/autoload.php';

// Set twitter screen name that you want to pull the tweet from.
$screen_name = "MetricLife";
$screen_name = "lau_tiamkok";
$screen_name = "TwitterDev";

// Enter Your Tokens.
// Set access tokens here - see: https://dev.twitter.com/apps/
$settings = array(
    'oauth_access_token' => "35757833-1TfPkc2TGTeCwAJ0ZVny0u7fvNHYgmj8ol7bKx32I",
    'oauth_access_token_secret' => "nfPqG2KV3Z4C99a0fEEo1bGmQfVWMygoRpYD9zfWxfgmu",
    'consumer_key' => "6OrXPJcQkxUKSjbyWabwNGn1X",
    'consumer_secret' => "rRBvi7r6OTaqqzrRsGwQqPM1swqfADDRjoysn6XSSYVzOQn78j"
);

// We need to give the PHP Twitter wrapper the URL so it can make the correct
// API request for us. In order to do this, we will need to create a string with
// the URL in it. Let’s call that string $url:
$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

// So we’ve given the Twitter wrapper our access tokens and the URL for the API
// call- now we need to tell it we want to use the GET method. To do this, we
// set another variable so that the PHP Twitter wrapper can make the correct
// request. Let’s call that string $requestMethod:
$requestMethod = "GET";

// We also need to set the GET information too. We could append that to the URL,
// but for the PHP Twitter Wrapper, we are going to add that information
// separately. I recommend looking through each Twitter API resource in the
// documentation as each resource have different parameters you can add. For the
// user_timeline one, we could add screen_name and count.
$getfield = '?screen_name=' . $screen_name . '&count=10';

// Now we need to make the call using the PHP Twitter Wrapper. To do this and to
// output it, we evoke the TwitterAPIExchange class with the access tokens and
// give it all the extra bits of information ($getField, $url and
// $requestMethod)
$twitter = new TwitterAPIExchange($settings);
$tweets = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();

$tweets_stripslashes = urldecode(stripslashes($tweets));
// echo $tweets_stripslashes;

// Do Stuff with the Data
// Instead of outputting the JSON string, lets convert it to an associative array using the json_decode function:
$string = json_decode($tweets, $assoc = TRUE);

if (isset($string["errors"]) && $string["errors"][0]["message"] != "") {
    echo "<h3>Sorry, there was a problem.</h3>
          <p>Twitter returned the following error message:</p>
          <p><em>" . $string[errors][0]["message"] . "</em></p>";
    exit();
}

// var_dump($string);
// echo "<pre>";
// print_r($string);
// echo "</pre>";

foreach($string as $items) {
    echo "Time and Date of Tweet: " . $items['created_at'] . "<br />";
    // echo "Tweet: " . utf8_decode($items['text']) . "<br />";
    // echo "Tweet: ". htmlentities($items['text'], ENT_NOQUOTES, 'UTF-8') . "<br />";
    // echo "Tweet: ". linkify_tweet($items['text']) . "<br />";
    echo "Tweet: ". $items['text'] . "<br />";
    echo "Tweet ID: ". $items['id'] . "<br />";
    echo "Tweet URL: https://twitter.com/muslimgirl/status/". $items['id'] . "<br />";
    echo "Tweeted by: ". $items['user']['name']."<br />";
    echo "Screen name: ". $items['user']['screen_name']."<br />";
    echo "Followers: ". $items['user']['followers_count']."<br />";
    echo "Friends: ". $items['user']['friends_count']."<br />";
    echo "Listed: ". $items['user']['listed_count']."<br /><br />";
}


function linkify_tweet($tweet) {

  //Convert urls to <a> links
  $tweet = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet);

  //Convert hashtags to twitter searches in <a> links
  $tweet = preg_replace("/#([A-Za-z0-9\/\.]*)/", "<a target=\"_new\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet);

  //Convert attags to twitter profiles in <a> links
  $tweet = preg_replace("/@([A-Za-z0-9\/\.]*)/", "<a href=\"http://www.twitter.com/$1\">@$1</a>", $tweet);

  return $tweet;
}
?>

</body>
</html>
