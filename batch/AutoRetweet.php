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

// リツイート可能なアカウントを取得する。
$sql = "SELECT accounts.*, retweet_groups.* FROM retweet_groups, retweet_group_accounts, accounts";
$sql .= " WHERE retweet_groups.retweet_group_id = retweet_group_accounts.retweet_group_id";
$sql .= " AND retweet_group_accounts.screen_name = accounts.screen_name";
$sql .= " AND UNIX_TIMESTAMP() - UNIX_TIMESTAMP(next_retweet)";
$result = $connection->query($sql);
$accounts = $result->fetchAll();
$result->close();
if(is_array($accounts)){
	foreach($accounts as $account){
		// 同じグループのアカウントを取得する。
		$sql = "SELECT * FROM retweet_group_accounts WHERE retweet_group_id = '".$account["retweet_group_id"]."'";
		$sql .= " AND screen_name != '".$account["screen_name"]."'";
		$result = $connection->query($sql);
		$targets = $result->fetchAll();
		$result->close();
		
		// ツイート停止時間帯の場合はスキップ
		if($account["tweet_suspend_start"] < $account["tweet_suspend_end"]){
			if($account["tweet_suspend_start"] < date("H") && date("H") < $account["tweet_suspend_end"]) continue;
		}elseif($account["tweet_suspend_end"] < $account["tweet_suspend_start"]){
			if($account["tweet_suspend_start"] < date("H") || date("H") < $account["tweet_suspend_end"]) continue;
		}
		
		$twitter = getTwitter($account["account_id"]);
				
		// アカウントリストからリツイート対象をピックアップ
		$tweets = array();
		if(is_array($targets)){
			foreach($targets as $target){
				$statuses = $twitter->status_userTimeline(array("user_id" => $target["user_id"], "count" => 200));
				if(is_array($statuses)){
					print_r($statuses);
					foreach($statuses as $status){
						// リツイート済みは対象外
						if($status->retweeted) continue;
						// 自分の投稿予定に含まれている場合は除外
						$sql = "SELECT my_tweets.* FROM tweets AS my_tweets, tweets";
						$sql .= " WHERE my_tweets.tweet_text = tweets.tweet_text";
						$sql .= " AND tweets.post_id = '".$status->id_str."'";
						$result = $connection->query($sql);
						if($result->count() > 0) continue;
						// 投稿をエントリー
						$tweets[] = $status;
					}
				}
			}
		}
		
		if(count($tweets) > 0){
			// エントリーされたツイートをソート
			usort($tweets, function($a, $b){
				if($a->retweet_count < $b->retweet_count) return -1;
				if($b->retweet_count < $a->retweet_count) return 1;
				return floor(mt_rand(0, 2)) - 1;
			});
			
			
			$twitter->status_retweet(array("id" => $tweets[0]->id));
			exit;
			
			$nextInterval = mt_rand($account["retweet_interval"] - $account["retweet_flactuation"], $account["retweet_interval"] + $account["retweet_flactuation"]);
			
			$sqlval = array();
			$sqlval["last_retweeted"] = date("Y-m-d H:i:s");
			$sqlval["next_retweet"] = date("Y-m-d H:i:s", strtotime("+".$nextInterval." minutes"));
			
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $key." = '".$this->escape($value)."'";
			}
			$sql = "UPDATE retweet_group_accounts SET ".implode(", ", $sqlval);
			$sql .= " WHERE retweet_group_id = '".$this->escape($account["retweet_group_id"])."'";
			$sql .= " WHERE user_id = '".$this->escape($account["user_id"])."'";
			$result = $this->query($sql);
		}
	}
}
