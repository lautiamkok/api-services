<?php
$tokenFile = 'token.txt';
$tokenContent = file_get_contents($tokenFile);

// Load 'Google/Client.php' and 'Google/Service/YouTube.php' with composer.
require_once __DIR__ . '/vendor/autoload.php';
session_start();

/*
 * You can acquire an OAuth 2.0 client ID and client secret from the
 * Google Developers Console <https://console.developers.google.com/>
 * For more information about using OAuth 2.0 to access Google APIs, please see:
 * <https://developers.google.com/youtube/v3/guides/authentication>
 * Please ensure that you have enabled the YouTube Data API for your project.
 */
$OAUTH2_CLIENT_ID = '181476429957-h9c057uvk8di1g3tl0ikoq0kcaci228u.apps.googleusercontent.com';
$OAUTH2_CLIENT_SECRET = 'LeAsNpveuZQrS3GC4LDi5Zd3';

// Client init
$client = new Google_Client();
$client->setClientId($OAUTH2_CLIENT_ID);
$client->setAccessType('offline');
$client->setApprovalPrompt('force');
$client->setAccessToken($tokenContent);
$client->setClientSecret($OAUTH2_CLIENT_SECRET);

$htmlBody = '';

// Check to ensure that the access token was successfully acquired.
if ($client->getAccessToken()) {
  try {

    /**
     * Check to see if our access token has expired. If so, get a new one and save it to file for future use.
     */
    if($client->isAccessTokenExpired()) {
        $newToken = json_decode($client->getAccessToken());
        $client->refreshToken($newToken->refresh_token);
        file_put_contents($tokenFile, $client->getAccessToken());
    }

    // Define an object that will be used to make all API requests.
    $youtube = new Google_Service_YouTube($client);

    // Call the channels.list method to retrieve information about the
    // currently authenticated user's channel.
    $channelsResponse = $youtube->channels->listChannels('contentDetails', array(
      'mine' => 'true',
    ));

    foreach ($channelsResponse['items'] as $channel) {
      // Extract the unique playlist ID that identifies the list of videos
      // uploaded to the channel, and then call the playlistItems.list method
      // to retrieve that list.
      $uploadsListId = $channel['contentDetails']['relatedPlaylists']['uploads'];

      $playlistItemsResponse = $youtube->playlistItems->listPlaylistItems('snippet', array(
        'playlistId' => $uploadsListId,
        'maxResults' => 50
      ));

      // print_r($playlistItemsResponse);

      $htmlBody .= "<h3>Videos in list $uploadsListId</h3><ul>";
      foreach ($playlistItemsResponse['items'] as $playlistItem) {
        $htmlBody .= sprintf('<li>%s (%s)</li>', $playlistItem['snippet']['title'],
          $playlistItem['snippet']['resourceId']['videoId']);
      }
      $htmlBody .= '</ul>';
    }
  } catch (Google_Service_Exception $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }

  $_SESSION['token'] = $client->getAccessToken();
} else {
  $state = mt_rand();
  $client->setState($state);
  $_SESSION['state'] = $state;

  $authUrl = $client->createAuthUrl();
  $htmlBody = <<<END
  <h3>Authorization Required</h3>
  <p>You need to <a href="$authUrl">authorize access</a> before proceeding.<p>
END;
}
?>

<!doctype html>
<html>
  <head>
    <title>My Uploads</title>
  </head>
  <body>
    <?=$htmlBody?>
  </body>
</html>
