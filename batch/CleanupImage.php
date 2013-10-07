<?php
$_SERVER["HTTPS"] = "";
$_SERVER["SERVER_NAME"] = $argv[1];
$_SERVER["DOCUMENT_ROOT"] = $argv[2];
$_SERVER["REQUEST_URI"] = "/batch/AutoFollow.php";

require_once(dirname(__FILE__)."/../require.php");

$connection = new Connection();

if ($handle = opendir(dirname(__FILE__)."/../images/")) {
    /* ディレクトリをループする際の正しい方法です */
    while (false !== ($file = readdir($handle))) {
        if(preg_match("/^([0-9]+)-([0-9]+)$/", $file, $params) > 0){
            $result = $connection->query("SELECT COUNT(*) AS count FROM tweets WHERE source_post_id = '".$params[1]."'");
            $data = $result->fetch();
            if($data["count"] > 0) continue;
            $result = $connection->query("SELECT COUNT(*) AS count FROM tweet_caches WHERE post_id = '".$params[1]."'");
            $data = $result->fetch();
            if($data["count"] > 0) continue;
            echo "DELETE : ".$file."\r\n";
        }
    }

    closedir($handle);
}
