<?php

require 'config.php';
require 'database.php';

$config = new Config("../providentvidea.sk.config");

$db = new Db($config->getConfig());

$result = $db->query("select * from videos order by `order`");

$video_list = array();

while ($row = $db->fetch_row($result)) {
    $video_list[] = array("title" => $row['title'],
        "src" => $row['src'],
        "type" => $row['type'],
        "plus" => $row['plus'],
        "id" => $row['id'],
        "minus" => $row['minus']
    );
}

$tpl = file_get_contents("./template/index.html");
$tpl = str_replace("{VIDEO_LIST}", json_encode($video_list), $tpl);
echo $tpl;

?>