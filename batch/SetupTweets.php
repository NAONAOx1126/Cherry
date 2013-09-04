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
$_SERVER["REQUEST_URI"] = "/batch/ImportTweets.php";

require_once(dirname(__FILE__)."/../require.php");

$connection = new Connection();
$sql = "SELECT accounts.*, account_groups.keyword_id, account_groups.pickup_limit, account_groups.pickup_count";
$sql .= " FROM accounts, account_groups";
$sql .= " WHERE account_groups.account_group_id = accounts.account_group_id";
$sql .= " AND account_groups.import_flg = 1";
$result = $connection->query($sql);
$accounts = $result->fetchAll();
$result->close();
if(is_array($accounts)){
	foreach($accounts as $account){
		// そのアカウントに投稿する投稿を取得します。
		$sql = "SELECT * FROM tweet_caches WHERE keyword_id = '".$connection->escape($account["keyword_id"])."' AND retweet_count >= '".$connection->escape($account["pickup_limit"])."' AND delete_flg = 0 ORDER BY retweet_count DESC";
		$result = $connection->query($sql);
		$cached_tweets = $result->fetchAll();
		$result->close();
		
		foreach($cached_tweets as $cached_tweet){
			// そのアカウントの予約済みツイート数を取得します。
			$sql = "SELECT * FROM tweets WHERE account_id = '".$connection->escape($account["account_id"])."' AND post_status = 1 AND delete_flg = 0";
			$result = $connection->query($sql);
			$tweets = $result->fetchAll();
			$result->close();
			
			// 予約済みツイートが規定数以上なら登録終了します。
			if(is_array($tweets) && $account["pickup_count"] > 0 && $account["pickup_count"] <= count($tweets)) break;
			
			// ツイートを登録します。
			$sqlval = array();
			$sqlval["account_id"] = $account["account_id"];
			$sqlval["source_post_id"] = $cached_tweet["post_id"];
			$sqlval["source_favorite_count"] = $cached_tweet["favorite_count"];
			$sqlval["source_retweet_count"] = $cached_tweet["retweet_count"];
			$sql = "SELECT * FROM tweets WHERE account_id = '".$connection->escape($sqlval["account_id"])."' AND  source_post_id = '".$connection->escape($sqlval["source_post_id"])."'";
			$result = $connection->query($sql);
			$registeredTweets = $result->fetchAll();
			$result->close();
			if(is_array($registeredTweets) && count($registeredTweets) > 0){
				if($registeredTweets[0]["post_status"] == "1"){
					foreach($sqlval as $key => $value){
						$sqlval[$key] = $key." = '".$connection->escape($value)."'";
					}
					$sql = "UPDATE tweets SET ".implode(", ", $sqlval);
					$sql .= " WHERE tweet_id = '".$connection->escape($registeredTweets[0]["tweet_id"])."'";
					echo $sql."\r\n";
					$result = $connection->query($sql);
				}
			}else{
				$sqlval["tweet_text"] = $cached_tweet["tweet_text"];
				$sqlval["rank"] = mt_rand(1, 100000);
				foreach($sqlval as $key => $value){
					$sqlval[$key] = $connection->escape($value);
				}
				$sql = "INSERT INTO tweets";
				$sql .= "(".implode(", ", array_keys($sqlval)).") VALUES ('".implode("', '", $sqlval)."')";
				echo $sql."\r\n";
				$result = $connection->query($sql);
			}
		}
	}
}
