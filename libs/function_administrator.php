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

// ログインチェック用関数
function checkLoginAdministrator(){
	if(isset($_POST["login_id"]) && isset($_POST["password"])){
		$connection = new Connection();
		$result = $connection->query("SELECT * FROM administrators WHERE login_id = '".$_POST["login_id"]."' AND password = '".$_POST["password"]."'");
		$data = $result->fetchAll();
		if(count($data) > 0){
			$_SESSION["ADMINISTRATOR"] = $data[0];
		}
	}
	if(!is_array($_SESSION["ADMINISTRATOR"]) && !($_SESSION["ADMINISTRATOR"]["administrator_id"] > 0)){
		require(APP_ROOT."/login.php");
		exit;
	}
}

// 設定を更新
function updateSetting(){
	if(isset($_POST["update"])){
		$connection = new Connection();
		$sqlval = $_POST;
		unset($sqlval["update"]);
		$connection->update("administrators", $sqlval, "administrator_id", $_SESSION["ADMINISTRATOR"]["administrator_id"]);
		//reload();
	}
}

// 設定を更新
function updateAdministrator(){
	if($_SESSION["ADMINISTRATOR"]["administrator_id"] > 0){
		$connection = new Connection();
		$result = $connection->query("SELECT * FROM administrators WHERE administrator_id = '".$_SESSION["ADMINISTRATOR"]["administrator_id"]."'");
		$data = $result->fetchAll();
		if(count($data) > 0){
			$_SESSION["ADMINISTRATOR"] = $data[0];
		}
	}
}