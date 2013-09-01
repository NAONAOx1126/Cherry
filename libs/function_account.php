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

// Twitterの認証を行い、アカウントを登録する。
function addTwitterAccount($loginAs = ""){
	$_SERVER["TWITTER"] = \Codebird\Codebird::getInstance();

	if (isset($_POST['oauth_verifier']) && isset($_SESSION['TWITTER_VERIFIED']) && !empty($_SESSION['ACCOUNT_GROUP_ID'])) {
		// トークンを認証する。
		$_SERVER["TWITTER"]->setToken($_SESSION["TWITTER_ACCESS_TOKEN"], $_SESSION["TWITTER_ACCESS_TOKEN_SECRET"]);
		unset($_SESSION['TWITTER_ACCESS_TOKEN']);
		unset($_SESSION['TWITTER_ACCESS_TOKEN_SECRET']);
		unset($_SESSION['TWITTER_VERIFIED']);

		// アクセストークンを取得
		$reply = $_SERVER["TWITTER"]->oauth_accessToken(array('oauth_verifier' => $_GET['oauth_verifier']));

		// アカウント情報を登録
		$connection = new Connection();
		$sqlval = array();
		$sqlval["administrator_id"] = $_SESSION["ADMINISTRATOR"]["administrator_id"];
		$sqlval["account_group_id"] = $_SESSION["ACCOUNT_GROUP_ID"];
		$sqlval["user_id"] = $reply->user_id;
		$sqlval["screen_name"] = $reply->screen_name;
		$sqlval["access_token"] = $reply->oauth_token;
		$sqlval["access_token_secret"] = $reply->oauth_token_secret;
		$result = $connection->query("SELECT * FROM accounts WHERE user_id = '".$connection->escape($reply->user_id)."'");
		$accounts = $result->fetchAll();
		if(is_array($accounts) && count($accounts) > 0){
			// 登録済みの場合は更新
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $key." = '".$connection->escape($value)."'";
			}
			$sql = "UPDATE accounts SET ".implode(", ", $sqlval);
			$sql .= " WHERE account_id = '".$connection->escape($accounts[0]["account_id"])."'";
			$result = $connection->query($sql);
		}else{
			// 投稿間隔はグループのものをデフォルトに設定
			$result = $connection->query("SELECT * FROM account_groups WHERE account_group_id = '".$connection->escape($sqlval["account_group_id"])."'");
			$groups = $result->fetchAll();
			$sqlval["post_interval"] = "0";
			if(is_array($groups) && count($groups) > 0){
				$sqlval["post_interval"] = $groups[0]["post_interval"];
			}
			// 未登録の場合は追加
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $connection->escape($value);
			}
			$sql = "INSERT INTO accounts";
			$sql .= "(".implode(", ", array_keys($sqlval)).") VALUES ('".implode("', '", $sqlval)."')";
			$result = $connection->query($sql);
		}

		unset($_SESSION["ACCOUNT_GROUP_ID"]);
			
		// GETパラメータを削除するため、自分のURLにリダイレクト
		header('Location: accounts.php');
		exit;
	}elseif(!empty($_POST["add_account"]) && !empty($_POST["account_group_id"])){
		// リクエストトークンを取得
		$reply = $_SERVER["TWITTER"]->oauth_requestToken(array("callback_url" => APP_ROOT_URL.APP_URL_PATH));

		// トークンを保存する。
		$_SERVER["TWITTER"]->setToken($reply->oauth_token, $reply->oauth_token_secret);
		$_SESSION["ACCOUNT_GROUP_ID"] = $_POST["account_group_id"];
		$_SESSION["TWITTER_ACCESS_TOKEN"] = $reply->oauth_token;
		$_SESSION["TWITTER_ACCESS_TOKEN_SECRET"] = $reply->oauth_token_secret;
		$_SESSION['TWITTER_VERIFIED'] = true;

		// 認証サイトに移動
		$redirectTo = $_SERVER["TWITTER"]->oauth_authorize();
		header('Location: '.$redirectTo);
		exit;
	}
}

// アカウントの一覧を取得する。
function getAccounts($account_group_id = ""){
	$connection = new Connection();
	$sql = "SELECT * FROM accounts WHERE administrator_id = '".$_SESSION["ADMINISTRATOR"]["administrator_id"]."'";
	if($account_group_id > 0){
		$sql .= " AND account_group_id = '".$account_group_id."'";
	}
	$result = $connection->query($sql);
	return $result->fetchAll();
}

// アカウントを更新
function updateAccount(){
	if(!empty($_POST["update"]) && $_POST["account_id"]){
		$connection = new Connection();
		$sqlval = array();
		$sqlval["post_interval"] = $_POST["post_interval"];
		foreach($sqlval as $key => $value){
			$sqlval[$key] = $key." = '".$connection->escape($value)."'";
		}
		$sql = "UPDATE accounts SET ".implode(", ", $sqlval);
		$sql .= " WHERE account_id = '".$connection->escape($_POST["account_id"])."'";
		$result = $connection->query($sql);
		
		// GETパラメータを削除するため、自分のURLにリダイレクト
		header('Location: accounts.php');
		exit;
	}
}

// アカウントを削除
function deleteAccount(){
	if(!empty($_POST["delete"])){
		$connection = new Connection();
		if(!empty($_POST["account_id"])){
			$result = $connection->query("DELETE FROM accounts WHERE account_id = '".$connection->escape($_POST["account_id"])."'");
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		header('Location: accounts.php');
		exit;
	}
}