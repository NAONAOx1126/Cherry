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

// ツイートのステータス
$_SERVER["TWEET_STATUS"] = array(
	"1" => "予約済",
	"2" => "投稿済",
);

// ツイートを取得
function getTweets($account_id){
	$connection = new Connection();
	$result = $connection->query("SELECT * FROM tweets WHERE account_id = '".$connection->escape($account_id)."' AND delete_flg = 0 ORDER BY rank DESC");
	return $result->fetchAll();
}

// ツイートを削除
function deleteTweet(){
	if(!empty($_GET["delete"])){
		$connection = new Connection();
		if(!empty($_GET["tweet_id"])){
			$result = $connection->query("SELECT * FROM tweets WHERE tweet_id = '".$connection->escape($_POST["tweet_id"])."'");
			$tweets = $result->fetchAll();
			if(is_array($tweets) && count($tweets) > 0){
				if($tweets[0]["post_status"] == "2"){
					// ツイート済みの場合はTwitter上から削除
					$twitter = getTwitter($tweets[0]["account_id"]);
					$twitter->statuses_destroy_ID(array("id" => $tweets[0]["post_id"]));
				}
				$connection->query("UPDATE tweets SET post_status = 1, delete_flg = 1 WHERE tweet_id = '".$connection->escape($tweets[0]["tweet_id"])."'");
			}				
		}
		
		// GETパラメータを削除するため、自分のURLにリダイレクト
		header('Location: tweets.php?account_id='.$_POST["account_id"]);
		exit;
	}
}

/*
// アカウントグループを登録
function registerAccountGroup(){
	if(!empty($_POST["register"])){
		$connection = new Connection();
		$sqlval = array();
		$sqlval["administrator_id"] = $_SESSION["ADMINISTRATOR"]["administrator_id"];
		$sqlval["account_group_name"] = $_POST["account_group_name"];
		$sqlval["keyword"] = $_POST["keyword"];
		$sqlval["pickup_limit"] = $_POST["pickup_limit"];
		$sqlval["post_interval"] = $_POST["post_interval"];
		if(!empty($_POST["account_group_id"])){
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $key." = '".$connection->escape($value)."'";
			}
			$sql = "UPDATE account_groups SET ".implode(", ", $sqlval);
			$sql .= " WHERE account_group_id = '".$connection->escape($_POST["account_group_id"])."'";
			$result = $connection->query($sql);
		}else{
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $connection->escape($value);
			}
			$sql = "INSERT INTO account_groups";
			$sql .= "(".implode(", ", array_keys($sqlval)).") VALUES ('".implode("', '", $sqlval)."')";
			$result = $connection->query($sql);
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		header('Location: account_groups.php');
		exit;
	}
}

*/