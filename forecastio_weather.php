<?php
function epoch_to_string_time($epoch,$timezone, $hour_only = false){
  $dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
  $TimeStr = $dt->format('Y-m-d H:i:s'); // output = 2012-08-15 00:00:00 
  $TimeZoneNameFrom="UTC";
  if ($hour_only){
    $ret = date_create($TimeStr, new DateTimeZone($TimeZoneNameFrom))
                ->setTimezone(new DateTimeZone($timezone))->format("ga");
    return $ret;

  }else{
    $ret = date_create($TimeStr, new DateTimeZone($TimeZoneNameFrom))
                ->setTimezone(new DateTimeZone($timezone))->format("H:i");
    return $ret;

  }
}

$mc = new Memcached();
$mc->addServer("127.0.0.1", 11211);
 

define('API_KEY', '3473b8c74dd6c98a35996f49ba7abede');
$payload = json_decode(file_get_contents('php://input'), true);
if(!$payload){
   print "no payload";
   die();
 }
$payload[1] /= 10000;
$payload[2] /= 10000;

$mem_key = $payload[1] . "_" . $payload[2];
$mem_result = $mc->get($mem_key);

if($mem_result) {
  print json_encode($mem_result);
}else{
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
  $today_low_time = epoch_to_string_time($forecast->daily->data[0]->temperatureMinTime, $forecast->timezone, true);
  $temp_high = round($forecast->daily->data[0]->temperatureMax);
  $today_high_time = epoch_to_string_time($forecast->daily->data[0]->temperatureMaxTime, $forecast->timezone, true);
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

  $response[1] = $temp; 
  $response[2] = $icon_id; //new
  $response[3] = $temp_low;
  $response[4] = $temp_high;
  $response[5] = $sunrise;
  $response[6] = $sunset;
  $response[7] = $today_icon_id;
  $response[8] = $tom_temp_low;
  $response[9] = $tom_temp_high;
  $response[10] = $tom_icon_id;
  $response[11] = $after_temp_low;
  $response[12] = $after_temp_high;
  $response[13] = $after_icon_id;
  $response[14] = $today_low_time;
  $response[15] = $today_high_time;

  $response_string = "";
  foreach ($response as &$value) {
        $response_string = $response_string . $value . "|";
  }

  $response_output = array();
  $response_output[1] = $response_string;
  
  $mc->set($mem_key, $response_output,(14*60));
  print json_encode($response_output);

}