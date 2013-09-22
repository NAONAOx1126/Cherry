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
		// ルートユーザーからフォローターゲットを取得する。
		if(!empty($account["root_user_id"])){
			$cursor = -1;
			while($cursor != 0){
				$followerIds = $twitter->followers_ids(array("user_id" => $account["root_user_id"], "cursor" => $cursor));
				foreach($followerIds->ids as $id){
					$connection->query("INSERT IGNORE INTO follower_caches(user_id, follower_user_id, depth) VALUES ('".$rootUser->id."', '".$id."', '1')");
				}
				$cursor = $followerIds->next_cursor;
			}
		}
		// キーワードからフォローターゲットを取得する。
		if(!empty($account["root_keyword"])){
			$rootUsers = $twitter->users_search(array("q" => $account["root_keyword"], "count" => "5"));
			foreach($rootUsers as $rootUser){
				$connection->query("INSERT IGNORE INTO keyword_users(keyword, user_id) VALUES ('".$account["root_keyword"]."', '".$rootUser->id_str."')");
				// ルートユーザーからフォローターゲットを取得する。
				$cursor = -1;
				while($cursor != 0){
					$followerIds = $twitter->followers_ids(array("user_id" => $rootUser->id, "cursor" => $cursor));
					foreach($followerIds->ids as $id){
						$connection->query("INSERT IGNORE INTO follower_caches(user_id, follower_user_id, depth) VALUES ('".$rootUser->id."', '".$id."', '1')");
					}
					$cursor = $followerIds->next_cursor;
				}
			}
		}
	}
}
