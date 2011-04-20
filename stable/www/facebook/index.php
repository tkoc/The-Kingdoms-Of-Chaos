<?php
// Copyright 2007 Facebook Corp.  All Rights Reserved. 
// 
// Application: The Kingdoms of Chaos
// File: 'index.php' 
//   This is a sample skeleton for your application. 
// 

require_once 'facebook.php';

$appapikey = 'b1dcf6266502006b442c2c8a748021f1';
$appsecret = 'a6abddbe696cff08e12858051a23c367';
$facebook = new Facebook($appapikey, $appsecret);
$user_id = $facebook->require_login();

// Greet the currently logged-in user!
echo "<p>Hello, <fb:name uid=\"$user_id\" useyou=\"false\" />!</p>";

// Print out at most 25 of the logged-in user's friends,
// using the friends.get API method
echo "<p>Friends:";
$friends = $facebook->api_client->friends_get();
$friends = array_slice($friends, 0, 25);
foreach ($friends as $friend) {
  echo "<br>$friend";
}
echo "</p>";