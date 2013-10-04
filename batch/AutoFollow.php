<?php 
/**
 * This file is part of Twitter auto post application.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   4.0.0
 */
/**
 * ツイートの取り込み処理を行います。
 */
$_SERVER["HTTPS"] = "";
$_SERVER["SERVER_NAME"] = $argv[1];
$_SERVER["DOCUMENT_ROOT"] = $argv[2];
$_SERVER["REQUEST_URI"] = "/batch/AutoFollow.php";

require_once(dirname(__FILE__)."/../require.php");

$connection = new Connection();

// フォロー実行対象アカウントを取得
$sql = "SELECT accounts.* FROM accounts";
$sql .= " WHERE UNIX_TIMESTAMP(next_follow_time) < UNIX_TIMESTAMP()";
$result = $connection->query($sql);
$accounts = $result->fetchAll();
$result->close();

if(is_array($accounts)){
	foreach($accounts as $account){
		$twitter = getTwitter($account["account_id"]);
	    
	    // 次のフォローを取得
	    $sql = "SELECT * FROM follower_caches WHERE account_id = '".$account["account_id"]."'";
	    $result = $connection->query($sql);
	    $follow = $result->fetch();
	    $result->close();
	    
	    if(!empty($follow)){
	        // 自分の情報を取得する。
		    $me = $twitter->users_show(array("user_id" => $account["user_id"]));
		    print_r($me);
		    exit;
	        // 取得したフォローのフォロワーを取得する。
    	    $result = (array) $twitter->followers_ids(array("user_id" => $follow["user_id"], "count" => "1000"));
    	    $followers = $result["ids"];
    	    for($i = 0; $i < 5; $i ++){
    	        $index = mt_rand(0, count($followers));
    	        if($index < count($followers)){
    	            $user_id = $followers[$index];
    	            $user = $twitter->users_show(array("user_id" => $user_id));
    	            if($_SESSION["ADMINISTRATOR"]["ignore_non_japanese_flg"] == "1" && mb_check_encoding($user->description, "ASCII")){
    	                continue;
    	            }
    	            if($_SESSION["ADMINISTRATOR"]["ignore_bot_flg"] == "1" && preg_match("/bot/i", $user->description) > 0){
    	                continue;
    	            }
    	            if($_SESSION["ADMINISTRATOR"]["ignore_url_flg"] == "1" && preg_match("/http:\\/\\//i", $user->description) > 0){
    	                continue;
    	            }
    	            
    	            $twitter->friendship_create()
    	            $follow
    	            
    	            if($follow["depth"] < $_SESSION["ADMINISTRATOR"]["tree_depth"]){
    	                $connection->query("INSERT IGNORE INTO follower_caches(account_id, user_id, depth) VALUES ('".$follow["account_id"]."', '".$user_id."', '".($follow["depth"] + 1)."')");
    	            }
    	        }
    	    }
    	    $connection->query("DELETE FROM follower_caches WHERE account_id = '".$follow["account_id"]."' AND user_id = '".$follow["user_id"]."'");
	    }
	}
}
