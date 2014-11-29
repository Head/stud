<?php
 
//###########################
//###########################
//function for console output

function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}

/*	
	
//###########################
//###########################
// copied the query request


	// need to adjust
    //$path = '/www/htdocs/w0128f89/zf1/library'; 
    //set_include_path(get_include_path() . PATH_SEPARATOR . $path);

	include_once("../ARC2/ARC2.php");
	
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
SELECT DISTINCT ?painting ?plabel ?artist ?pic ?name ?pdesc 
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



	
	
	//send the query
	try {
		$results = $store->query($query, 'rows');
		foreach ($results as $row) {
			
			// call the thing and give it $row['pdesc']
			
			
			debug_to_console($row['plabel']);
			$descr = urlencode($row['pdesc']);
			debug_to_console($descr);
			
			/*$doc->addField(Zend_Search_Lucene_Field::UnIndexed('painting', utf8_decode($row['painting'])));
			$doc->addField(Zend_Search_Lucene_Field::Text('plabel', utf8_decode($row['plabel'])));
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('pic', utf8_decode($row['pic'])));
			$doc->addField(Zend_Search_Lucene_Field::Text('pdesc', utf8_decode($row['pdesc'])));
			$index->addDocument($doc);
			
			break;
		}
	} catch (Exception $e) {
		// nothing for now  ---- print "<div class='error'>".$e->getMessage()."</div>\n";
	}
*/

/*

//###########################
//###########################
// test request to dbpedia spotlight
	
	header('Content-Type', 'application/json');

	// Get cURL
	$curl = curl_init();
	
	//Set the text to be sent to dbpedia spotlight
	$descr = 'First documented in the 13th century, Berlin was the capital of the Kingdom of Prussia (1701–1918), the German Empire (1871–1918), the Weimar Republic (1919–33) and the Third Reich (1933–45). Berlin in the 1920s was the third largest municipality in the world. After World War II, the city became divided into East Berlin -- the capital of East Germany -- and West Berlin, a West German exclave surrounded by the Berlin Wall from 1961–89. Following German reunification in 1990, the city regained its status as the capital of Germany, hosting 147 foreign embassies.';
	//$descr = 'This is a test text about Christmas and Santa Clause giving gifts to the Lost Boys of Neverland in Canada flying Serenity into outer space.';
	$descr = urlencode($descr);
	
	// Set options for cURL -> https://github.com/dbpedia-spotlight/dbpedia-spotlight/wiki/Web-service
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_HTTPHEADER => array('Accept: application/json', 'charset=utf-8'),
		CURLOPT_HEADER => false,
		CURLOPT_URL => 'http://spotlight.dbpedia.org/rest/annotate' .
		'?text=' . $descr .
		'&confidence=0.2' . 	
		'&support=20',
	));
	
	
	// Send request and get result
	$result = json_encode(curl_exec($curl));
	// Close request
	curl_close($curl);
	
	//output json to console
	debug_to_console($result);
	
	// escape stuff 
	//$result = preg_replace('/(\\\\n|\\\\t)/', '', $result);
	//stripslashes($result);
	function escapeJsonString($value) {
		$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
		$result = str_replace($escapers, $replacements, $value);
		return $result;
	}
	
	$result = escapeJsonString($result);
	
*/

