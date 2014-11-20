<?php

/**
 * Test of the Alchemy API
 * http://www.alchemyapi.com/developers/getting-started-guide/using-alchemyapi-with-php
 */

require_once '../alchemyapi_php/alchemyapi.php';
$alchemyapi = new AlchemyAPI();

$myText = "I can't wait to integrate AlchemyAPI's awesome PHP SDK into my app!";
$response = $alchemyapi->sentiment("text", $myText, null);
echo "Sentiment: ", $response["docSentiment"]["type"], PHP_EOL;