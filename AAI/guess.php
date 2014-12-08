<?php

session_start();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

header('Content-Type: text/json; charset=utf-8');

$artist   = filter_var($request->query->artist, FILTER_SANITIZE_STRING);
$correct  = filter_var($request->query->correct, FILTER_SANITIZE_STRING);

//age, gender, art, degree, artisturi, answer
$string = $_SESSION['userdata']->age.','.$_SESSION['userdata']->gender.','.$_SESSION['userdata']->art.','.$_SESSION['userdata']->degree.','.$artist.','.$correct."\n";

file_put_contents('guess.csv', $string, FILE_APPEND | LOCK_EX);
