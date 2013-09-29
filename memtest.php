<?php
$mc = new Memcached();
$mc->addServer("127.0.0.1", 11211);
 
$result = $mc->get("test_key");
 
if($result) {
    echo $result;
} else {
    echo "No data on Cache. Please refresh page pressing F5";
      $mc->set("test_key", "test data pulled from Cache!", 5) or die ("Failed to save data at Memcached server");
}
?>
