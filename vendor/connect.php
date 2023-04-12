<?php
$db = 'railway';
$host = 'containers-us-west-204.railway.app';
$password = 'cl1TPudPfuSdmrPga7V5';
$port = '5474';
$user = 'root';

if (
  $_SERVER['HTTP_HOST'] == 'parse' ||
  $_SERVER['HTTP_HOST'] == 'localhost' ||
  $_SERVER['HTTP_HOST'] == 'localhost:8080'
) {
  // MY LOCAL BASE
  $link = new mysqli('localhost', 'root', 'root', 'parse') or die($link->error);
} else {
  // RAILWAY MYSQL BASE
  $link = new mysqli($host, $user, $password, $db, $port) or die($link->error);
}
