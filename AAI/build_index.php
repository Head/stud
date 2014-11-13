<?php
    $path = '/www/htdocs/w0128f89/zf1/library';
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);

	require_once "EasyRdf.php";
	require_once 'Zend/Search/Lucene.php';
	
	$endpoint = 'http://87.106.81.97:3030/ds/query';
	$sparql = new EasyRdf_Sparql_Client($endpoint);
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
		$results = $sparql->query($query);
		#Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
		#Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8_CaseInsensitive());
		$index = new Zend_Search_Lucene('../tmp/arts_index', true);
		
		if (isset($_REQUEST['dump'])) {
			foreach ($results->getFields() as $field) {
				print $field;
			}
			
		print $results->dump($_REQUEST['dump']);
		}
		
		foreach ($results as $row) {
			$doc = new Zend_Search_Lucene_Document();
			foreach ($results->getFields() as $field) {
				if (isset($row->$field)) {
					$value = $row->$field->dumpValue('text');
					$value = utf8_decode($value);
					$field = utf8_decode($field);
					$doc->addField(Zend_Search_Lucene_Field::Text($field, strtolower($value)));
				}
			}
			$index->addDocument($doc);
		}
			$index->commit();
			print 'success'; //'index contains: '.$index->count().'documents';
		} catch (Exception $e) {
			print "<div class='error'>".$e->getMessage()."</div>\n";
		}
?>
