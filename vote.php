<?php

require 'config.php';
require 'database.php';

header('Content-type: application/json');

session_start();

function normalize_ip($ip)
{
    return substr(preg_replace("/[^0-9.]+/", "", $ip), 0, 15);
}

$client_ip = normalize_ip($_SERVER['REMOTE_ADDR']);
# $client_ip = rand(1,10000);
$vote = intval($_POST['vote']);
$video_id = intval($_POST['video_id']);
$plus = $vote == 1 ? 1 : 0;
$minus = $vote == -1 ? 1 : 0;

if (($vote != 1 && $vote != -1) || $video_id <= 0) {
    echo json_encode(array("error" => "error"));
    exit;
} else if (isset($_SESSION[$client_ip.$video_id])) {
    echo json_encode(array("error" => "already voted"));
    exit;
} else {
    // remember user voted
    $_SESSION[$client_ip.$video_id] = true;
}

session_write_close();

$config = new Config("../providentvidea.cz.config");

$db = new Db($config->getConfig());

$result = $db->query("select id from votes where ip='$client_ip' and video_id=$video_id and
                    datetime >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");

if ($db->num_rows($result) > 0) {
    echo json_encode(array("error" => "already voted"));
    exit;
} else {
    $db->query("insert into votes set ip='$client_ip', datetime = NOW(), video_id=$video_id, plus=$plus, minus=$minus");
    # should be moved to background process
    $result_sum = $db->query("select SUM(plus) as plus, SUM(minus) as minus from votes where video_id=$video_id");
    $row_sum = $db->fetch_row($result_sum);
    $plus = intval($row_sum['plus']);
    $minus = intval($row_sum['minus']);
    $db->query("update videos set plus=$plus, minus=$minus where id=$video_id");
    echo json_encode(array("plus" => $plus, "minus" => $minus));
}

?>