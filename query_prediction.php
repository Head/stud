<?php
session_start();

include './bigml-php/bigml/bigml.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

//.$request->query;

$api = new BigML("Hardknox", "1c651c7716b41134337a8559949f31f3b8ff7cb1",True);
$model = $api::get_model('model/548ee909568be57ad90006de');


//print_r($model->object->model->root);

// "000000": "42","000001": "m","000002": "nein","000003": "b","000004": "http://dbpedia.org/resource/Albert_Bierstadt"
// 000000 age       string
// 000001 gender    string
// 000002 art       string
// 000003 degree    string
// 000004 uri       string

//$age = '21';
//$gender = 'm';
//$art = 'nein';
//$degree = 'h';
//$uri = 'http://dbpedia.org/resource/Adolph_Menzel';

$age = $request->age;
$art = $request->art;
$degree = $request->degree;
$gender = $request->gender;
$uri = $request->artist;

$prediction = $api::create_prediction($model, array('000000'=> $age, '000001'=> $gender, '000002'=> $art, '000003'=> $degree, '000004'=> $uri ));

//print_r($prediction->object->confidence);
//print_r($prediction->object->name);
//print_r($prediction->object->output);
//artist: $scope.artist.artist, art: $scope.userdata.art["key"], degree: $scope.userdata.degree["key"], age: $scope.userdata.age, gender: $scope.userdata.gender["key"]}

//echo $request->gender." - ".$request->artist." - ".$request->age." - ".$request->degree." - ".$request->gender;

$arr = array($prediction->object->output,$prediction->object->confidence);
echo json_encode($arr);
