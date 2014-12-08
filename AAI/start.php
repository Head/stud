<?php

session_start();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

header('Content-Type: text/json; charset=utf-8');

$_SESSION['userdata']->age      = filter_var($request->userdata->age, FILTER_SANITIZE_STRING);
$_SESSION['userdata']->art      = filter_var($request->userdata->art->key, FILTER_SANITIZE_STRING);
$_SESSION['userdata']->degree   = filter_var($request->userdata->degree->key, FILTER_SANITIZE_STRING);
$_SESSION['userdata']->gender   = filter_var($request->userdata->gender->key, FILTER_SANITIZE_STRING);

print_r($_SESSION['userdata']);