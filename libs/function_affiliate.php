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

// アフィリエイトを取得
function getAffiliates($account_id){
	$connection = new Connection();
	$result = $connection->query("SELECT * FROM accounts WHERE account_id = '".$connection->escape($account_id)."' AND administrator_id = '".$connection->escape($_SESSION["ADMINISTRATOR"]["administrator_id"])."'");
	$accounts = $result->fetchAll();
	if(is_array($accounts) && count($accounts) > 0){
		$result = $connection->query("SELECT * FROM affiliates WHERE account_id = '".$connection->escape($account_id)."'");
		return $result->fetchAll();
	}
	return array();
}

// アフィリエイトを削除
function deleteAffiliate(){
	if(!empty($_POST["delete"])){
		$connection = new Connection();
		$result = $connection->query("SELECT * FROM accounts WHERE account_id = '".$connection->escape($_POST["account_id"])."' AND administrator_id = '".$connection->escape($_SESSION["ADMINISTRATOR"]["administrator_id"])."'");
		$accounts = $result->fetchAll();
		if(is_array($accounts) && count($accounts) > 0){
			if($_POST["affiliate_id"] > 0){
				$_POST["affiliate_ids"] = array($_POST["affiliate_id"]);
			}
			if(is_array($_POST["affiliate_ids"]) && count($_POST["affiliate_ids"]) > 0){
				foreach($_POST["affiliate_ids"] as $index => $affiliate_id){
					$_POST["affiliate_ids"][$index] = $connection->escape($affiliate_id);
				}
				$result = $connection->query("SELECT * FROM affiliates WHERE account_id = '".$accounts[0]["account_id"]."' AND affiliate_id IN ('".implode("', '", $_POST["affiliate_ids"])."')");
				$affiliates = $result->fetchAll();
				if(is_array($affiliates) && count($affiliates) > 0){
					foreach($affiliates as $affiliate){
						$connection->query("DELETE FROM affiliates WHERE affiliate_id = '".$connection->escape($affiliate["affiliate_id"])."'");
					}
				}
			}
			
			// GETパラメータを削除するため、自分のURLにリダイレクト
			reload("account_id=".$_POST["account_id"]);
		}
	}
}

// アフィリエイトを追加
function registerAffiliate(){
	if(!empty($_POST["register"])){
		$connection = new Connection();
		$result = $connection->query("SELECT * FROM accounts WHERE account_id = '".$connection->escape($_POST["account_id"])."' AND administrator_id = '".$connection->escape($_SESSION["ADMINISTRATOR"]["administrator_id"])."'");
		$accounts = $result->fetchAll();
		if(is_array($accounts) && count($accounts) > 0){
			$sqlval = array();
			$sqlval["account_id"] = $accounts[0]["account_id"];
			$sqlval["tweet_text"] = $_POST["tweet_text"];
			$sqlval["frequency"] = $_POST["frequency"];
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $connection->escape($value);
			}
			$sql = "INSERT INTO affiliates";
			$sql .= "(".implode(", ", array_keys($sqlval)).") VALUES ('".implode("', '", $sqlval)."')";
			$result = $connection->query($sql);
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		reload("account_id=".$_POST["account_id"]);
	}
}

// アフィリエイトを更新
function updateAffiliate(){
	if(!empty($_POST["update"])){
		$connection = new Connection();
		$result = $connection->query("SELECT * FROM accounts WHERE account_id = '".$connection->escape($_POST["account_id"])."' AND administrator_id = '".$connection->escape($_SESSION["ADMINISTRATOR"]["administrator_id"])."'");
		$accounts = $result->fetchAll();
		if(is_array($accounts) && count($accounts) > 0){
			if(is_array($_POST["tweet_text"]) && count($_POST["tweet_text"]) > 0){
				foreach($_POST["tweet_text"] as $affiliate_id => $tweet_text){
					if(!($_POST["frequency"][$affiliate_id] >= 0)){
						$_POST["frequency"][$affiliate_id] = "1";
					}
					$connection->query("UPDATE affiliates SET tweet_text = '".$connection->escape($tweet_text)."', frequency = '".$connection->escape($_POST["frequency"][$affiliate_id])."' WHERE affiliate_id = '".$connection->escape($affiliate_id)."'");
				}
			}
				
			// GETパラメータを削除するため、自分のURLにリダイレクト
			reload("account_id=".$_POST["account_id"]);
		}
	}
}
