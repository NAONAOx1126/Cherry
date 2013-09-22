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

// アカウントを更新
updateAdministrator();
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
	<?php $followers = array(50, 100, 200, 400, 800, 1200, 1600, 2000); ?>
	<th class="blue header">フォローワー数</th>
	<th class="blue header">最大フォロー比率／フォロワー数</th>
	<th class="blue header">最大フォロー数／日</th>
	<th class="blue header">最大アンフォロー数／日</th>
	<?php foreach($followers as $follower): ?>
	<tr>
	<th class="blue header"><?php val($follower); ?>人まで</th>
	<td><input type="text" name="max_follows_<?php val($follower); ?>" class="input-small" value="<?php val($_SESSION["ADMINISTRATOR"]["max_follows_".$follower]); ?>" />%</td>
	<td><input type="text" name="daily_follows_<?php val($follower); ?>" class="input-small" value="<?php val($_SESSION["ADMINISTRATOR"]["daily_follows_".$follower]); ?>" />人</td>
	<td><input type="text" name="daily_unfollows_<?php val($follower); ?>" class="input-small" value="<?php val($_SESSION["ADMINISTRATOR"]["daily_unfollows_".$follower]); ?>" />人</td>
	</tr>
	<?php endforeach; ?>
	<tr>
	<th class="blue header"><?php val($follower); ?>人以上</th>
	<td><input type="text" name="max_follows_over_<?php val($follower); ?>" class="input-small" value="<?php val($_SESSION["ADMINISTRATOR"]["max_follows_over_".$follower]); ?>" />%</td>
	<td><input type="text" name="daily_follows_over_<?php val($follower); ?>" class="input-small" value="<?php val($_SESSION["ADMINISTRATOR"]["daily_follows_over_".$follower]); ?>" />人</td>
	<td><input type="text" name="daily_unfollows_over_<?php val($follower); ?>" class="input-small" value="<?php val($_SESSION["ADMINISTRATOR"]["daily_unfollows_over_".$follower]); ?>" />人</td>
	</tr>
	<tr>
	<th class="blue header">フォロワー追跡の深度</th>
	<td colspan="3"><select name="tree_depth">
	<?php for($i = 0; $i < 5; $i ++): ?>
	<option value="<?php val($i); ?>"<?php echo ($_SESSION["ADMINISTRATOR"]["tree_depth"] == $i)?" selected":""; ?>><?php val($i); ?></option>
	<?php endfor; ?>
	</select>階層まで</td>
	</tr>
	<tr>
	<th class="blue header">除外条件</th>
	<td colspan="3">
		<input type="hidden" name="ignore_non_japanese_flg" value="0" />
		<input type="checkbox" name="ignore_non_japanese_flg" value="1"<?php echo ($_SESSION["ADMINISTRATOR"]["ignore_non_japanese_flg"] == "1")?" checked":""; ?> /> 外国人／
		<input type="hidden" name="ignore_bot_flg" value="0" />
		<input type="checkbox" name="ignore_bot_flg" value="1"<?php echo ($_SESSION["ADMINISTRATOR"]["ignore_bot_flg"] == "1")?" checked":""; ?> /> ボット／
		<input type="hidden" name="ignore_url_flg" value="0" />
		<input type="checkbox" name="ignore_url_flg" value="1"<?php echo ($_SESSION["ADMINISTRATOR"]["ignore_url_flg"] == "1")?" checked":""; ?> /> 含URL
	</td>
	</tr>
	<tr>
	<th class="blue header">フォロー返しを待つ日数</th>
	<td colspan="3"><input type="text" name="unfollow_limit" class="input-small" value="<?php val($_SESSION["ADMINISTRATOR"]["unfollow_limit"]); ?>" />日</td>
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
