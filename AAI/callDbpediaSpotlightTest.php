<?php

	include_once("../ARC2/ARC2.php");
	
	set_time_limit(0);// to infinity and beyond
 
/*
	just a helper to write to console
*/
function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}

// using some test data until the curl requests can be properly encoded
// used for testing function "getCategories($json_output)" , not needed any further
/*
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
	
*/

//----------------------------------------
// START ACTUALLY DOING SOMETHING HERE
//----------------------------------------

// start doing stuff. And write array into the given file.
//runAllRequestsAndSaveThemToFile('Paintings_and_Categories.txt');

$array = json_decode(file_get_contents('Paintings_and_Categories.json'),true);
saveCategorizePaintingsToFuseki( $array );


//----------------------------------------
// BELOW ARE DEFINED FUNCTIONS
//----------------------------------------

/*
	runAllRequestsAndSaveThemToFile()
	
	does all the stuff and writes it to file
*/
function runAllRequestsAndSaveThemToFile($filename){
	//retrieve array with paintings (label & description)
	$listOfPaintings = getPaintings(true); // true -> reads from paintings.txt, to get all data from Server use "false"
	
	//debug_to_console('Count of returned paintings: ' . count($listOfPaintings));
	//var_dump($paintings);

	$paintingcategories = array();

	//do curl requests
	$i = 0;
	foreach ($listOfPaintings as $p) { 
		// if($i > 2){
			// break;
		// }
	
		$result = singleRequest('http://spotlight.dbpedia.org/rest/annotate?text=' . urlencode($p['descr']) . '&confidence=0.2&support=20');
		
		$json_output = json_decode($result, TRUE);
			
		$categs = getCategories( $json_output );
		$paintingcategories[$p['label']] = $categs;
		
		//write array into text-file
		file_put_contents($filename, json_encode($paintingcategories, true) . PHP_EOL, FILE_APPEND);
		debug_to_console('succesfully appended ' . $i . '. painting categories into file: ' . $filename);
		
		$i++;
		//debug_to_console($i);
	}

	//write array into text-file
	//file_put_contents($filename, json_encode($paintingcategories, true));
	//debug_to_console('succesfully wrote all categories into file: ' . $filename);
	//var_dump($paintingcategories);
	debug_to_console('Amount of analysed paintings: ' . count($paintingcategories));
}


// single request to dbpedia - this one is used
// ---------
function singleRequest($url){
	
	header('Content-Type', 'application/json');

	// Get cURL
	$curl = curl_init();
	
	//Set the text to be sent to dbpedia spotlight
	//$descr = 'First documented in the 13th century, Berlin was the capital of the Kingdom of Prussia (1701–1918), the German Empire (1871–1918), the Weimar Republic (1919–33) and the Third Reich (1933–45). Berlin in the 1920s was the third largest municipality in the world. After World War II, the city became divided into East Berlin -- the capital of East Germany -- and West Berlin, a West German exclave surrounded by the Berlin Wall from 1961–89. Following German reunification in 1990, the city regained its status as the capital of Germany, hosting 147 foreign embassies.';
	//$descr = 'This is a test text about Christmas and Santa Clause giving gifts to the Lost Boys of Neverland in Canada flying Serenity into outer space.';
	//$descr = urlencode($descr);
	
	// Set options for cURL -> https://github.com/dbpedia-spotlight/dbpedia-spotlight/wiki/Web-service
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_HTTPHEADER => array('Accept: application/json', 'charset=utf-8'),
		CURLOPT_HEADER => false,
		CURLOPT_CONNECTTIMEOUT => 0, //The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
		CURLOPT_TIMEOUT => 400, //The maximum number of seconds to allow cURL functions to execute. timeout in seconds 
		CURLOPT_URL => $url
	));
	
	// Send request and get result
	
	$result = curl_exec($curl);
	// Close request
	curl_close($curl);
	//output json to console
	//debug_to_console($result);
	return $result;

}

	

/* not used
	code for multi curl
*/
function doMultiCurl($paintings){

	//set up array with get urls
	$competeRequests = array();
	$i = 0;
	foreach($paintings as $val) {
		if($i > 50){
			break;
		}
		$competeRequests[] = 'http://spotlight.dbpedia.org/rest/annotate?text=' . urlencode($val['descr']) . '&confidence=0.2&support=20';
		$i++; //break; // breaking here to test with first image. 
	}

	debug_to_console($competeRequests);

	//first batch
	$curlRequest = array();
	foreach (array_chunk($competeRequests, 100) as $requests) { // processing in batches of 100 [max = 1000]
		$results = multiRequest($requests);
		$curlRequest = array_merge($curlRequest, $results);
	}
	// currently this is an empty array??
	//var_dump($curlRequest);
	//debug_to_console($curlRequest); // = the result from dbpediaspotlight

	$catpaintings = array();
	$j = 0;
	// process the result 
	foreach ($curlRequest as $json){
		
		$json_output = json_decode($json, TRUE);
		$types = $json_output['Resources'][$j]['@types'];
		//var_dump("OUTPUT JSON STUFF" . $json_output['Resources'][$j]['@types']);
		debug_to_console($types); // Zwischendurch hab ich hier mal das richtige ausgegeben bekommen X.X (ohne excapen oder ähnliches)
		
		/*
		// escape stuff 
		//$result = preg_replace('/(\\\\n|\\\\t)/', '', $result);
		//stripslashes($result);
		function escapeJsonString($value) {
			$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
			$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
			$result = str_replace($escapers, $replacements, $value);
			return $result;
		}
		*/
		
		// build an array with the painting and the first three categories found from the analysis
		$categs = getCategories( $json_output );
		//debug_to_console($categs);
		$catpaintings[$paintings[$j]['label']] = $categs;
		
		$j++;
		//$result = escapeJsonString($result);
	}

	var_dump($catpaintings);
	debug_to_console(count($catpaintings));
	
	return $catpaintings;

}

