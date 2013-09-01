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
		$result = $connection->query("SELECT * FROM tweets WHERE account_id = '".$connection->escape($account_id)."' AND delete_flg = 0 ORDER BY source_retweet_count DESC");
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
			header('Location: tweets.php?account_id='.$_POST["account_id"]);
			exit;
		}
	}
}
