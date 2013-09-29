<?php
//$epoch = 1344988800; 
//$dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
//echo $dt->format('Y-m-d H:i:s'); // output = 2012-08-15 00:00:00 
$TimeStr="2012-01-01 12:00:00";
$TimeZoneNameFrom="UTC";
$TimeZoneNameTo="Europe/Amsterdam";
echo "hello\n";
echo date_create($TimeStr, new DateTimeZone($TimeZoneNameFrom))
          ->setTimezone(new DateTimeZone($TimeZoneNameTo))->format("Y-m-d H:i:s");
?>
