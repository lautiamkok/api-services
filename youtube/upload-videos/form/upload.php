<?php
$tokenFile = 'token.txt';
$tokenContent = file_get_contents($tokenFile);

// Load 'Google/Client.php' and 'Google/Service/YouTube.php' with composer.
require_once __DIR__ . '/vendor/autoload.php';
session_start();

// If the size of post data is greater than post_max_size, the $_POST and
// $_FILES superglobals are empty. This can be tracked in various ways, e.g. by
// passing the $_GET variable to the script processing the data, i.e. , and then
// checking if $_GET['processed'] is set.
$max_filesize_in_mib = min((int)(ini_get('upload_max_filesize')), (int)(ini_get('post_max_size')), (int)(ini_get('memory_limit')));
// print_r($max_filesize_in_mib);

// Gracefully handle files that exceed PHP's `post_max_size`.
// http://stackoverflow.com/questions/2133652/how-to-gracefully-handle-files-that-exceed-phps-post-max-size
// if (empty($_FILES)
//     && empty($_POST)
//     && isset($_SERVER['REQUEST_METHOD'])
//     && strtolower($_SERVER['REQUEST_METHOD']) == 'post'
// ){
//     $postMax = ini_get('post_max_size'); //grab the size limits...
//     echo "Please note files larger than {$postMax} will result in this error!";
// }

// Implemented a check, comparing CONTENT_LENGTH and post_max_size.
$maxPostSize = iniGetBytes('post_max_size');
if ($_SERVER['CONTENT_LENGTH'] > $maxPostSize) {
    // echo sprintf('Max post size exceeded! Got %s bytes, but limit is %s bytes.',
    //     $_SERVER['CONTENT_LENGTH'],
    //     $maxPostSize
    // );

    throw new Exception(
        sprintf('Max post size exceeded! Got %s bytes, but limit is %s bytes.',
            $_SERVER['CONTENT_LENGTH'],
            $maxPostSize
        )
    );
}

// Must add enctype= "multipart/form-data" to the form tag - or you will get an empty $_FILES.
// http://stackoverflow.com/questions/15130159/files-empty-after-form-submission
$files = array();
if (isset($_FILES['upload']) && count($_FILES['upload']) > 0) {
    $files = reArrayUploadFiles($_FILES['upload']);
}

$videos = filterUploadFiles($files, array(
    'video/mpeg',
    'video/mp4'
));

$attachments = filterUploadFiles($files, array(
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/msword',
));

print_r($videos);
// print_r($attachments);

$youtube_results = array();
if (count($videos) > 0) {
    foreach ($videos as $key => $video) {
        $youtube_results[] = uploadVideoToYoutube(
          $tokenFile,
          $tokenContent,
          $video,
          'Test Title',
          'Test description',
          array('tag1','tag2')
      );
    }
}
print_r($youtube_results);

$ids_string = '';
$ids = array();
$errors_string = '';
$errors = array();
if (count($youtube_results) > 0) {
    foreach ($youtube_results as $key => $result) {
        if (isset($result['error'])) {
            $errors[] = $result['name'] . ' - ' . $result['error'];
        } elseif (isset($result['id'])) {
            $ids[] = $result['id'];
        }
    }
    $ids_string = implode(', ', $ids);
    $errors_string = implode(', ', $errors);
}
print_r($errors_string);
print_r($ids_string);

// Get bytes.
function iniGetBytes($val)
{
    $val = trim(ini_get($val));
    if ($val != '') {
        $last = strtolower(
            $val{strlen($val) - 1}
        );
    } else {
        $last = '';
    }
    switch ($last) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

// Filter the files that you want.
function filterUploadFiles (array $files, array $wanted) {
    $wanted_files = array();
    foreach ($files as $key => $file) {
        if (in_array($file['type'], $wanted)) {
            $wanted_files[] = $file;
        }
    }
    return $wanted_files;
}

// Convert the $_FILES array to the cleaner (IMHO) array.
// http://php.net/manual/en/features.file-upload.multiple.php#53240
function reArrayUploadFiles (&$file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
    return $file_ary;
}

// Upload videos to youtube channel via Youtube API.
function uploadVideoToYoutube (
    $tokenFile,
    $tokenContent,
    array $file,
    $title = '',
    $description = '',
    $tags = array(),
    $categoryId = ''
) {
    return array(
        'name' => $file['name'],
        'error' => 'error!'
    );
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
            // $videoPath = "/home/lau/Desktop/Samples/Vids/Welcome.mp4";
            $videoPath = $file['tmp_name'];

            // Create a snippet with title, description, tags and category ID
            // Create an asset resource and set its snippet metadata and type.
            // This example sets the video's title, description, keyword tags, and
            // video category.
            $snippet = new Google_Service_YouTube_VideoSnippet();
            $snippet->setTitle($title);
            $snippet->setDescription($description);
            $snippet->setTags($tags);

            // Numeric video category. See
            // https://developers.google.com/youtube/v3/docs/videoCategories/list
            $snippet->setCategoryId($categoryId);

            // Set the video's status to "public". Valid statuses are "public",
            // "private" and "unlisted".
            $status = new Google_Service_YouTube_VideoStatus();
            $status->privacyStatus = "private";

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

            return array(
                'name' => $file['name'],
                'id' => $status['id']
            );
        } catch (Google_Service_Exception $e) {
            return array(
                'name' => $file['name'],
                'error' => htmlspecialchars($e->getMessage())
            );
        } catch (Google_Exception $e) {
            return array(
                'name' => $file['name'],
                'error' => htmlspecialchars($e->getMessage())
            );
        }

        $_SESSION['token'] = $client->getAccessToken();
    } else {
        // If the user hasn't authorized the app, initiate the OAuth flow
        $state = mt_rand();
        $client->setState($state);
        $_SESSION['state'] = $state;

        $authUrl = $client->createAuthUrl();
    }
}

