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

define("MAX_SEARCH_TWEET_PAGES", 5);
define("MAX_SEARCH_USERS", 5);

// Twitterの投稿をキーワード検索する。
function searchTweets($twitter, $keyword){
	$tweets = array();
	$max_id = "";
	// ヒットしたツイートを取得（直近1000件でリツイートのあるもの）
	for($i = 0; $i < MAX_SEARCH_TWEET_PAGES; $i ++){
		$condition = array("q" => $keyword, "lang" => "ja", "result_type" => "recent", "count" => 100);
		if(!empty($max_id)) $condition["max_id"] = $max_id;
		$result = $twitter->search_tweets($condition);
		foreach($result->statuses as $tweet){
			if(!empty($tweet->retweeted_status)) $tweet = $tweet->retweeted_status;
			if($tweet->retweet_count > 0){
				$tweets[$tweet->id] = $tweet;
			}
		}
		if(preg_match("/max_id=([0-9]+)/", $result->search_metadata->next_results, $params) > 0){
			$max_id = $params[1];
		}
	}

	// ヒットしたユーザーを取得
	$condition = array("q" => $keyword, "page" => "1", "count" => MAX_SEARCH_USERS);
	$result = $twitter->users_search($condition);
	foreach($result as $user){
		$condition = array("user_id" => $user->id, "count" => 200, "trim_user" => false, "exclude_replies" => true, "include_rts" => false);
		$result = $twitter->statuses_userTimeline($condition);
		foreach($result as $tweet){
			if(!empty($tweet->retweeted_status)) $tweet = $tweet->retweeted_status;
			if($tweet->retweet_count > 0){
				$tweets[$tweet->id] = $tweet;
			}
		}
	}

	uasort($tweets, function($a, $b){
		return $a->retweet_count < $b->retweet_count;
	});
	return $tweets;
}

$connection = new Connection();
$result = $connection->query("SELECT * FROM account_groups");
$accountGroups = $result->fetchAll();
$result->close();
if(is_array($accountGroups)){
	foreach($accountGroups as $accountGroup){
		// アカウントグループのキーワードで検索します。
		$sql = "SELECT * FROM accounts WHERE account_group_id = '".$accountGroup["account_group_id"]."'";
		$result = $connection->query($sql);
		$accounts = $result->fetchAll();
		$result->close();
		if(is_array($accounts) && count($accounts) > 0){
			foreach($accounts as $account){
				// 既存のアカウントのランクを1000上げる
				$connection->query("UPDATE tweets SET rank = rank + 1000 WHERE account_id = '".$connection->escape($account["account_id"])."'");
			}
			$twitter = getTwitter($accounts[0]["account_id"]);
			$tweets = searchTweets($twitter, $accountGroup["keyword"]);
			if(is_array($tweets)){
				foreach($tweets as $tweet){
					// 日本語でないツイートは除外
					if($tweet->lang != "ja") continue;
					
					// 規定のリツイート数に見た無い場合は除外
					if($tweet->retweet_count < $accountGroup["pickup_limit"]) continue;
					
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
					foreach($accounts as $account){
						// Tweetを登録
						$sqlval = array();
						$sqlval["account_id"] = $account["account_id"];
						$sqlval["source_post_id"] = $tweet->id;
						$sqlval["tweet_text"] = $tweet->text;
						$sqlval["source_favorite_count"] = $tweet->favorite_count;
						$sqlval["source_retweet_count"] = $tweet->retweet_count;
						$sqlval["post_status"] = "1";
						$sqlval["rank"] = mt_rand(1, 1000);
						
						// 既に登録済みか調べる。
						$result = $connection->query("SELECT * FROM tweets WHERE account_id = '".$connection->escape($sqlval["account_id"])."' AND source_post_id = '".$connection->escape($sqlval["source_post_id"])."'");
						$registeredTweets = $result->fetchAll();
						if(is_array($registeredTweets) && count($registeredTweets) > 0){
							if($registeredTweets[0]["post_status"] == "1"){
								// 登録済み／未投稿の場合はrankを引き継いで更新
								$sqlval["rank"] = $registeredTweets[0]["rank"];
								foreach($sqlval as $key => $value){
									$sqlval[$key] = $key." = '".$connection->escape($value)."'";
								}
								$sql = "UPDATE tweets SET ".implode(", ", $sqlval);
								$sql .= " WHERE tweet_id = '".$connection->escape($registeredTweets[0]["tweet_id"])."'";
								$result = $connection->query($sql);
							}
						}else{
							foreach($sqlval as $key => $value){
								$sqlval[$key] = $connection->escape($value);
							}
							$sql = "INSERT INTO tweets";
							$sql .= "(".implode(", ", array_keys($sqlval)).") VALUES ('".implode("', '", $sqlval)."')";
							$result = $connection->query($sql);
						}
					}
				}
			}
		}
	}
}
