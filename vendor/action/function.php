<?php require '../connect.php';

use DiDom\Document;
use GuzzleHttp\Client;

function get_html($url, Client $client)
{
  $response = $client->get($url);
  return $response->getBody()->getContents();
}

function get_team(Document $document, Client $client)
{
  global $link;
  global $url;
  $table_row = $document->find('#tournaments-tables-table-0 .custom-table__row');
  for ($i = 0; $i < count($table_row); $i++) {
    //sleep(rand(1, 3));
    //echo 'Команда ' . $i . PHP_EOL;
    $id[$i] = trim($table_row[$i]->first('.custom-table__number')->text());
    $icon_link[$i] = trim($table_row[$i]->first('img.custom-table__img')->attr('src'));
    $team_name[$i] = trim($table_row[$i]->first('.custom-table__team-name')->text());
    $team_link[$i] = trim($table_row[$i]->first('a.custom-table__team')->attr('href'));
    $games[$i] = trim($table_row[$i]->find('.custom-table__var')[0]->text());
    $win[$i] = trim($table_row[$i]->find('.custom-table__var')[1]->text());
    $draw[$i] = trim($table_row[$i]->find('.custom-table__var')[2]->text());
    $lost[$i] = trim($table_row[$i]->find('.custom-table__var')[3]->text());

    $sql = $link->query("SELECT * FROM `team` WHERE `id` = '$id[$i]'");
    if ($sql->num_rows == 0){
      $link->query(
        "INSERT INTO `team`(`id`, `name`, `games`, `win`, `draw`, `lost`, `icon_link`, `team_link`)
         VALUES ('$id[$i]','$team_name[$i]','$games[$i]','$win[$i]','$draw[$i]','$lost[$i]','$icon_link[$i]','$url.$team_link[$i]')"
      ) or die($link->error);
    } else {
      $link->query(
        "UPDATE `team`
         SET `id`='$id[$i]',`name`='$team_name[$i]',`games`='$games[$i]',
         `win`='$win[$i]',`draw`='$draw[$i]',`lost`='$lost[$i]',
         `icon_link`='$icon_link[$i]',`team_link`='$url.$team_link[$i]'
         WHERE `id` = '$id[$i]'"
      ) or die($link->error);
    }
    get_players($document, $client, $url . $team_link[$i]);
    $array[$i] = [
      'id' => $id[$i],
      'icon_link' => $icon_link[$i],
      'name' => $team_name[$i],
      'team_link' => $url . $team_link[$i],
      'games' => $games[$i],
      'win' => $win[$i],
      'draw' => $draw[$i],
      'lost' => $lost[$i],
    ];
  }
  return $array;
}

function get_players(Document $document, Client $client, $team_url)
{
  global $link;
  global $url;
  static $team_id = 1;
  sleep(rand(1, 3));
  $players = get_html($team_url, $client);
  $document->loadHtml($players);
  $table_row = $document->find('div#tournament-application-players-approved table .table__row');
  for ($i=0; $i < count($table_row); $i++) {
    //echo 'Игрок '.$i.PHP_EOL;
    $number[$i] = trim($table_row[$i]->first('.table__cell--number')->text());
    $amplua[$i] = trim($table_row[$i]->first('.table__cell--amplua')->text());
    $name[$i] = trim($table_row[$i]->first('.table__player-name')->text());
    $img_link[$i] = trim($table_row[$i]->first('img.table__player-img')->attr('src'));
    $player_link[$i] = trim($table_row[$i]->first('a.table__player')->attr('href'));
    $birthday[$i] = trim($table_row[$i]->first('.table__cell--middle')->text());
    $games[$i] = trim($table_row[$i]->find('.table__cell--variable')[0]->text());
    $goals[$i] = trim($table_row[$i]->find('.table__cell--variable')[1]->text());
    $assists[$i] = trim($table_row[$i]->find('.table__cell--variable')[2]->text());
    $yellow_card[$i] = trim($table_row[$i]->find('.table__cell--variable')[3]->text());
    $red_card[$i] = trim($table_row[$i]->find('.table__cell--variable')[4]->text());
    // ПРОВЕРКА НА НАЛИЧИЕ https://superliga.rfs.ru
    $arr = explode('/', $img_link[$i]);
    if (!in_array('https:', $arr, true)){
      $http = $url.$img_link[$i];
    } else{
      $http = $img_link[$i];
    }
    $sql = $link->query("SELECT * FROM `players` WHERE `name` = '$name[$i]'");
    if ($sql->num_rows == 0) {
      $link->query(
        "INSERT INTO `players`(`id`, `team_id`, `number`, `amplua`, `name`, `birthday`, `games`, `goals`, `assists`, `yellow_card`, `red_card`, `player_link`, `img_link`)
         VALUES (NULL,'$team_id','$number[$i]','$amplua[$i]','$name[$i]','$birthday[$i]','$games[$i]','$goals[$i]','$assists[$i]','$yellow_card[$i]','$red_card[$i]','$url.$player_link[$i]','$http')"
      ) or die($link->error);
    } else {
      $link->query(
        "UPDATE `players`
         SET `team_id`='$team_id', `number`='$number[$i]',`amplua`='$amplua[$i]',
         `name`='$name[$i]',`birthday`='$birthday[$i]',`games`='$games[$i]',`goals`='$goals[$i]',
         `assists`='$assists[$i]',`yellow_card`='$yellow_card[$i]',`red_card`='$red_card[$i]',
         `player_link`='$url.$player_link[$i]',`img_link`='$http'
         WHERE `name` = '$name[$i]'"
      ) or die($link->error);
    }
    // $array[$i] = [
    //   'number' => $number[$i],
    //   'amplua' => $amplua[$i],
    //   'name' => $name[$i],
    //   'img_link' => $http,
    //   'player_link' => $url.$player_link[$i],
    //   'birthday' => $birthday[$i],
    //   'games' => $games[$i],
    //   'goals' => $goals[$i],
    //   'assists' => $assists[$i],
    //   'yellow_card' => $yellow_card[$i],
    //   'red_card' => $red_card[$i]
    // ];
  }
  $team_id++;
  //return $array;
}
