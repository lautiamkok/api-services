# YouTube API

https://developers.google.com/youtube/v3/getting-started

# Tutorials

https://www.youtube.com/watch?v=-vH2eZAM30s
http://www.w3resource.com/API/youtube/tutorial.php
https://www.domsammut.com/code/php-server-side-youtube-v3-oauth-api-video-upload-guide

https://www.youtube.com/watch?v=hfWe1gPCnzc
https://developers.google.com/oauthplayground/
http://www.phpgang.com/how-to-authenticate-upload-videos-to-youtube-channel-in-php_974.html
https://www.codementor.io/nodejs/tutorial/uploading-videos-to-youtube-with-nodejs-google-api

http://stackoverflow.com/questions/20198093/api-upload-video-in-php-to-a-specific-channel
http://stackoverflow.com/questions/19449061/upload-videos-to-my-youtube-channel-without-user-authentication-using-youtubeapi

# Video samples

http://camendesign.com/code/video_for_everybody/test.html

# Youtube Channel Config

https://www.youtube.com/account_advanced
https://www.youtube.com/upload_defaults

# Getting Credentials/ API Keys

1. Got to Google Developer Console  with your Google account - https://console.developers.google.com/
2. Create the new project (name does not matter for the plugin).

    Example project:

    https://console.developers.google.com/iam-admin/iam/project?project=multimedia-submission

3. Go to your project (by clicking on its name in the list).
4. In the list of APIs, select YouTube Data API (v3) and enable it (Enable API).

    Example:

    https://console.developers.google.com/apis/library?project=multimedia-submission
    https://console.developers.google.com/apis/api/youtube/overview?project=multimedia-submission

5. Click Credentials on the left, then choose to create api key or credentials.

    Example credentials:

    Client ID: 504646174423-etmknegujqfd3vo95q7hh9r6uij7dqc3.apps.googleusercontent.com
    Client secret: wYkuB7fmcXY0re-eUXxs_QWr

    Example API key:

    API key: AIzaSyAx9I6v2JH75zLJHuDmLEna7nY8eQSX498

    Note: remember to set **Application type** to **Other** on creating credentials.

@ref:
https://github.com/google/google-api-php-client-services
http://stackoverflow.com/questions/2742813/how-to-validate-youtube-video-ids
http://help.dimsemenov.com/kb/wordpress-royalslider-tutorials/wp-how-to-get-youtube-api-key

# Getting the access token and refresh token

1. Create a token.php (make sure set its permission to 777). Place the code example from Google - https://developers.google.com/youtube/v3/code_samples/php#upload_a_video - with a little of this modification as follow:

    Change:

    ```
    if (isset($_SESSION['token'])) {
      $client->setAccessToken($_SESSION['token']);
    }
    ```

    To:

    ```
    if (isset($_SESSION['token'])) {
      $client->setAccessToken($_SESSION['token']);

      // @ref: http://www.whitewareweb.com/php-youtube-video-upload-google-api-oauth-2-0-v3/
      echo "Access Token: " . $_SESSION['token'];
    }
    ```

    Full code:

    ```
    <?php

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
    $OAUTH2_CLIENT_ID = 'REPLACE_ME';
    $OAUTH2_CLIENT_SECRET = 'REPLACE_ME';

    $client = new Google_Client();
    $client->setClientId($OAUTH2_CLIENT_ID);
    $client->setClientSecret($OAUTH2_CLIENT_SECRET);
    $client->setScopes('https://www.googleapis.com/auth/youtube');
    $redirect = filter_var('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'],
        FILTER_SANITIZE_URL);
    $client->setRedirectUri($redirect);

    // To get a refresh token.
    $client->setAccessType('offline');
    $client->setApprovalPrompt('force');

    // Define an object that will be used to make all API requests.
    $youtube = new Google_Service_YouTube($client);

    if (isset($_GET['code'])) {
      if (strval($_SESSION['state']) !== strval($_GET['state'])) {
        die('The session state did not match.');
      }

      $client->authenticate($_GET['code']);
      $_SESSION['token'] = $client->getAccessToken();
      header('Location: ' . $redirect);
    }

    if (isset($_SESSION['token'])) {
      $client->setAccessToken($_SESSION['token']);

      // @ref: http://www.whitewareweb.com/php-youtube-video-upload-google-api-oauth-2-0-v3/
      echo "Access Token: " . $_SESSION['token'];
    }

    // Check to ensure that the access token was successfully acquired.
    if ($client->getAccessToken()) {
      try{
        // REPLACE this value with the path to the file you are uploading.
        $videoPath = "/path/to/file.mp4";

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

    ```
2. Run token.php on your browser. Grant the access. Then you get a json output below:

    {
      "access_token": "ya29.Ci8FA69nOu8PrpwgTMeXKP6u88vk0SAk3NsN5pbBp5x47t7YvjObwYx3DG4NRmfjiQ",
      "token_type": "Bearer",
      "expires_in": 3600,
      "refresh_token": "1\/0SYF_gTLgQjBZKays8nfjq5hzQTUapZ7zyRIHP7X_G8",
      "created": 1466238624
    }

    Save it as token.txt

