<?php
function epoch_to_string_time($epoch,$timezone){
  $dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
  $TimeStr = $dt->format('Y-m-d H:i:s'); // output = 2012-08-15 00:00:00 
  $TimeZoneNameFrom="UTC";
  $ret = date_create($TimeStr, new DateTimeZone($TimeZoneNameFrom))
            ->setTimezone(new DateTimeZone($timezone))->format("H:i");
  return $ret;
}

//disabled
print("no longer used, thanks.");
die();

define('API_KEY', '3473b8c74dd6c98a35996f49ba7abede');
$payload = json_decode(file_get_contents('php://input'), true);
if(!$payload){
   print "no payload";
   die();
 }
$payload[1] /= 10000;
$payload[2] /= 10000;
$url = "http://api.forecast.io/forecast/" . API_KEY . "/$payload[1],$payload[2]?units=$payload[3]&exclude=minutely,hourly,alerts";

$forecast = json_decode(@file_get_contents($url));
if(!$forecast) {
    die();
}
$response = array();
$icons = array(
    'clear-day' => 0,
    'clear-night' => 1,
    'rain' => 2,
    'snow' => 3,
    'sleet' => 4,
    'wind' => 5,
    'fog' => 6,
    'cloudy' => 7,
    'partly-cloudy-day' => 8,
    'partly-cloudy-night' => 9
);
$icon_id = $icons[$forecast->currently->icon];
$temp = round($forecast->currently->temperature);
$temp_low = round($forecast->daily->data[0]->temperatureMin);
$temp_high = round($forecast->daily->data[0]->temperatureMax);
$today_icon_id = $icons[$forecast->daily->data[0]->icon];

$raw_sunrise_time = $forecast->daily->data[0]->sunriseTime;
$sunrise = epoch_to_string_time($raw_sunrise_time,$forecast->timezone);

$raw_sunset_time = $forecast->daily->data[0]->sunsetTime;
$sunset = epoch_to_string_time($raw_sunset_time,$forecast->timezone);

$tom_temp_low = round($forecast->daily->data[1]->temperatureMin);
$tom_temp_high = round($forecast->daily->data[1]->temperatureMax);
$tom_icon_id = $icons[$forecast->daily->data[1]->icon];

$after_temp_low = round($forecast->daily->data[2]->temperatureMin);
$after_temp_high = round($forecast->daily->data[2]->temperatureMax);
$after_icon_id = $icons[$forecast->daily->data[2]->icon];

$response[1] = array('S', $temp); 
$response[2] = array('S', $icon_id); //new

//$response[5] = $sunrise;
//$response[6] = $sunset;

$response[3] = array('S', $temp_low);
$response[4] = array('S', $temp_high);
$response[7] = array('S', $today_icon_id);

$response[8] = array('S',$tom_temp_low);
$response[9] = array('S',$tom_temp_high);
$response[10] = array('S', $tom_icon_id);

print json_encode($response);