/* not used
	Function from guy on here: http://stackoverflow.com/questions/12379801/simultaneous-http-requests-in-php-with-curl
	
	$data should be an array with the get urls
*/
function multiRequest($data) {
  // array of curl handles
  $curly = array();
  // data to be returned
  $result = array();
  
  header('Content-Type', 'application/json');

  // multi handle
  $mh = curl_multi_init();

  // loop through $data and create curl handles
  // then add them to the multi-handle
  foreach ($data as $id => $d) {

    $curly[$id] = curl_init();

    $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
    curl_setopt($curly[$id], CURLOPT_URL,            $url);
    curl_setopt($curly[$id], CURLOPT_HEADER,         0);
    curl_setopt($curly[$id], CURLOPT_HTTPHEADER,     array('Accept: application/json', 'charset=utf-8'));
    curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curly[$id], CURLOPT_TIMEOUT, 		 400); //The maximum number of seconds to allow cURL functions to execute. timeout in seconds 
    curl_setopt($curly[$id], CURLOPT_CONNECTTIMEOUT, 0); //The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
	
    curl_multi_add_handle($mh, $curly[$id]);
  }

  // execute the handles
  $running = null;
  do {
    curl_multi_exec($mh, $running);
  } while($running > 0);

  // get content and remove handles
  foreach($curly as $id => $c) {
    $result[$id] = curl_multi_getcontent($c);
	var_dump($result[$id]);
    curl_multi_remove_handle($mh, $c);
  }

  // all done
  curl_multi_close($mh);

  return $result;

}	
	
	
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
	//$json = json_decode($jsonSpotlightResult, true);
	$json = $jsonSpotlightResult;
	
	//see the decoded result
	//var_dump($json);
	
	//test output
	//echo $json['@text'];
	
	$categories = array();
	
	foreach ($json['Resources'] as $r) { 
		$types = $r['@types'];
	
		$posStart = 0;
		$dontUseErgebnis = false;
		
		// only search if string > 0 and Dbpedia resource is found
		// cut out the substring we want -> "DBpedia:XXXX"
		if($types !== '' && stristr($types, 'DBpedia:') !== FALSE){
			while($posStart < strlen($types)){
				$posDbpedia = stripos($types, 'DBpedia:', $posStart);
				$posEnd = stripos($types, ',', $posDbpedia);
				
				// if it's the last category there's no ',' found at the end
				if($posEnd === FALSE){
					$posEnd = strlen($types);
				}
				
				// get the substring
				$ergebnis = substr($types, $posDbpedia, ($posEnd - $posDbpedia));
				
				if(strcasecmp($ergebnis, 'DBpedia:TopicalConcept') == 0){  // Exclude this entity
					$posStart = $posEnd;
					$dontUseErgebnis = true;
					continue;
				}else{
					break;
				}
			}
			
			if(!$dontUseErgebnis){
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
	}

	return $categories;
}


/*
	Get a bunch of paintings (labels + description) limited to 2000
	$useTxt boolean / True reads from text file - False sends new request to Server
	
	returns: 
	array[
		{
			"label" => "DBpedia:The_Dream_of_the_Fisherman's_Wife",
			"descr" => "Some description for the painting"
		},
		{
			"label" => "DBpedia:Some_Painting",
			"descr" => "Some description for the painting"
		},
		...
	]
*/
function getPaintings( $useTxt ){

	if($useTxt){
	
		//header('Content-Type: application/json; charset=utf-8');
		$array = file_get_contents('paintings.txt');
		debug_to_console('successfully read Paintings from paintings.txt');
		//var_dump(json_decode($array,true));
        return json_decode($array,true);
	
	}else{
	
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
		SELECT DISTINCT ?painting ?plabel ?pdesc ' . // was ?painting ?plabel ?artist ?pic ?name ?pdesc '
		'WHERE 
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


		$paintings = array();
		$pcurr = array();
		
		//send the query
		try {
			$results = $store->query($query, 'rows');
			$i = 0;
			foreach ($results as $row) {
				$pos = strrpos($row['painting'], '/') + 1;
				$label = 'DBpedia:' . substr($row['painting'], $pos);
				$paintings[] = array('label' => $label, 'descr' => $row['pdesc']);
			}
		} catch (Exception $e) {
			print "<div class='error'>".$e->getMessage()."</div>\n";
		}
		
		//write to text-file here
		file_put_contents('paintings.txt', json_encode($paintings, true));
		debug_to_console('successfully got Paintings and written to paintings.txt');
	}

	return $paintings;
}


/*
    $catDescs should be a map with the painting as key and a list of categories as value, e.g.:

    array("dbpedia:Baronci_Altarpiece" => array(Holiday, Film, TelevisionShow), )
*/
function saveCategorizePaintingsToFuseki( $catPaintings ) {


}

?>
