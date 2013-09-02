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
$_SERVER["REQUEST_URI"] = "/batch/PostTweets.php";

require_once(dirname(__FILE__)."/../require.php");

$connection = new Connection();
$result = $connection->query("SELECT accounts.* FROM accounts, tweets WHERE accounts.account_id = tweets.account_id AND accounts.post_interval > 0 GROUP BY tweets.account_id HAVING UNIX_TIMESTAMP( MAX( tweets.post_time ) ) IS NULL OR accounts.post_interval * 60 < UNIX_TIMESTAMP() - UNIX_TIMESTAMP(MAX(tweets.post_time))");
$accounts = $result->fetchAll();
$result->close();
if(is_array($accounts)){
	foreach($accounts as $account){
		// アカウントの最優先の投稿を取得
		if($account["post_order"] == "2"){
			$order = "rank";
		}else{
			$order = "source_retweet_count";
		}
		$sql = "SELECT * FROM tweets WHERE account_id = '".$account["account_id"]."' AND post_status = 1 AND delete_flg = 0 ORDER BY ".$order." DESC LIMIT 1";
		$result = $connection->query($sql);
		$tweets = $result->fetchAll();
		$result->close();
		if(is_array($tweets) && count($tweets) > 0){
			// 投稿可能なツイートがある場合は投稿する。
			$twitter = getTwitter($account["account_id"]);
			
			// 画像がある場合と無い場合で分岐
			$imageFilename = "/images/".$tweets[0]["source_post_id"]."-1";
			if(file_exists(APP_ROOT.$imageFilename)){
				$medias = array();
				for($i = 1; file_exists(APP_ROOT."/images/".$tweets[0]["source_post_id"]."-".$i); $i ++){
					$medias[] = APP_ROOT."/images/".$tweets[0]["source_post_id"]."-".$i;
				}
				$tweeted = $twitter->statuses_updateWithMedia(array("status" => $tweets[0]["tweet_text"], "media[]" => $medias));
			}else{
				$params = array("status" => $tweets[0]["tweet_text"]);
				$tweeted = $twitter->statuses_update($params);
			}
			print_r($tweeted);
			$sqlval = array();
			$sqlval["post_id"] = $tweeted->id;
			$sqlval["post_status"] = "2";
			$sqlval["post_time"] = date("Y-m-d H:i:s");
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $key." = '".$connection->escape($value)."'";
			}
			$sql = "UPDATE tweets SET ".implode(", ", $sqlval);
			$sql .= " WHERE tweet_id = '".$connection->escape($tweets[0]["tweet_id"])."'";
			$result = $connection->query($sql);
		}
	}
}
