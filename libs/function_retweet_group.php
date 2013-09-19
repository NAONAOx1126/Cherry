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

// リツイートグループを取得
function getRetweetGroups(){
	$connection = new Connection();
	$sql = "SELECT * FROM retweet_groups";
	$sql .= " WHERE administrator_id = '".$_SESSION["ADMINISTRATOR"]["administrator_id"]."'";
	$result = $connection->query($sql);
	return $result->fetchAll();
}

// リツイートグループを登録
function registerRetweetGroup(){
	if(!empty($_POST["register"])){
		$connection = new Connection();
		$sqlval = array();
		$sqlval["administrator_id"] = $_SESSION["ADMINISTRATOR"]["administrator_id"];
		$sqlval["retweet_group_name"] = $_POST["retweet_group_name"];
		if(!empty($_POST["retweet_group_id"])){
			$connection->update("retweet_groups", $sqlval, "retweet_group_id", $_POST["retweet_group_id"]);
		}else{
			$connection->insert("retweet_groups", $sqlval);
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		reload();
	}
}

// リツイートグループを削除
function deleteRetweetGroup(){
	if(!empty($_GET["delete"])){
		$connection = new Connection();
		if(!empty($_GET["retweet_group_id"])){
			$sql = "DELETE FROM retweet_groups";
			$sql .= " WHERE retweet_group_id = '".$connection->escape($_POST["retweet_group_id"])."'";
			$result = $connection->query($sql);
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		reload();
	}
}

// グループメンバーを追加
function addRetweetGroupMember(){
	if(!empty($_POST["add_member"])){
		$connection = new Connection();
		
		$result = $connection->query("SELECT * FROM retweet_group_accounts WHERE retweet_group_id = '".$connection->escape($_POST["retweet_group_id"])."' AND screen_name = '".$_POST["screen_name"]."'");
		$c_retweet_group_account = $result->count();
		$result->close();
		
		// 未登録の場合のみ登録処理を実行
		if($c_retweet_group_account == 0){
			// 登録済みアカウントの件数を取得
			$sql = "SELECT accounts.* FROM retweet_group_accounts, accounts";
			$sql .= " WHERE retweet_group_accounts.screen_name = accounts.screen_name";
			$sql .= " AND retweet_group_id = '".$connection->escape($_POST["retweet_group_id"])."'";
			$result = $connection->query($sql);
			$accounts = $result->fetchAll();
			$result->close();
			if(count($accounts) == 0){
				$sql = "SELECT * FROM accounts";
				$sql .= " WHERE screen_name = '".$connection->escape($_POST["screen_name"])."'";
				$result = $connection->query($sql);
				$accounts = $result->fetchAll();
			}
			
			if(count($accounts) > 0){
				$twitter = getTwitter($accounts[0]["account_id"]);
				$user = $twitter->users_show(array("screen_name" => $_POST["screen_name"]));
		
				if($user->id > 0){
					$sqlval = array();
					$sqlval["retweet_group_id"] = $_POST["retweet_group_id"];
					$sqlval["user_id"] = $user->id;
					$sqlval["screen_name"] = $user->screen_name;
					$connection->insert("retweet_group_accounts", $sqlval);
				}
			}
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		reload();
	}
}

// グループメンバーを削除
function removeRetweetGroupMember(){
	if(!empty($_POST["remove_member"])){
		$connection = new Connection();

		if(!empty($_POST["screen_name"])){
			$result = $connection->query("DELETE FROM retweet_group_accounts WHERE retweet_group_id = '".$connection->escape($_POST["retweet_group_id"])."' AND screen_name = '".$connection->escape($_POST["screen_name"])."'");
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		reload();
	}
}

// グループメンバーを取得
function getRetweetGroupMember($retweet_group_id){
	$connection = new Connection();
	$result = $connection->query("SELECT * FROM retweet_group_accounts WHERE retweet_group_id = '".$connection->escape($retweet_group_id)."'");
	return $result->fetchAll();
}
