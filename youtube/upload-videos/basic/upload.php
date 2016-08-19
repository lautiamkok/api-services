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
  try{
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

    // REPLACE this value with the path to the file you are uploading.
    $videoPath = "/home/lau/Desktop/Vids/big_buck_bunny_240p_1mb.mp4";

    // Create a snippet with title, description, tags and category ID
    // Create an asset resource and set its snippet metadata and type.
    // This example sets the video's title, description, keyword tags, and
    // video category.
    $snippet = new Google_Service_YouTube_VideoSnippet();
    $snippet->setTitle("Test title");
    $snippet->setDescription("Test description");
    $snippet->setTags(array("tag1", "tag2"));

    // Numeric video category. See
    // https://developers.google.com/youtube/v3/docs/videoCategories/list
    $snippet->setCategoryId("22");

    // Set the video's status to "public". Valid statuses are "public",
    // "private" and "unlisted".
    $status = new Google_Service_YouTube_VideoStatus();
    $status->privacyStatus = "public";

    // Associate the snippet and status objects with a new video resource.
    $video = new Google_Service_YouTube_Video();
    $video->setSnippet($snippet);
    $video->setStatus($status);

    // Specify the size of each chunk of data, in bytes. Set a higher value for
    // reliable connection as fewer chunks lead to faster uploads. Set a lower
    // value for better recovery on less reliable connections.
    $chunkSizeBytes = 1 * 1024 * 1024;

    // Setting the defer flag to true tells the client to return a request which can be called
    // with ->execute(); instead of making the API call immediately.
    $client->setDefer(true);

    // Create a request for the API's videos.insert method to create and upload the video.
    $insertRequest = $youtube->videos->insert("status,snippet", $video);

    // Create a MediaFileUpload object for resumable uploads.
    $media = new Google_Http_MediaFileUpload(
        $client,
        $insertRequest,
        'video/*',
        null,
        true,
        $chunkSizeBytes
    );
    $media->setFileSize(filesize($videoPath));


    // Read the media file and upload it chunk by chunk.
    $status = false;
    $handle = fopen($videoPath, "rb");
    while (!$status && !feof($handle)) {
      $chunk = fread($handle, $chunkSizeBytes);
      $status = $media->nextChunk($chunk);
    }

    fclose($handle);

    // If you want to make other calls after the file upload, set setDefer back to false
    $client->setDefer(false);


    $htmlBody .= "<h3>Video Uploaded</h3><ul>";
    $htmlBody .= sprintf('<li>%s (%s)</li>',
        $status['snippet']['title'],
        $status['id']);

    $htmlBody .= '</ul>';

  } catch (Google_Service_Exception $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
        htmlspecialchars($e->getMessage()));
  }

  $_SESSION['token'] = $client->getAccessToken();
} else {
  // If the user hasn't authorized the app, initiate the OAuth flow
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
<title>Video Uploaded</title>
</head>
<body>
  <?=$htmlBody?>
</body>
</html>
