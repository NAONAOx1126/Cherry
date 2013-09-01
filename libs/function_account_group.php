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

// アカウントグループを取得
function getAccountGroups(){
	$connection = new Connection();
	$result = $connection->query("SELECT * FROM account_groups WHERE administrator_id = '".$_SESSION["ADMINISTRATOR"]["administrator_id"]."'");
	return $result->fetchAll();
}

// アカウントグループを登録
function registerAccountGroup(){
	if(!empty($_POST["register"])){
		$connection = new Connection();
		$sqlval = array();
		$sqlval["administrator_id"] = $_SESSION["ADMINISTRATOR"]["administrator_id"];
		$sqlval["account_group_name"] = $_POST["account_group_name"];
		$sqlval["keyword"] = $_POST["keyword"];
		$sqlval["pickup_limit"] = $_POST["pickup_limit"];
		$sqlval["pickup_count"] = $_POST["pickup_count"];
		$sqlval["post_interval"] = $_POST["post_interval"];
		if(!empty($_POST["account_group_id"])){
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $key." = '".$connection->escape($value)."'";
			}
			$sql = "UPDATE account_groups SET ".implode(", ", $sqlval);
			$sql .= " WHERE account_group_id = '".$connection->escape($_POST["account_group_id"])."'";
			$result = $connection->query($sql);
		}else{
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $connection->escape($value);
			}
			$sql = "INSERT INTO account_groups";
			$sql .= "(".implode(", ", array_keys($sqlval)).") VALUES ('".implode("', '", $sqlval)."')";
			$result = $connection->query($sql);
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		header('Location: account_groups.php');
		exit;
	}
}

// アカウントグループを削除
function deleteAccountGroup(){
	if(!empty($_GET["delete"])){
		$connection = new Connection();
		if(!empty($_GET["account_group_id"])){
			$result = $connection->query("DELETE FROM account_groups WHERE account_group_id = '".$connection->escape($_GET["account_group_id"])."'");
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		header('Location: account_groups.php');
		exit;
	}
}