## Obtain a refresh token (manually).

1. Go to https://developers.google.com/oauthplayground/

2. Go to setting on the right, select:

    Use your own OAuth credentials

    Fill in:

    OAuth Client ID
    OAuth Client secret

3. Go back to your credentials at https://console.developers.google.com:

    https://console.developers.google.com/apis/credentials/oauthclient/504646174423-2vd44pujj4tchvdgn5smrb7iv4t7v3s4.apps.googleusercontent.com?project=multimedia-submission

    Authorized redirect URIs:
    https://developers.google.com/oauthplayground

    Click 'Save'.


4. Under 'Select & authorize APIs' on the left, go to:

    YouTube Data API v3

    Select:

    https://www.googleapis.com/auth/youtube
    https://www.googleapis.com/auth/youtube.upload

5. Click 'Authorize API'.

    Refresh token: 1/3wFdqal6rvw7gHY6Jfe6KC7i_urjE2TeVWGWOjs4Nfs

@ref:
https://www.youtube.com/watch?v=hfWe1gPCnzc

## Obtain Google APIs Client Library for PHP

1. Obtaining the client library - Google APIs Client Library for PHP

    **Using Composer**

    You can install the library by adding it as a dependency to your composer.json.

    "require": {
        "google/apiclient": "1.0.*@beta"
    }

2. Create a PHP file with Composer autoloader at the beginning:

    require_once __DIR__ . '/vendor/autoload.php';

# Revoke access

https://security.google.com/settings/security/permissions

# Notes

1. YouTube Category Names & ID’s

Category Name Category => ID
Film & Animation => 1
Autos & Vehicles => 2
Music => 10
Pets & Animals => 15
Sports => 17
Short Movies => 18
Travel & Events => 19
Gaming => 20
Videoblogging => 21
People & Blogs => 22
Comedy => 23
Entertainment => 24
News & Politics => 25
Howto & Style => 26
Education => 27
Science & Technology => 28
Movies => 30
Anime/Animation => 31
Action/Adventure => 32
Classics => 33
Comedy => 34
Documentary => 35
Drama => 36
Family => 37
Foreign 38
Horror => 39
Sci-Fi/Fantasy => 40
Thriller => 41
Shorts => 42
Shows 43
Trailers => 44

# Trouble Shooting

## $_FILES and $_POST data empty when uploading certain files

The issue you are facing is due to the fact that post_max_size is set to 8M as default in your php.ini. As your file is 10.4MB you run into the following error:

POST Content-Length of 10237675 bytes exceeds the limit of 8388608 bytes in Unknown
Because you've reached that limit. The trick to fixing this is to simply up that limit by changing the value. You could simply change it directly in your php.ini file to whatever you desire, i.e. 20M.

Or you could set it via your .htaccess file with:

    php_value post_max_size 20M
    php_value upload_max_filesize 20M

Note that it is impossible to access the file directly on your disk. Either you change the 'upload_max_filesize' or you enter the file path manually, assuming you are on the same machine without any access restrictions.

@ref:
http://stackoverflow.com/questions/29836496/files-and-post-data-empty-when-uploading-certain-files
http://stackoverflow.com/questions/3586919/why-would-files-be-empty-when-uploading-files-to-php
http://stackoverflow.com/questions/38279194/php-upload-how-to-get-the-relative-path-of-an-uploaded-file
https://mediatemple.net/community/products/dv/204404784/how-do-i-increase-the-php-upload-limits

## Error: redirect_uri_mismatch

**Solution:**

Delete the credentials and create a new one with **Application type** set to **Other**.

## No refresh token given from google.

You get this:

Access Token: {"access_token":"ya29.Ci8aA-J8ngLwvfyFWdgvXMNZZHkLGF-3UjeLy4ik0wCHzOlPEsjLukTAhWKYJCTEYA","token_type":"Bearer","expires_in":3600,"created":1468089383}

Instead of:

Access Token: {"access_token":"ya29.Ci8aAzS53VuxrZM13SyjKYyYPaC5-Xj3V_5xzOSQQyxJZWwDjOyXi8hLQFpRBjpJkA","token_type":"Bearer","expires_in":3600,"refresh_token":"1\/cPMtIyCkaNSYEgo5lZXdsqDQQqiJG9UCZjP7rOOUYzc","created":1468093937}

**Solution:**

1. Revoke previous access at https://security.google.com/settings/security/permissions
2. Make sure you have this in your token.php:

    // To get a refresh token.
    $client->setAccessType('offline');
    $client->setApprovalPrompt('force');

3. Run token.php on the browser again (a different browser to avoid this error - OAuth 2.0 access token has expired, and a refresh token is not available):

@ref:
http://stackoverflow.com/questions/20565653/oauth-2-0-access-token-has-expired-and-a-refresh-token-is-not-available
http://stackoverflow.com/questions/8942340/get-refresh-token-google-api
http://stackoverflow.com/questions/10827920/not-receiving-google-oauth-refresh-token
