<?php

/**
 * @author Marcelius 'mardagz' Dagpin
 * @name Beautify JSON
 * @copyright 2012
 * @uses /
 * $json_array = array(
 *      "name" => "mardagz",
 *      "gender" => "lalaki po akow hihihi",
 *      "age" => 40
 * );
 *
 * $json_data = json_encode($json_array);
 *
 * print $json->beautify_json($json_data);
 *
 */
 
class PRETTY_JSON{
   
    function beautify_json($json) {
        $tab = "  ";
        $new_json = "";
        $indent_level = 0;
        $in_string = false;
   
        $json_obj = json_decode($json);
   
        if($json_obj === false)
            return false;
   
        $json = json_encode($json_obj);
        $len = strlen($json);
   
        for($c = 0; $c < $len; $c++)
        {
            $char = $json[$c];
            switch($char)
            {
                case '{':
                case '[':
                    if(!$in_string)
                    {
                        $new_json .= $char . "\n" . str_repeat($tab, $indent_level+1);
                        $indent_level++;
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;
                case '}':
                case ']':
                    if(!$in_string)
                    {
                        $indent_level--;
                        $new_json .= "\n" . str_repeat($tab, $indent_level) . $char;
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;
                case ',':
                    if(!$in_string)
                    {
                        $new_json .= ",\n" . str_repeat($tab, $indent_level);
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;
                case ':':
                    if(!$in_string)
                    {
                        $new_json .= ": ";
                    }
                    else
                    {
                        $new_json .= $char;
                    }
                    break;
                case '"':
                    if($c > 0 && $json[$c-1] != '\\')
                    {
                        $in_string = !$in_string;
                    }
                default:
                    $new_json .= $char;
                    break;                    
            }
        }
   
        return $new_json;
    }
}
 
 
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


//###########################
//###########################
// test request to dbpedia spotlight
	
	header('Content-Type', 'application/json');

	// Get cURL
	$curl = curl_init();
	
	//Set the text to be sent to dbpedia spotlight
	//$descr = 'First documented in the 13th century, Berlin was the capital of the Kingdom of Prussia (1701–1918), the German Empire (1871–1918), the Weimar Republic (1919–33) and the Third Reich (1933–45). Berlin in the 1920s was the third largest municipality in the world. After World War II, the city became divided into East Berlin -- the capital of East Germany -- and West Berlin, a West German exclave surrounded by the Berlin Wall from 1961–89. Following German reunification in 1990, the city regained its status as the capital of Germany, hosting 147 foreign embassies.';
	$descr = 'This is a test text about Christmas and Santa Clause giving gifts to the Lost Boys of Neverland in Canada flying Serenity into outer space.';
	$descr = urlencode($descr);
	
	// Set options for cURL -> https://github.com/dbpedia-spotlight/dbpedia-spotlight/wiki/Web-service
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		//CURLOPT_URL => 'http://spotlight.dbpedia.org/rest/candidates' .   //bringt Ergebnisse ohne vollständige URIs
		CURLOPT_URL => 'http://spotlight.dbpedia.org/rest/annotate' .
		'?text=' . $descr .
		'&confidence=0.2' . 	
		'&support=20',
		
		/* couldn't get post to work, that's why i'm using get ^
		CURLOPT_POST => TRUE,
		CURLOPT_POSTFIELDS => array(
			text => 'This is a test text.',
			//conferenceResolution => '',
			confidence => '0.2',
			support => '20'
		),*/
		
		CURLOPT_HEADER => TRUE,
		CURLOPT_HTTPHEADER => array('Accept: application/json')
	));
	// Send request and get result
	$result = json_encode(curl_exec($curl));
	// Close request
	curl_close($curl);
	
	//output json to console
	debug_to_console($result);
	//use class to print json (see source code in FF for structure)
	$json = new PRETTY_JSON();
	print $json->beautify_json($result);
	
	
//###########################
//###########################
// copied the query request

/*
	// need to adjust
    $path = '/www/htdocs/w0128f89/zf1/library'; 
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);

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

	try {
		$results = $store->query($query, 'rows');
		foreach ($results as $row) {
			
			// call the thing and give it $row['pdesc']
			
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('painting', utf8_decode($row['painting'])));
			$doc->addField(Zend_Search_Lucene_Field::Text('plabel', utf8_decode($row['plabel'])));
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('pic', utf8_decode($row['pic'])));
			$doc->addField(Zend_Search_Lucene_Field::Text('pdesc', utf8_decode($row['pdesc'])));
			$index->addDocument($doc);
		}
	} catch (Exception $e) {
		print "<div class='error'>".$e->getMessage()."</div>\n";
	}
	
	
*/

/*
    $catDescs should be a map with the painting as key and a list of categories as value, e.g.:

    array("dbpedia:Baronci_Altarpiece" => array(Holiday, Film, TelevisionShow), )
*/
function saveCategorizePaintingsToFuseki( $catPaintings ) {


}

?>
