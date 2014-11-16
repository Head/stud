<?php
    $path = '/www/htdocs/w0128f89/zf1/library';
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);

	include_once("../ARC2/ARC2.php");
	require_once 'Zend/Search/Lucene.php';
	
	$dbpconfig = array(
		"remote_store_endpoint" => "http://87.106.81.97:3030/ds/query"
	);
	$store = ARC2::getRemoteStore($dbpconfig);

$query = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
PREFIX rdfs:    <http://www.w3.org/2000/01/rdf-schema#> 
PREFIX dbpedia: <http://dbpedia.org/resource/> 
PREFIX yago: <http://dbpedia.org/class/yago/> 
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
PREFIX dbpedia-owl: <http://dbpedia.org/ontology/> 
PREFIX dbpprop: <http://dbpedia.org/property/> 
PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
SELECT DISTINCT ?painting ?plabel ?artist ?pic ?name ?pdesc ?adesc
WHERE 
{ ?painting rdf:type yago:Painting103876519 ; 
            foaf:depiction ?pic .
  optional {
    ?painting dbpprop:artist ?artist .
  }
  optional {
    ?painting dbpedia-owl:abstract ?pdesc . 
    FILTER (langMatches(lang(?pdesc),\'en\'))
  }
  optional {
    ?painting rdfs:label ?plabel . 
    FILTER (langMatches(lang(?plabel),\'en\'))
  }
  optional {
    ?artist rdfs:label ?name ; 
      dbpedia-owl:abstract ?adesc.
    FILTER (langMatches(lang(?name),\'en\'))
    FILTER (langMatches(lang(?adesc),\'en\'))
  }
} LIMIT 2000';
/*
$paintingQuery = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
PREFIX rdfs:    <http://www.w3.org/2000/01/rdf-schema#> 
PREFIX dbpedia: <http://dbpedia.org/resource/> 
PREFIX yago: <http://dbpedia.org/class/yago/> 
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
PREFIX dbpedia-owl: <http://dbpedia.org/ontology/> 
PREFIX dbpprop: <http://dbpedia.org/property/> 
PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
SELECT DISTINCT ?painting ?plabel ?pic ?name ?pdesc
WHERE 
{ ?painting rdf:type yago:Painting103876519 ; 
    dbpprop:artist ?artist ;
    foaf:depiction ?pic ;
    dbpedia-owl:abstract ?pdesc ; 
    rdfs:label ?plabel . 
    FILTER (langMatches(lang(?pdesc),\'en\')) .
    FILTER (langMatches(lang(?plabel),\'en\')) .
    ?artist rdfs:label ?name ; 
  }
} LIMIT 2000';

$painterQuery = 'PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
PREFIX rdfs:    <http://www.w3.org/2000/01/rdf-schema#> 
PREFIX dbpedia: <http://dbpedia.org/resource/> 
PREFIX yago: <http://dbpedia.org/class/yago/> 
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
PREFIX dbpedia-owl: <http://dbpedia.org/ontology/> 
PREFIX dbpprop: <http://dbpedia.org/property/> 
PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> 
SELECT DISTINCT ?artist ?name ?adesc
WHERE 
{ ?painting rdf:type yago:Painting103876519 ; 
    dbpprop:artist ?artist .
  ?artist rdfs:label ?name ; 
    dbpedia-owl:abstract ?adesc.
  FILTER (langMatches(lang(?name),\'en\'))
  FILTER (langMatches(lang(?adesc),\'en\'))
  }
} LIMIT 2000';
*/

	try {
		$index = new Zend_Search_Lucene('../tmp/arts_arc2_index', true);
		$results = $store->query($query, 'rows');
		$doc = new Zend_Search_Lucene_Document();
		foreach ($results as $row) {
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('painting', utf8_decode($row['painting'])));
			$doc->addField(Zend_Search_Lucene_Field::Text('plabel', utf8_decode($row['plabel'])));
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('pic', utf8_decode($row['pic'])));
			$doc->addField(Zend_Search_Lucene_Field::Text('pdesc', utf8_decode($row['pdesc'])));
			if (isset($row['artist']) && isset($row['name'])) {
				$doc->addField(Zend_Search_Lucene_Field::UnIndexed('artist', utf8_decode($row['artist'])));
				$doc->addField(Zend_Search_Lucene_Field::Text('name', utf8_decode($row['name'])));
				$doc->addField(Zend_Search_Lucene_Field::Text('adesc', utf8_decode($row['adesc'])));
			}
			$index->addDocument($doc);
		}
		$index->commit();
		print count($results) . ' documents added to index.';
	} catch (Exception $e) {
		print "<div class='error'>".$e->getMessage()."</div>\n";
	}
?>
