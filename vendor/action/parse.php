<?php
set_time_limit(0);
ini_set('memory_limit', -1);

require '../connect.php';
require '../autoload.php';
require 'function.php';

use \GuzzleHttp\Client;
use \DiDom\Document;

$client = new Client();
$document = new Document();

$url = 'https://superliga.rfs.ru';

$html = get_html($url, $client);
$document->loadHtml($html);
sleep(rand(1, 3));

$team_data = get_team($document, $client);


// ОТПРАВКА JSON-ОТВЕТА
header('Content-type: application/json');
echo json_encode([
  'team' => $team_data,
  //'players' => $players_data
], JSON_UNESCAPED_UNICODE);
