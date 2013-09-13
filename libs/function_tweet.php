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
	$result = $connection->query("SELECT * FROM accounts WHERE account_id = '".$connection->escape($account_id)."' AND administrator_id = '".$connection->escape($_SESSION["ADMINISTRATOR"]["administrator_id"])."'");
	$accounts = $result->fetchAll();
	if(is_array($accounts) && count($accounts) > 0){
		$result = $connection->query("SELECT * FROM tweets WHERE account_id = '".$connection->escape($account_id)."' AND post_status = 1 AND delete_flg = 0 ORDER BY source_retweet_count DESC");
		return $result->fetchAll();
	}
	return array();
}

// ツイートを取得
function getPostedTweets($account_id){
	$connection = new Connection();
	$result = $connection->query("SELECT * FROM accounts WHERE account_id = '".$connection->escape($account_id)."' AND administrator_id = '".$connection->escape($_SESSION["ADMINISTRATOR"]["administrator_id"])."'");
	$accounts = $result->fetchAll();
	if(is_array($accounts) && count($accounts) > 0){
		$result = $connection->query("SELECT * FROM tweets WHERE account_id = '".$connection->escape($account_id)."' AND post_status = 2 AND delete_flg = 0 ORDER BY post_time DESC");
		return $result->fetchAll();
	}
	return array();
}

// ツイートを削除
function deleteTweet(){
	if(!empty($_POST["delete"])){
		$connection = new Connection();
		$result = $connection->query("SELECT * FROM accounts WHERE account_id = '".$connection->escape($_POST["account_id"])."' AND administrator_id = '".$connection->escape($_SESSION["ADMINISTRATOR"]["administrator_id"])."'");
		$accounts = $result->fetchAll();
		if(is_array($accounts) && count($accounts) > 0){
			if($_POST["tweet_id"] > 0){
				$_POST["tweet_ids"] = array($_POST["tweet_id"]);
			}
			if($_POST["retweets"] > 0){
				$result = $connection->query("SELECT * FROM tweets WHERE account_id = '".$connection->escape($_POST["account_id"])."' AND retweet_count < '".$connection->escape($_POST["retweets"])."' AND post_status = 2 AND delete_flg = 0 AND post_time < '".date("Y-m-d H:i:s", strtotime("-".$_POST["past_days"]."day"))."'");
				$tweets = $result->fetchAll();
				$_POST["tweet_ids"] = array();
				if(is_array($tweets) && count($tweets) > 0){
					foreach($tweets as $tweet){
						$_POST["tweet_ids"][] = $tweet["tweet_id"];
					}
				}
			}
			if(is_array($_POST["tweet_ids"]) && count($_POST["tweet_ids"]) > 0){
				foreach($_POST["tweet_ids"] as $index => $tweet_id){
					$_POST["tweet_ids"][$index] = $connection->escape($tweet_id);
				}
				$result = $connection->query("SELECT * FROM tweets WHERE tweet_id IN ('".implode("', '", $_POST["tweet_ids"])."')");
				$tweets = $result->fetchAll();
				if(is_array($tweets) && count($tweets) > 0){
					foreach($tweets as $tweet){
						if($tweet["post_status"] == "2"){
							// ツイート済みの場合はTwitter上から削除
							$twitter = getTwitter($tweet["account_id"]);
							$twitter->statuses_destroy_ID(array("id" => $tweet["post_id"]));
						}
						$connection->query("UPDATE tweets SET post_status = 1, delete_flg = 1 WHERE tweet_id = '".$connection->escape($tweet["tweet_id"])."'");
					}
				}
			}
			
			// GETパラメータを削除するため、自分のURLにリダイレクト
			reload("account_id=".$_POST["account_id"]);
		}
	}
}

// ツイートを追加
function registerTweet(){
	if(!empty($_POST["register"])){
		$connection = new Connection();
		$result = $connection->query("SELECT * FROM accounts WHERE account_id = '".$connection->escape($_POST["account_id"])."' AND administrator_id = '".$connection->escape($_SESSION["ADMINISTRATOR"]["administrator_id"])."'");
		$accounts = $result->fetchAll();
		if(is_array($accounts) && count($accounts) > 0){
			$sqlval = array();
			$sqlval["account_id"] = $accounts[0]["account_id"];
			$sqlval["source_post_id"] = uniqid($accounts[0]["account_id"]."P");
			$sqlval["tweet_text"] = $_POST["tweet_text"];
			$sqlval["post_status"] = "1";
			$sqlval["source_favorite_count"] = "99999999";
			$sqlval["source_retweet_count"] = "99999999";
			$sqlval["rank"] = "99999999";
			$sqlval["delete_flg"] = "0";
			if($_FILES["tweet_image"]["error"] == UPLOAD_ERR_OK){
				move_uploaded_file($_FILES["tweet_image"]["tmp_name"], APP_ROOT."/images/".$sqlval["source_post_id"]."-1");
			}
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $connection->escape($value);
			}
			$sql = "INSERT INTO tweets";
			$sql .= "(".implode(", ", array_keys($sqlval)).") VALUES ('".implode("', '", $sqlval)."')";
			$result = $connection->query($sql);
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		reload("account_id=".$_POST["account_id"]);
	}
}

// ツイートを更新
function updateTweet(){
	if(!empty($_POST["update"])){
		$connection = new Connection();
		$result = $connection->query("SELECT * FROM accounts WHERE account_id = '".$connection->escape($_POST["account_id"])."' AND administrator_id = '".$connection->escape($_SESSION["ADMINISTRATOR"]["administrator_id"])."'");
		$accounts = $result->fetchAll();
		if(is_array($accounts) && count($accounts) > 0){
			if(is_array($_POST["tweet_text"]) && count($_POST["tweet_text"]) > 0){
				foreach($_POST["tweet_text"] as $tweet_id => $tweet_text){
					$connection->query("UPDATE tweets SET tweet_text = '".$connection->escape($tweet_text)."' WHERE tweet_id = '".$connection->escape($tweet_id)."'");
				}
			}
				
			// GETパラメータを削除するため、自分のURLにリダイレクト
			reload("account_id=".$_POST["account_id"]);
		}
	}
}
