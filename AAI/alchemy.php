<?php

/**
 * Test of the Alchemy API
 * Doku under:
 * https://github.com/AlchemyAPI/alchemyapi_php
 */

require_once '../alchemyapi_php/alchemyapi.php';
$alchemyapi = new AlchemyAPI();


$myText = "I can't wait to integrate AlchemyAPI's awesome PHP SDK into my app!";
$response = $alchemyapi->sentiment("text", $myText, null);
echo "Sentiment: ", $response["docSentiment"]["type"], PHP_EOL;

$response = $alchemyapi->image_keywords('url','https://c2.staticflickr.com/6/5531/9629720567_f4a95951bb_z.jpg', null);
foreach ($response['imageKeywords'] as $imagekeyword) {
    echo 'keyword: ', $imagekeyword['text'], PHP_EOL;
    echo 'score: ', $imagekeyword['score'], PHP_EOL;
}