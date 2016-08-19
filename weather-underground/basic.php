<?php
// $json_string = file_get_contents("http://api.wunderground.com/api/f3872da382b887a7/geolookup/conditions/q/MY/Kuala_Lumpur.json");
// $parsed_json = json_decode($json_string);
// $location = $parsed_json->{'location'}->{'city'};
// $temp_f = $parsed_json->{'current_observation'}->{'temp_f'};
// echo "Current temperature in ${location} is: ${temp_f}\n";

// http://www.wunderground.com/weather/api/d/docs?d=data/index&MR=1
// http://api.wunderground.com/api/f3872da382b887a7/features/settings/q/query.format
// Example:
// http://api.wunderground.com/api/f3872da382b887a7/history_20060405/q/UK/Plymouth.json -> by City
// http://api.wunderground.com/api/f3872da382b887a7/history_20151118/q/pws:IPLYMOUT12.json -> by PWS
// http://api.wunderground.com/api/f3872da382b887a7/history_2015/q/pws:IPLYMOUT12.json -> always return the current date's data

$json_string = file_get_contents("http://api.wunderground.com/api/f3872da382b887a7/geolookup/conditions/q/UK/London.json");

$parsed_json = json_decode($json_string);

$location = $parsed_json->{'location'}->{'city'};
$temp_c = $parsed_json->{'current_observation'}->{'temp_c'};
echo "Current temperature in ${location} is: ${temp_c}\n";

$wind_dir = $parsed_json->{'current_observation'}->{'wind_dir'};
echo "Current wind direction in ${location} is: ${wind_dir}\n";

$wind_degrees = $parsed_json->{'current_observation'}->{'wind_degrees'};
echo "Current wind direction (degrees) in ${location} is: ${wind_degrees}\n";

$wind_mph = $parsed_json->{'current_observation'}->{'wind_mph'};
echo "Current wind speed in ${location} is: ${wind_mph}\n";

$relative_humidity = $parsed_json->{'current_observation'}->{'relative_humidity'};
echo "Relative humidity is: ${relative_humidity}\n";

$local_epoch = $parsed_json->{'current_observation'}->{'local_epoch'};
echo "Local epoch is: ${local_epoch}\n";

$stations = $parsed_json->{'location'}->{'nearby_weather_stations'}->{'pws'}->{'station'};

// print_r($stations);

$count = count($stations);
for($i = 0; $i < $count; $i++)
{
    $station = $stations[$i];
    if (strcmp($station->{'id'}, "IPUCHONG2") == 0)
    {
        echo "Neighborhood: " . $station->{'neighborhood'} . "\n";
        echo "City: " . $station->{'city'} . "\n";
        echo "State: " . $station->{'state'} . "\n";
        echo "Latitude: " . $station->{'lat'} . "\n";
        echo "Longitude: " . $station->{'lon'} . "\n";
        break;
    }
}
