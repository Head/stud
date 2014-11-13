<?php
	include_once("../ARC2/ARC2.php");
	require_once '/www/htdocs/w0128f89/zf1/library/Zend/Search/Lucene.php';
	
	# Remote Store
	$dbpconfig = array(
		"remote_store_endpoint" => "http://dbpedia.org/sparql"
		#"remote_store_endpoint" => "http://87.106.81.97:3030/ds/query"
	);
	$store = ARC2::getRemoteStore($dbpconfig);
	
	$query = 'PREFIX rdf:      <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
			PREFIX rdfs:     <http://www.w3.org/2000/01/rdf-schema#>
			PREFIX dbpedia: <http://dbpedia.org/resource/>
			PREFIX yago: <http://dbpedia.org/class/yago/>
			PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
			PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
			PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>
			PREFIX dbpprop: <http://dbpedia.org/property/>
			PREFIX foaf: <http://xmlns.com/foaf/0.1/>
			PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
			SELECT DISTINCT ?painting ?plabel ?artist ?pic ?name ?pdesc WHERE { 
					?painting rdf:type yago:Painting103876519 ;
						rdfs:label ?plabel ;
						dbpedia-owl:author ?artist ; 
						dbpedia-owl:abstract ?pdesc ;
						foaf:depiction ?pic .
					?artist dbpprop:name ?name .
					FILTER (lang(?pdesc) = \'en\')
					FILTER (lang(?plabel) = \'en\')
					FILTER (lang(?name) = \'en\')
				} LIMIT 200';
	
	try {
		$index = new Zend_Search_Lucene('../tmp/arts_arc2_index', true);
		$results = $store->query($query, 'rows');
		$doc = new Zend_Search_Lucene_Document();
		foreach ($results as $row) {
			$doc->addField(Zend_Search_Lucene_Field::Text('painting', utf8_decode($row['painting'])));
			$doc->addField(Zend_Search_Lucene_Field::Text('plabel', utf8_decode($row['plabel'])));
			$doc->addField(Zend_Search_Lucene_Field::Text('artist', utf8_decode($row['artist'])));
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('pic', utf8_decode($row['pic'])));
			$doc->addField(Zend_Search_Lucene_Field::Text('name', utf8_decode($row['name'])));
			$doc->addField(Zend_Search_Lucene_Field::Text('pdesc', utf8_decode($row['pdesc'])));
			$index->addDocument($doc);
		}
		$index->commit();
		print 'success'; //'index contains: '.$index->count().'documents';
	} catch (Exception $e) {
		print "<div class='error'>".$e->getMessage()."</div>\n";
	}
?>
