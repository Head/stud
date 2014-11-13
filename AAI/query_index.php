<?php

$path = '/www/htdocs/w0128f89/zf1/library';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once 'Zend/Search/Lucene.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

header('Content-Type: text/json; charset=utf-8');


$index = new Zend_Search_Lucene('tmp/arts_arc2_index');
$search = $request->query;
$hits = $index->find(strtolower($search));
$json = '[';

foreach ($hits as $hit) {
	$doc = $hit->getDocument();
	$json .= '{ "ID":'.$hit->id.', "score":'.$hit->score.',';
	foreach ($doc->getFieldNames() as $fieldName) {
		$field = $doc->getField($fieldName);
		$json .= '"'.$field->name.'":"'.$field->getUtf8Value().'",';
	}
	$json = rtrim($json, ',');
	$json .= '},';
}	
$json = rtrim($json, ',');
$json .= ']';
$json = preg_replace('#@[a-z]{2}#','', $json);
$json = preg_replace('#""#','"', $json);
echo $json;

