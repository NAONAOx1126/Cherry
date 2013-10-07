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
        echo "$file\n";
        if(preg_math("/^([0-9]+)-([0-9]+)$/", $file, $params) > 0){
            print_r($params);
        }
    }

    closedir($handle);
}
