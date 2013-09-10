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

// タイムゾーンを設定
date_default_timezone_set("Asia/Tokyo");

// 入力のサニタイズ用関数
function sanitizeInput(){
	// マジッククオートを解除する関数
	function remove_magic_quote($value){
		if(get_magic_quotes_gpc() == "1"){
			if(is_array($value)){
				foreach($value as $i => $val){
					$value[$i] = remove_magic_quote($val);
				}
			}else{
				$value = str_replace("\\\"", "\"", $value);
				$value = str_replace("\\\'", "\'", $value);
				$value = str_replace("\\\\", "\\", $value);
			}
		}
		return $value;
	}

	// POSTとGETを統合
	foreach($_POST as $key => $value){
		$_GET[$key] = $value;
	}
	$_POST = $_GET = remove_magic_quote($_GET);
}
sanitizeInput();

// セッションを開始
session_start();

// アプリケーションのルートパス
$appRoot = realpath(dirname(__FILE__));
define("APP_ROOT", $appRoot);

// アプリケーションのサブディレクトリ
$docRoot = $_SERVER["DOCUMENT_ROOT"];
if(substr($docRoot, -1) == "/"){
	$docRoot = substr($docRoot, 0, strlen($docRoot) - 1);
}
$appSubdir = str_replace($docRoot, "", $appRoot);
define("APP_SUBDIR", $appSubdir);

// アプリケーションのURLパス
$appRootUrl = (($_SERVER["HTTPS"] == "on")?"https":"http")."://".$_SERVER["SERVER_NAME"].$appSubdir;
define("APP_ROOT_URL", $appRootUrl);

$appUrlPath = $_SERVER["REQUEST_URI"];
if(($pos = strpos($appUrlPath, "#")) > 0) $appUrlPath = substr($appUrlPath, 0, $pos);
if(($pos = strpos($appUrlPath, "?")) > 0) $appUrlPath = substr($appUrlPath, 0, $pos);
$appUrlPath = str_replace($appSubdir, "", $appUrlPath);
define("APP_URL_PATH", $appUrlPath);

// エラー変数の初期化
$_SERVER["ERRORS"] = array();

// 設定ファイルを読み込み
require_once(APP_ROOT."/configure/configure_".$_SERVER["SERVER_NAME"].".php");

// 共通で使用するクラスの呼び出し
require_once(APP_ROOT."/libs/Connection.php");
require_once(APP_ROOT."/libs/Result.php");
require_once(APP_ROOT."/libs/codebird.php");

// Twitterのコンシューマーキーを設定
\Codebird\Codebird::setConsumerKey(TWITTER_APP_ID, TWITTER_SECRET);

// 関数群を読み込み
require_once(APP_ROOT."/libs/function_keyword.php");
require_once(APP_ROOT."/libs/function_account_group.php");
require_once(APP_ROOT."/libs/function_account.php");
require_once(APP_ROOT."/libs/function_administrator.php");
require_once(APP_ROOT."/libs/function_tweet.php");
require_once(APP_ROOT."/libs/function_affiliate.php");

// HTML value用のエスケープ処理
function val($text){
	echo str_replace("\"", "&quot;", $text);
}

// Twitterの認証のアカウントを取得する。
function getTwitter($account_id){
	// 登録済みユーザーの認証を行う。
	$accessToken = "";
	$accessTokenSecret = "";
	if(!empty($account_id)){
		$connection = new Connection();
		$result = $connection->query("SELECT * FROM accounts WHERE account_id = '".$connection->escape($account_id)."'");
		$accounts = $result->fetchAll();
		if(is_array($accounts) && count($accounts) > 0){
			$twitter = \Codebird\Codebird::getInstance();
			$twitter->setToken($accounts[0]["access_token"], $accounts[0]["access_token_secret"]);
			return $twitter;
		}
	}
	return FALSE;
}