//###########################
//###########################
// Continue with "test"-JSON

	//data that is obtained in a loop -> label of painting and analyzis of description text
	$result_piclabel = "DBpedia:The_Dream_of_the_Fisherman's_Wife";  // Das Bild wird mich noch lange verfolgen...
	
	$result='{	"@text":"blah picture description blah",
				"Resources":[
					{
						"@types":"Freebase:/time/event,Freebase:/time,Freebase:/book/book_subject,Freebase:/book,Freebase:/film/film_subject,Freebase:/film,DBpedia:TopicalConcept"
					},
					{
						"@types":"DBpedia:AdministrativeRegion,DBpedia:PopulatedPlace,DBpedia:Place,Schema:Place,Freebase:/film/film_location,Freebase:/film,Freebase:/location/location,Freebase:/location,Freebase:/location/dated_location"
					},
					{
						"@types":"Freebase:/fictional_universe/fictional_setting,Freebase:/fictional_universe,Freebase:/fictional_universe/type_of_fictional_setting"
					},
					{
						"@types":"DBpedia:Country,DBpedia:PopulatedPlace,DBpedia:Place,Schema:Place,Schema:Country,Freebase:/location/statistical_region,Freebase:/location,Freebase:/royalty/system_of_nobility,Freebase:/royalty,Freebase:/royalty/kingdom,Freebase:/military/military_service,Freebase:/military,Freebase:/location/country,Freebase:/location/dated_location,Freebase:/location/location,Freebase:/military/military_combatant,DBpedia:TopicalConcept"
					},
					{
						"@types":"DBpedia:Country,DBpedia:PopulatedPlace,DBpedia:Place,Schema:Place,Schema:Country,Freebase:/location/country,Freebase:/location,Freebase:/location/dated_location,Freebase:/location/location,Freebase:/book/book_subject,Freebase:/book,Freebase:/military/military_combatant,Freebase:/military,Freebase:/location/statistical_region,Freebase:/royalty/kingdom,Freebase:/royalty,Freebase:/government/governmental_jurisdiction,Freebase:/government"
					},
					{
						"@types":"DBpedia:Country,DBpedia:PopulatedPlace,DBpedia:Place,Schema:Place,Schema:Country,Freebase:/location/dated_location,Freebase:/location,Freebase:/location/statistical_region,Freebase:/government/government,Freebase:/government,Freebase:/location/country,Freebase:/book/book_subject,Freebase:/book,Freebase:/education/field_of_study,Freebase:/education,Freebase:/location/location,Freebase:/organization/organization_member,Freebase:/organization"
					},
					{
						"@types":"DBpedia:Country,DBpedia:PopulatedPlace,DBpedia:Place,Schema:Place,Schema:Country,Freebase:/projects/project_focus,Freebase:/projects,Freebase:/government/governmental_jurisdiction,Freebase:/government,Freebase:/location/country,Freebase:/location,Freebase:/film/film_subject,Freebase:/film,Freebase:/education/field_of_study,Freebase:/education,Freebase:/book/book_subject,Freebase:/book,Freebase:/location/dated_location,Freebase:/aviation/aircraft_owner,Freebase:/aviation,Freebase:/location/location,Freebase:/location/statistical_region,Freebase:/military/military_combatant,Freebase:/military,DBpedia:TopicalConcept"
					},
					{
						"@types":"DBpedia:AdministrativeRegion,DBpedia:PopulatedPlace,DBpedia:Place,Schema:Place,Freebase:/film/film_location,Freebase:/film,Freebase:/location/location,Freebase:/location,Freebase:/location/dated_location"
					},
					{
						"@types":""
					},
					{
						"@types":"DBpedia:City,DBpedia:Settlement,DBpedia:PopulatedPlace,DBpedia:Place,Schema:Place,Schema:City,Freebase:/travel/travel_destination,Freebase:/travel,Freebase:/location/citytown,Freebase:/location,Freebase:/film/film_location,Freebase:/film,Freebase:/protected_sites/listed_site,Freebase:/protected_sites,Freebase:/architecture/architectural_structure_owner,Freebase:/architecture,Freebase:/location/statistical_region,Freebase:/olympics/olympic_host_city,Freebase:/olympics,Freebase:/government/political_district,Freebase:/government,Freebase:/location/administrative_division,Freebase:/location/de_state,Freebase:/business/employer,Freebase:/business,Freebase:/government/governmental_jurisdiction,Freebase:/location/location,Freebase:/organization/organization_scope,Freebase:/organization,Freebase:/location/de_city,Freebase:/olympics/olympic_bidding_city,Freebase:/business/business_location,Freebase:/fictional_universe/fictional_setting,Freebase:/fictional_universe,Freebase:/location/place_with_neighborhoods,Freebase:/location/dated_location,Freebase:/book/book_subject,Freebase:/book,Freebase:/sports/sports_team_location,Freebase:/sports,DBpedia:TopicalConcept"
					},
					{
						"@types":"DBpedia:TopicalConcept"
					}
				]
			}';
				 
	// build an array with the painting and the first three categories find from the analyzis
	$catpaintings[$result_piclabel] = getCategories( $result );

	var_dump($catpaintings);
	
	
	
/*
   $jsonSpotlightResult should be the returned JSON object from DBpedia spotlight. The important part of the return object is the "Resources" array with the key "@types"
	Expected structure:
	{	"@text":"blah picture description blah",
		"XXX":"other stuff",
		"Resources":[
			{
				"XXX:":"blah",
				"@types":"Freebase:/time/event,Freebase:/time,Freebase:/book/book_subject,Freebase:/book,Freebase:/film/film_subject,Freebase:/film,DBpedia:TopicalConcept"
			},
			{
				"XXX:":"blah",
				"@types":"Freebase:/time/event,Freebase:/time,Freebase:/book/book_subject,Freebase:/book,Freebase:/film/film_subject,Freebase:/film,DBpedia:TopicalConcept"
			}
		]
	}
*/
function getCategories( $jsonSpotlightResult ) {

	//encode to array
	$json = json_decode($jsonSpotlightResult, true);
	
	//see the decoded result
	//var_dump($json);
	
	//test output
	//echo $json['@text'];
	
	$categories = array();
	
	foreach ($json['Resources'] as $r) { 
		$types = $r['@types'];
		
		// only search if string > 0 and Dbpedia resource is found
		// cut out the substring we want -> "DBpedia:XXXX"
		if($types !== '' && stristr($types, 'DBpedia:') !== FALSE){
			$posDbpedia = stripos($types, 'DBpedia:');
			$posEnd = stripos($types, ',', $posDbpedia);
			
			// if it's the last category there's no ',' found at the end
			if($posEnd === FALSE){
				$posEnd = strlen($types);
			}
			
			// get the substring
			$ergebnis = substr($types, $posDbpedia, ($posEnd - $posDbpedia));
			
			// append substring to categories array if it's not in the array yet
			if (in_array($ergebnis, $categories)) {
				continue;	
			}else{
				array_push($categories, $ergebnis);
			}
			
			// only get the first three categories found
			if(count($categories) == 3){
				break;
			}
		}
	}

	return $categories;
}

/*
    $catDescs should be a map with the painting as key and a list of categories as value, e.g.:

    array("dbpedia:Baronci_Altarpiece" => array(Holiday, Film, TelevisionShow), )
*/
function saveCategorizePaintingsToFuseki( $catPaintings ) {


}

?>
