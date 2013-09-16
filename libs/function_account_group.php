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
		// キーワードデータを取得
		$keyword_ids = array();
		for($i = 1; $i < 9; $i ++){
			if($_POST["keyword_id".$i] > 0){
				$keyword_ids[] = $_POST["keyword_id".$i];
			}
		}
		$sql = "SELECT * FROM keywords WHERE keyword_id IN (".implode(", ", $keyword_ids).")";
		$result = $connection->query($sql);
		$datas = $result->fetchAll();
		$result->close();
		
		// キーワードを取得
		$keywords = array();
		foreach($datas as $k){
			if(!empty($k["keyword"])){
				$keywords[] = $k["keyword"];
			}
		}
		$sqlval = array();
		$sqlval["administrator_id"] = $_SESSION["ADMINISTRATOR"]["administrator_id"];
		$sqlval["account_group_name"] = $_POST["account_group_name"];
		$sqlval["keyword_id1"] = $_POST["keyword_id1"];
		$sqlval["keyword_id2"] = $_POST["keyword_id2"];
		$sqlval["keyword_id3"] = $_POST["keyword_id3"];
		$sqlval["keyword_id4"] = $_POST["keyword_id4"];
		$sqlval["keyword_id5"] = $_POST["keyword_id5"];
		$sqlval["keyword_id6"] = $_POST["keyword_id6"];
		$sqlval["keyword_id7"] = $_POST["keyword_id7"];
		$sqlval["keyword_id8"] = $_POST["keyword_id8"];
		$sqlval["pickup_limit"] = $_POST["pickup_limit"];
		$sqlval["pickup_count"] = $_POST["pickup_count"];
		$sqlval["import_flg"] = $_POST["import_flg"];
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
		reload();
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
		reload();
	}
}
