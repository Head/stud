<?php
header('Content-Type: text/json; charset=utf-8');

require_once 'Zend/Search/Lucene.php';

#$index = new Zend_Search_Lucene('../tmp/arts_index');
$index = new Zend_Search_Lucene('../tmp/arts_arc2_index');
$search = $_REQUEST['query'];
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
?>
