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
function getKeywords(){
	$connection = new Connection();
	$result = $connection->query("SELECT * FROM keywords WHERE administrator_id = '".$_SESSION["ADMINISTRATOR"]["administrator_id"]."'");
	return $result->fetchAll();
}

// アカウントグループを登録
function registerKeyword(){
	if(!empty($_POST["register"])){
		$connection = new Connection();
		$sqlval = array();
		$sqlval["administrator_id"] = $_SESSION["ADMINISTRATOR"]["administrator_id"];
		$sqlval["keyword"] = $_POST["keyword"];
		if(!empty($_POST["keyword_id"])){
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $key." = '".$connection->escape($value)."'";
			}
			$sql = "UPDATE keywords SET ".implode(", ", $sqlval);
			$sql .= " WHERE keyword_id = '".$connection->escape($_POST["keyword_id"])."'";
			$result = $connection->query($sql);
		}else{
			foreach($sqlval as $key => $value){
				$sqlval[$key] = $connection->escape($value);
			}
			$sql = "INSERT INTO keywords";
			$sql .= "(".implode(", ", array_keys($sqlval)).") VALUES ('".implode("', '", $sqlval)."')";
			$result = $connection->query($sql);
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		header('Location: keywords.php');
		exit;
	}
}

// アカウントグループを削除
function deleteKeyword(){
	if(!empty($_GET["delete"])){
		$connection = new Connection();
		if(!empty($_GET["keyword_id"])){
			$result = $connection->query("UPDATE keywords SET delete_flg = 1 WHERE keyword_id = '".$connection->escape($_GET["keyword_id"])."'");
		}
		// GETパラメータを削除するため、自分のURLにリダイレクト
		header('Location: keywords.php');
		exit;
	}
}
