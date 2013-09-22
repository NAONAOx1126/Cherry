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
$_SERVER["DOCUMENT_ROOT"] = $argv[2];
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
		// ルートユーザーからフォローターゲットを取得する。
		if(!empty($account["root_screen_name"])){
			$twitter = getTwitter($account["account_id"]);
			$rootUser = $twitter->users_show(array("screen_name" => $account["root_screen_name"]));
			$cursor = -1;
			while($cursor != 0){
				$followerIds = $twitter->followers_ids(array("user_id" => $rootUser->id, "cursor" => $cursor));
				foreach($followerIds->ids as $id){
					$connection->query("INSERT IGNORE INTO follower_caches(user_id, follower_user_id, depth) VALUES ('".$account["user_id"]."', '".$id."', '1')");
				}
				$cursor = $followerIds->next_cursor;
			}
		}
		// キーワードからフォローターゲットを取得する。
	}
}
