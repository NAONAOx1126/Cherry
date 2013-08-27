<?php
// アプリケーションのルートパス
$appRoot = realpath(dirname(__FILE__));
define("APP_ROOT", $appRoot);

// アプリケーションのサブディレクトリ
$appSubdir = str_replace($_SERVER["DOCUMENT_ROOT"], "", $appRoot);
define("APP_SUBDIR", $appSubdir);

// アプリケーションのURLパス
$appRootUrl = (($_SERVER["HTTPS"] == "on")?"https":"http")."://".$_SERVER["SERVER_NAME"].$appSubdir;
define("APP_ROOT_URL", $appRootUrl);

// エラー変数の初期化
$_SERVER["ERRORS"] = array();

// 共通で使用するクラスの呼び出し
require_once(APP_ROOT."/libs/Connection.php");
require_once(APP_ROOT."/libs/Result.php");

// ログインチェック用関数
function checkLogin(){
	if(isset($_POST["login_id"]) && isset($_POST["password"])){
		$connection = new Connection(array());
	}
}