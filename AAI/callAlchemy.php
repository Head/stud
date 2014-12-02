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
//runAllRequestsAndSaveThemToFile('Paintings_Relations.txt');

//$array = json_decode(file_get_contents('Paintings_Relations.txt'),true);
//saveCategorizedPaintingsToFuseki( $array );


//----------------------------------------
// BELOW ARE DEFINED FUNCTIONS
//----------------------------------------

/*
	runAllRequestsAndSaveThemToFile()
	http://access.alchemyapi.com/calls/text/TextGetRelations?apikey=YOUR_API_KEY&outputMode=rdf&sentiment=1&entities=1&text=Ugly%20Bob%20attacked%20beautiful%20Susan
	a0130930b8e8ee2c46e9d31e8ec69170266ea7c7
	af69a9d467fce3151089697910d001bda0cbad09
	does all the stuff and writes it to file
*/
function runAllRequestsAndSaveThemToFile($filename){
	//retrieve array with paintings (label & description)
	$listOfPaintings = getPaintings(false); // true -> reads from paintings.txt, to get all data from Server use "false"

	//debug_to_console('Count of returned paintings: ' . count($listOfPaintings));
	//var_dump($paintings);

	$paintingcategories = array();

	//do curl requests
	$i = 0;
	foreach ($listOfPaintings as $p) {
		//if($i > 3){
		//	break;
		//}

		$result = singleRequest('http://access.alchemyapi.com/calls/text/TextGetRelations?apikey=af69a9d467fce3151089697910d001bda0cbad09&outputMode=json&sentiment=1&entities=1&text=' . urlencode($p['descr']));

		$json_output = json_decode($result, TRUE);

		// Check Alchemy Status Flag in result
		// If Status==OK -> parse and append data
		if ($json_output['status'] == "ERROR"){
			debug_to_console("STATUS: ERROR ALCHEMY (".$i." paintings) - msg:".$json_output['statusInfo']);
			// If limit of api calls reached -> break loop
			if ($json_output['statusInfo'] == "daily-transaction-limit-exceeded"){
				debug_to_console("BREAK - LIMIT EXCEEDED");
				break;
			}
		}
		else {
			// get relations out of json
			$relats = getRelations( $json_output );
			// append to array with painting-label as key
			$paintingcategories[$p['label']] = $relats;
		}
		$i++;
		debug_to_console($i);
	}

	//write array into text-file
	file_put_contents($filename, json_encode($paintingcategories, true) . PHP_EOL, FILE_APPEND);
	debug_to_console('succesfully appended ' . $i . '. painting categories into file: ' . $filename);

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


function getRelations( $jsonSpotlightResult ) {
	$relations = array();
	//debug_to_console("CALL getRelations");
	foreach ($jsonSpotlightResult['relations'] as $r) {
			array_push($relations, $r['subject']['text'] . " " . $r['action']['text'] . " " . $r['object']['text']);
		}
	//debug_to_console($relations);
	return $relations;
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
		SELECT DISTINCT ?painting ?plabel ?pic ?pdesc ' . // was ?painting ?plabel ?artist ?pic ?name ?pdesc '
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
				//$pos = strrpos($row['painting'], '/') + 1;
				//$label = 'DBpedia:' . substr($row['painting'], $pos);
				//$paintings[] = array('label' => $label, 'descr' => $row['pdesc']);

				//$pos = strrpos($row['pic'], '/') + 1;
				//$label = substr($row['pic'], $pos);

				$label = $row['pic'];
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
	Puts string into file
*/
function saveStatements2File($filename, $string) {
	file_put_contents($filename, $string);
}

/*
	Inserts a valid SPQARQL INSERT Statement on the Fuseki-Server. Will print errors (if so) into the html container.

	$insertStatement must be a SPARQL INSERT Statement
*/
function fusekiInsert($insertStatement) {
	$abs = realpath('s-update');
	exec($abs . " -f fuseki_insert_statements.txt --service http://87.106.81.97:3030/ds/update");
}




/*
This Function will search for the special characters:
!
)
(
,
'
and prefix them with a '\' (backslash), e.g.:
'show!' will become 'show\!'

the escaped string will be replaced
*/
function sanitizeRDF($string, $escape_char=array("'", ")", "(", ",", "!")  ) {

	foreach($escape_char as $ec){
		$string = str_replace($ec, "\\$ec", $string);
	}
	// $string = str_replace("'", "\'", $string);
	// $string = str_replace(")", "\)", $string);
	// $string = str_replace("(", "\(", $string);
	// $string = str_replace(",", "\,", $string);
	// $string = str_replace("!", "\!", $string);

	return $string;

}



/*
	This function build SPAQRL INSERT Statements from a map
	and saves them into an array.
	Every key will be taken as subject to the INSERT STATEMENT and
	build upon an entry from the list stored in the value-part of the map.

	$catPaintings must be a map, where each value is a list of strings (the category).

	returns an array of SPARQL INSERT Statements

*/
function buildInsertStatements($catPaintings) {

	$insertStatements = [];
	$relation = 'dbpedia:isTipOf';

	foreach($catPaintings as $painting => $categories) {

		//$painting = sanitizeRDF($painting);
		foreach ($categories as $category) {
			$category = sanitizeRDF($category,array('"') );
			$insertStatement = "INSERT DATA {  <${painting}> ${relation} \"${category}\" . };";
			$insertStatements[] = $insertStatement;
		}
	}

	return $insertStatements;

}

/*
	This function will convert the list of SPARQL INSERT Statements $insertStatements
	into a single string with a new-line character after each line.

	The Prefix "PREFIX dbpedia: <http://dbpedia.org/resource/>" will be
	automatically added.

	$insertStatements must be an array of SPARQL INSERT Statements

	returns the concatenated inserts string
*/
function buildInsertStatementsString($insertStatements) {

	$prefix = 'PREFIX dbpedia: <http://dbpedia.org/resource/> ' . PHP_EOL;
	// - Build big String with newline-character after each line
	$insertStatementsString = implode(PHP_EOL, $insertStatements);

	return $prefix . $insertStatementsString;

}

/*
    $catDescs should be a map with the painting as key and a list of categories as value, e.g.:

    array("dbpedia:Baronci_Altarpiece" => array(Holiday, Film, TelevisionShow), )
*/
function saveCategorizedPaintingsToFuseki( $catPaintings ) {


	$insertStatements = buildInsertStatements($catPaintings);
	$insertStatementsString = buildInsertStatementsString(str_replace("DBpedia","dbpedia",$insertStatements));
	saveStatements2File("fuseki_insert_statements.txt", $insertStatementsString);

	fusekiInsert($insertStatementsString);

}

//saveCategorizedPaintingsToFuseki(array("dbpedia:Baronci_Altarpiece" => array("Holiday", "Film", "TelevisionShow", "Krankenhaus") ));

?>
