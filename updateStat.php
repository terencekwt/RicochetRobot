<?php
require_once('db_connect.php');

$state = array();
$results = mysql_query("SELECT * FROM users");
//print_r($results);

while($data = mysql_fetch_array($results)){
    $name = $data['name'];
    $state[$name] = $data;
    //array_push($state,$data);
}
//print_r($state);
echo json_encode($state);
