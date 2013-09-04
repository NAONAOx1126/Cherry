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
$_SERVER["REQUEST_URI"] = "/batch/UpdateStatus.php";

require_once(dirname(__FILE__)."/../require.php");

$connection = new Connection();
$result = $connection->query("SELECT accounts.* FROM accounts");
$accounts = $result->fetchAll();
$result->close();
if(is_array($accounts)){
	foreach($accounts as $account){
		// アカウントのツイートの状態を取得
		$twitter = getTwitter($account["account_id"]);
		$condition = array("user_id" => $account["user_id"], "count" => 200, "trim_user" => false, "exclude_replies" => true, "include_rts" => false);
		$tweets = (array) $twitter->statuses_userTimeline($condition);
		if(is_array($tweets) && count($tweets) > 0){
			foreach($tweets as $tweet){
					print_r($tweets);
				if($tweets->favorite_count > 0 || $tweets->rewteet_count){
					$connection->query("UPDATE tweets SET favorite_count = '".$connection->escape($tweet->favorite_count)."', retweet_count = '".$connection->escape($tweet->retweet_count)."' WHERE post_id = '".$connection->escape($tweet->id)."'");
				}
			}
		}
	}
}
