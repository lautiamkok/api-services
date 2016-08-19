<?php
$string = '
GyyjxGMSQCA,   aa ,
_yV8FP195RQ,
2m14F4n4Rdc,
sKXI7NfDvu4,
ewugOAh8voA,
R_NuBNcFv_s,
7PUOs9CnfTc,
9HQOFBL4w8I,
ar2k3xfT9Uc,
IStKEuyXXZE,
8ZK3dHOzxLs,
yFDK0XOfVo4,,,
';

// Strip all spaces.
$string = str_replace(' ', '', $string);

// Strip all breaks.
// http://stackoverflow.com/questions/5258543/remove-all-the-line-breaks-from-the-html-source
$string = preg_replace('/^\s+|\n|\r|\s+$/m', '', $string);

// Break items into an array and remove the empty ones.
$items = array_filter(explode(',', $string));
print_r($items);

$api_key = 'AIzaSyAkH1hLXYwoG0AUjtSxZa3W8Z_GREf7xzY';
foreach ($items as $key => $id) {
    $youtube_id = $id;

    // Prepare the Youtube api url.
    $request_url = 'https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $youtube_id . '&key=' . $api_key;

    // Use curl to send the GET request.
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $request_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);
    curl_close ($curl);

    $result_decode = json_decode($result, true);

    if (count($result_decode['items']) > 0) {
?>
    <div class="grid-item col-md-4 col-sm-4 col-xs-6">
        <div class="video-box">
            <!-- 16:9 aspect ratio -->
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?php echo $youtube_id;?>" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
<?php
    }
}
?>

<?php
// Validate yourube id.
// http://stackoverflow.com/questions/2742813/how-to-validate-youtube-video-ids
// $url = 'https://www.youtube.com/watch?v=MmpKDm-kjTM';
// $id = 'MmpKDm-kjTM';
// $has_match = preg_match('/^[a-zA-Z0-9_-]{11}$/', $id) > 0;;
// var_dump($has_match);

// http://help.dimsemenov.com/kb/wordpress-royalslider-tutorials/wp-how-to-get-youtube-api-key
// http://stackoverflow.com/questions/2742813/how-to-validate-youtube-video-ids
// https://www.googleapis.com/youtube/v3/videos?part=id&id=yFDK0XOfVo4&key=AIzaSyAkH1hLXYwoG0AUjtSxZa3W8Z_GREf7xzY

// If it passes:
// {
//  "kind": "youtube#videoListResponse",
//  "etag": "\"5g01s4-wS2b4VpScndqCYc5Y-8k/9BeDUDO6lvNudkXkJ098RFKAwww\"",
//  "pageInfo": {
//   "totalResults": 1,
//   "resultsPerPage": 1
//  },
//  "items": [
//   {
//    "kind": "youtube#video",
//    "etag": "\"5g01s4-wS2b4VpScndqCYc5Y-8k/NeAFOYg9F4kFPK9uuHMmkt6U5R0\"",
//    "id": "yFDK0XOfVo4"
//   }
//  ]
// }

// If it fails:
// {
//  "kind": "youtube#videoListResponse",
//  "etag": "\"5g01s4-wS2b4VpScndqCYc5Y-8k/fLm1KQ8-3QJyblcpL-fp8y0eIVw\"",
//  "pageInfo": {
//   "totalResults": 0,
//   "resultsPerPage": 0
//  },
//  "items": []
// }
?>
