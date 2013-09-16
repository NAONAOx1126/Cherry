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
 * ツイート管理画面の表示を行います。
 */
require_once(dirname(__FILE__)."/require.php");

// ログインチェックを行う。
checkLoginAdministrator();

// 設定を更新
updateSetting();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex,nofollow" />
<title>CHERRY - Twitter自動投稿アプリ</title>
<link rel="stylesheet" href="<?php echo APP_SUBDIR; ?>/css/bootstrap.css" />
<link rel="stylesheet" href="<?php echo APP_SUBDIR; ?>/css/bootstrap-responsive.css" />
<style>
body {
	padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
	font-size: 10px;
}

h1 {
	font-size: 14px;
}
h2{
	font-size: 12px;
}
h3{
	font-size: 11px;
}
</style>
<script type="text/javascript" src="<?php echo APP_SUBDIR; ?>/js/bootstrap.js"></script>
</head>
<body>
<?php require(APP_ROOT."/parts/header.php"); ?>
<div class="container-fluid">
<div class="row-fluid">
<!--/span-->
<div class="span12">
	<form action="setting.php" method="POST">
	<table class="table table-bordered table-striped" summary="一覧">
	<?php $followers = array(10, 25, 50, 100, 250, 500, 1000); ?>
	<?php foreach($followers as $follower): ?>
	<tr>
	<th class="blue header">フォロワー<?php val($follower); ?>までの最大フォロー人数</th>
	<td><input type="text" name="max_follows_<?php val($follower); ?>" class="input-small" value="<?php val($_SESSION["ADMINISTRATOR"]["max_follows_".$follower]); ?>" />人</td>
	</tr>
	<?php endforeach; ?>
	<tr>
	<th class="blue header">フォロワー<?php val($follower); ?>人以上の時のフォロー人数比</th>
	<td>フォロワーの<input type="text" name="max_follows_rate_over_<?php val($follower); ?>" class="input-small" value="<?php val($_SESSION["ADMINISTRATOR"]["max_follows_rate_over_".$follower]); ?>" />％</td>
	</tr>
	<tr><td colspan="2">
	<input type="submit" class="btn" name="update" value="設定を更新" />
	</td></tr>
	</form>
	</table>
</div>
</div>
</div>
</body>
</html>
