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

// キーワードのリストを取得する
$connection = new Connection();
$sql = "SELECT keywords.*, accounts.account_id, account.keyword AS ngword";
$sql .= " FROM keywords, account_groups, accounts";
$sql .= " WHERE (keywords.keyword_id = account_groups.keyword_id1";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id2";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id3";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id4";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id5";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id6";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id7";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id8)";
$sql .= " AND account_groups.account_group_id = accounts.account_group_id";
$sql .= " AND keywords.delete_flg = 0";
$result = $connection->query($sql);
$keywords = $result->fetchAll();
$result->close();

if(is_array($keywords)){
	foreach($keywords as $keyword){
		// アカウントグループのキーワードで検索します。
		$twitter = getTwitter($keyword["account_id"]);
		
		$ngwords = explode(" ", str_replace("　", " ", $keyword["ngword"]));
		$keyw = str_replace("　", " ", $keyword["keyword"]);
		if(is_array($ngwords)){
		    foreach($ngwords as $ng){
		        $keyw .= " -".$ng;
		    }
		}
		$rootUsers = (array) $twitter->users_search(array("q" => $keyw, "page" => "1", "count" => "20"));
		unset($rootUsers["httpstatus"]);
		
		// 検索したユーザーからランダムで3人をルートとして登録
        for($i = 0; $i < 3; $i ++){
            $targetIndex = floor(mt_rand(0, count($rootUsers)));
            if($targetIndex < count($rootUsers)){
                $rootUser = $rootUsers[$targetIndex];
                $connection->query("INSERT IGNORE INTO follower_caches(account_id, user_id, depth) VALUES ('".$keyword["account_id"]."', '".$rootUser->id."', '1')");
            }
        }
    }
}
