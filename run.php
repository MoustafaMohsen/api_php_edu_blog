<?php

require 'json_create_post.php';
if (!file_exists ( "login.json" )) {
    http_response_code(400);
    echo '{"message":"File login.json not found"}';
}

$json_str = file_get_contents("login.json");

$login_arr = json_decode($json_str);

$username = $login_arr->username;
$password = $login_arr->password;
$data = (array)$login_arr->data;

$jsonCreate = new JsonCreatePost($username,$password,$data);
echo $jsonCreate->create();