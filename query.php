<?php
$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

error_reporting(E_ALL);
include_once("ARC2/ARC2.php");
//include_once("arc2-sparql11/ARC2.php");

$config = array(
    /* db */
    'db_name' => 'AII',
    'db_user' => 'AII',
    'db_pwd' => 'lab2',
    /* store */
    'store_name' => 'arc_tests',
    /* stop after 100 errors */
    'max_errors' => 100,
);
/*
//local query
$store = ARC2::getStore($config);
if (!$store->isSetUp()) {
    $store->setUp();

    $store->query('LOAD <http://server/AAI/Arts_Ontology.owl>');
}
*/

# Remote Store
$dbpconfig = array(
    "remote_store_endpoint" => "http://87.106.81.97:3030/ds/query",
);
//$store = ARC2::getComponent('SPARQL11RemoteStore', $dbpconfig);
$store = ARC2::getRemoteStore($dbpconfig);


if ($errs = $store->getErrors()) {
    echo "<h1>getRemoteStore error<h1>" ;
}

$query = '
      PREFIX rdf:      <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
      PREFIX rdfs:     <http://www.w3.org/2000/01/rdf-schema#>
      PREFIX dbpedia: <http://dbpedia.org/resource/>
    PREFIX yago: <http://dbpedia.org/class/yago/>
    PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
    PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>
    PREFIX dbpprop: <http://dbpedia.org/property/>
    PREFIX foaf: <http://xmlns.com/foaf/0.1/>
    PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
'.$request->query;

$triples = $store->query($query, 'rows'); /* execute the query */

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo json_encode($triples);
