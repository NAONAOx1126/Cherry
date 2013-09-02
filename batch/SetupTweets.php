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
$sql = "SELECT accounts.*, account_groups.keyword_id, account_groups.pickup_limit, account_groups.pickup_count FROM accounts, account_groups";
$sql .= " FROM accounts, account_groups";
$sql .= " WHERE account_groups.account_group_id = accounts.account_group_id";
$sql .= " AND account_groups.import_flag = 1";
$result = $connection->query($sql);
$accounts = $result->fetchAll();
$result->close();
if(is_array($accounts)){
	foreach($accounts as $account){
		// そのアカウントに投稿する投稿を取得します。
		$sql = "SELECT * FROM tweet_caches WHERE keyword_id = '".$connection->escape($account["keyword_id"])."' AND delete_flg = 0 ORDER BY retweet_count DESC LIMIT ".$connection->escape($account["pickup_count"]);
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
			if(is_array($tweets) && $account["pickup_count"] <= count($tweets)) break;
			
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
				$result = $connection->query($sql);
			}
				
		$max_id = "";
		if(is_array($search_caches) && count($search_caches) > 0){
			$max_id = $search_caches[0]["max_id"];
		}
		
		// アカウントグループのキーワードで検索します。
		$tweets = array();
		$twitter = getTwitter($keyword["account_id"]);
		$condition = array("q" => str_replace(" OR -", " -", str_replace(" ", " OR ", str_replace("　", " ", $keyword["keyword"]))), "lang" => "ja", "result_type" => "recent", "count" => 100);
		if(!empty($max_id)) $condition["max_id"] = $max_id;
		$result = $twitter->search_tweets($condition);
		foreach($result->statuses as $tweet){
			if(isset($tweet->retweeted_status) && !empty($tweet->retweeted_status)) $tweet = $tweet->retweeted_status;
			if($tweet->retweet_count > 0){
				$tweets[$tweet->id] = $tweet;
			}
		}
		
		
		if(preg_match("/max_id=([0-9]+)/", $result->search_metadata->next_results, $params) > 0){
			if(!empty($max_id)){
				if(isset($params[1]) && $params[1] > 0){
					$sql = "UPDATE tweet_search_cache SET max_id = '".$connection->escape($params[1])."' WHERE account_id = '".$connection->escape($keyword["account_id"])."'";
				}else{
					$sql = "DELETE FROM tweet_search_cache WHERE account_id = '".$connection->escape($keyword["account_id"])."'";
				}
			}else{
				$sql = "INSERT INTO tweet_search_cache (account_id, max_id) VALUES ('".$connection->escape($keyword["account_id"])."', '".$connection->escape($params[1])."')";
			}
			$result = $connection->query($sql);
		}
		
		foreach($tweets as $tweet){
			// Tweetの取得元がシステム上のものは除外
			$sql = "SELECT tweets.* FROM tweets WHERE post_id = '".$connection->escape($tweet->id)."'";
			$result = $connection->query($sql);
			$relatedTweets = $result->fetchAll();
			if(is_array($relatedTweets) && count($relatedTweets)) continue;
				
			// 画像がある場合は元画像をダウンロードする。
			if(isset($tweet->entities->media) && is_array($tweet->entities->media)){
				foreach($tweet->entities->media as $index => $media){
					$imageFilename = "/images/".$tweet->id."-".($index + 1);
					if(($fp = fopen(APP_ROOT.$imageFilename, "w+")) !== FALSE){
						fwrite($fp, file_get_contents($media->media_url));
						fclose($fp);
						@chmod(APP_ROOT.$imageFilename, 0666);
						// 画像を取得し、テキスト内のURLを削除
						$tweet->text = str_replace($media->url, "", $tweet->text);
					}
				}
			}
			
			$sqlval = array();
			$sqlval["keyword_id"] = $keyword["keyword_id"];
			$sqlval["post_id"] = $tweet->id;
			$sqlval["tweet_text"] = $tweet->text;
			$sqlval["favorite_count"] = $tweet->favorite_count;
			$sqlval["retweet_count"] = $tweet->retweet_count;
			
			// 同じ内容かつIDが別のツイートを取得
			$tweet_id = "";
			$sql = "SELECT * FROM tweet_caches WHERE keyword_id = '".$connection->escape($keyword["keyword_id"])."'";
			$sql .= " AND tweet_text = '".$connection->escape($tweet->text)."'";
			$sql .= " AND post_id != '".$connection->escape($tweet->id)."'";
			$result = $connection->query($sql);
			$sameTweets = $result->fetchAll();
			$result->close();
			if(isset($sameTweets) && count($sameTweets) > 0){
				foreach($sameTweets as $sameTweet){
					if($sameTweet["retweet_count"] < $sqlval["retweet_count"]){
						$tweet_id = $sameTweet["tweet_id"];
					}
				}
			}
			
			if(empty($tweet_id)){
				// 既に登録済みか調べる。(IDか内容が一致するものは除外する。)
				$sql = "SELECT * FROM tweet_caches WHERE keyword_id = '".$connection->escape($keyword["keyword_id"])."' AND post_id = '".$connection->escape($tweet->id)."'";
				$result = $connection->query($sql);
				$registeredTweets = $result->fetchAll();
				$result->close();
				if(is_array($registeredTweets) && count($registeredTweets) > 0){
					$tweet_id = $registeredTweets[0]["tweet_id"];
				}
			}

			if(!empty($tweet_id)){
				$sql = "SELECT * FROM tweet_caches WHERE tweet_id = '".$connection->escape($tweet_id)."'";
				$result = $connection->query($sql);
				$registeredTweets = $result->fetchAll();
				$result->close();
				if(is_array($registeredTweets) && count($registeredTweets) > 0){
					if($registeredTweets[0]["post_status"] == "1"){
						foreach($sqlval as $key => $value){
							$sqlval[$key] = $key." = '".$connection->escape($value)."'";
						}
						$sql = "UPDATE tweet_caches SET ".implode(", ", $sqlval);
						$sql .= " WHERE tweet_id = '".$connection->escape($tweet_id)."'";
						$result = $connection->query($sql);
					}
				}
			}else{
				foreach($sqlval as $key => $value){
					$sqlval[$key] = $connection->escape($value);
				}
				$sql = "INSERT INTO tweet_caches";
				$sql .= "(".implode(", ", array_keys($sqlval)).") VALUES ('".implode("', '", $sqlval)."')";
				$result = $connection->query($sql);
			}
		}
	}
}
