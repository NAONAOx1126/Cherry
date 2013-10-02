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
 * アカウント管理画面の表示を行います。
 */
require_once(dirname(__FILE__)."/require.php");

// ログインチェックを行う。
checkLoginAdministrator();

// アカウントを更新
updateAccount();

// アカウントグループを取得
$accountGroups = getAccountGroups();

// アカウントを取得
$account = getAccount($_POST["account_id"]);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex,nofollow" />
<title>CHERRY - Twitter自動投稿アプリ</title>
<link rel="stylesheet" href="<?php val(APP_SUBDIR); ?>/css/bootstrap.css" />
<link rel="stylesheet" href="<?php val(APP_SUBDIR); ?>/css/bootstrap-responsive.css" />
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
<script type="text/javascript" src="<?php val(APP_SUBDIR); ?>/js/bootstrap.js"></script>
</head>
<body>
<?php require(APP_ROOT."/parts/header.php"); ?>
<div class="container-fluid">
<div class="row-fluid">
<!--/span-->
<div class="span12">
	<form action="account_details.php" method="POST">
	<input type="hidden" name="account_id" value="<?php val($account["account_id"]); ?>" />
	<table class="table table-bordered table-striped" summary="一覧">
	<tr>
		<th class="blue header">アカウントグループ</th>
		<td>
		<select name="account_group_id">
		<?php foreach($accountGroups as $accountGroup): ?>
		<option value="<?php val($accountGroup["account_group_id"]); ?>"<?php val(($accountGroup["account_group_id"] == $account["account_group_id"])?" selected":""); ?>><?php val($accountGroup["account_group_name"]); ?></option>
		<?php endforeach; ?>
		</select>
		</td>
	</tr>
	<tr>
		<th class="blue header">TwitterユーザーID</th>
		<td><?php val($account["user_id"]); ?></td>
	</tr>
	<tr>
		<th class="blue header">Twitterユーザー名</th>
		<td><?php val($account["screen_name"]); ?></td>
	</tr>
	<tr>
		<th class="blue header">投稿間隔</th>
		<td>
			<input type="radio" name="post_interval" value="0"<?php if($account["post_interval"] == "0") val(" checked"); ?> />更新停止&nbsp;
			<input type="radio" name="post_interval" value="30"<?php if($account["post_interval"] == 30) val(" checked"); ?> />30分毎&nbsp;
			<input type="radio" name="post_interval" value="60"<?php if($account["post_interval"] == 60) val(" checked"); ?> />1時間毎&nbsp;
			<input type="radio" name="post_interval" value="120"<?php if($account["post_interval"] == 120) val(" checked"); ?> />2時間毎<br />
			<input type="radio" name="post_interval" value="180"<?php if($account["post_interval"] == 180) val(" checked"); ?> />3時間毎&nbsp;
			<input type="radio" name="post_interval" value="240"<?php if($account["post_interval"] == 240) val(" checked"); ?> />4時間毎&nbsp;
			<input type="radio" name="post_interval" value="300"<?php if($account["post_interval"] == 300) val(" checked"); ?> />5時間毎&nbsp;
			<input type="radio" name="post_interval" value="360"<?php if($account["post_interval"] == 360) val(" checked"); ?> />6時間毎<br />
			<input type="radio" name="post_interval" value="420"<?php if($account["post_interval"] == 420) val(" checked"); ?> />7時間毎&nbsp;
			<input type="radio" name="post_interval" value="480"<?php if($account["post_interval"] == 480) val(" checked"); ?> />8時間毎&nbsp;
			<input type="radio" name="post_interval" value="540"<?php if($account["post_interval"] == 540) val(" checked"); ?> />9時間毎&nbsp;
			<input type="radio" name="post_interval" value="600"<?php if($account["post_interval"] == 600) val(" checked"); ?> />10時間毎<br />
			<input type="radio" name="post_interval" value="660"<?php if($account["post_interval"] == 660) val(" checked"); ?> />11時間毎&nbsp;
			<input type="radio" name="post_interval" value="720"<?php if($account["post_interval"] == 720) val(" checked"); ?> />12時間毎&nbsp;
			<input type="radio" name="post_interval" value="780"<?php if($account["post_interval"] == 780) val(" checked"); ?> />13時間毎&nbsp;
			<input type="radio" name="post_interval" value="840"<?php if($account["post_interval"] == 840) val(" checked"); ?> />14時間毎<br />
			<input type="radio" name="post_interval" value="900"<?php if($account["post_interval"] == 900) val(" checked"); ?> />15時間毎&nbsp;
			<input type="radio" name="post_interval" value="960"<?php if($account["post_interval"] == 960) val(" checked"); ?> />16時間毎&nbsp;
			<input type="radio" name="post_interval" value="1020"<?php if($account["post_interval"] == 1020) val(" checked"); ?> />17時間毎&nbsp;
			<input type="radio" name="post_interval" value="1080"<?php if($account["post_interval"] == 1080) val(" checked"); ?> />18時間毎<br />
			<input type="radio" name="post_interval" value="1140"<?php if($account["post_interval"] == 1140) val(" checked"); ?> />19時間毎&nbsp;
			<input type="radio" name="post_interval" value="1200"<?php if($account["post_interval"] == 1200) val(" checked"); ?> />20時間毎&nbsp;
			<input type="radio" name="post_interval" value="1260"<?php if($account["post_interval"] == 1260) val(" checked"); ?> />21時間毎&nbsp;
			<input type="radio" name="post_interval" value="1320"<?php if($account["post_interval"] == 1320) val(" checked"); ?> />22時間毎<br />
			<input type="radio" name="post_interval" value="1380"<?php if($account["post_interval"] == 1380) val(" checked"); ?> />23時間毎&nbsp;
			<input type="radio" name="post_interval" value="1440"<?php if($account["post_interval"] == 1440) val(" checked"); ?> />24時間毎<br />
			前後<input type="text" class="input-mini" name="post_flactuation" value="<?php val($account["post_flactuation"]); ?>" />分の揺らぎを持たせる。
		</td>
	</tr>
	<tr>
		<th class="blue header">投稿順序</th>
		<td>
			<input type="radio" name="post_order" value="1"<?php if($account["post_order"] == "1") val(" checked"); ?> />RT数順&nbsp;
			<input type="radio" name="post_order" value="2"<?php if($account["post_order"] == "2") val(" checked"); ?> />ランダム&nbsp;<br />
		</td>
	</tr>
	<tr>
		<th class="blue header">リツイート間隔</th>
		<td>
			<input type="radio" name="retweet_interval" value="0"<?php if($account["retweet_interval"] == "0") val(" checked"); ?> />更新停止&nbsp;
			<input type="radio" name="retweet_interval" value="30"<?php if($account["retweet_interval"] == 30) val(" checked"); ?> />30分毎&nbsp;
			<input type="radio" name="retweet_interval" value="60"<?php if($account["retweet_interval"] == 60) val(" checked"); ?> />1時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="120"<?php if($account["retweet_interval"] == 120) val(" checked"); ?> />2時間毎<br />
			<input type="radio" name="retweet_interval" value="180"<?php if($account["retweet_interval"] == 180) val(" checked"); ?> />3時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="240"<?php if($account["retweet_interval"] == 240) val(" checked"); ?> />4時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="300"<?php if($account["retweet_interval"] == 300) val(" checked"); ?> />5時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="360"<?php if($account["retweet_interval"] == 360) val(" checked"); ?> />6時間毎<br />
			<input type="radio" name="retweet_interval" value="420"<?php if($account["post_interval"] == 420) val(" checked"); ?> />7時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="480"<?php if($account["post_interval"] == 480) val(" checked"); ?> />8時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="540"<?php if($account["post_interval"] == 540) val(" checked"); ?> />9時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="600"<?php if($account["post_interval"] == 600) val(" checked"); ?> />10時間毎<br />
			<input type="radio" name="retweet_interval" value="660"<?php if($account["post_interval"] == 660) val(" checked"); ?> />11時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="720"<?php if($account["post_interval"] == 720) val(" checked"); ?> />12時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="780"<?php if($account["post_interval"] == 780) val(" checked"); ?> />13時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="840"<?php if($account["post_interval"] == 840) val(" checked"); ?> />14時間毎<br />
			<input type="radio" name="retweet_interval" value="900"<?php if($account["post_interval"] == 900) val(" checked"); ?> />15時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="960"<?php if($account["post_interval"] == 960) val(" checked"); ?> />16時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="1020"<?php if($account["post_interval"] == 1020) val(" checked"); ?> />17時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="1080"<?php if($account["post_interval"] == 1080) val(" checked"); ?> />18時間毎<br />
			<input type="radio" name="retweet_interval" value="1140"<?php if($account["post_interval"] == 1140) val(" checked"); ?> />19時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="1200"<?php if($account["post_interval"] == 1200) val(" checked"); ?> />20時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="1260"<?php if($account["post_interval"] == 1260) val(" checked"); ?> />21時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="1320"<?php if($account["post_interval"] == 1320) val(" checked"); ?> />22時間毎<br />
			<input type="radio" name="retweet_interval" value="1380"<?php if($account["post_interval"] == 1380) val(" checked"); ?> />23時間毎&nbsp;
			<input type="radio" name="retweet_interval" value="1440"<?php if($account["post_interval"] == 1440) val(" checked"); ?> />24時間毎<br />
			前後<input type="text" class="input-mini" name="retweet_flactuation" value="<?php val($account["retweet_flactuation"]); ?>" />分の揺らぎを持たせる。
		</td>
	</tr>
	<tr>
		<th class="blue header">アフィリエイト間隔</th>
		<td>
			<input type="text" name="affiliate_interval" value="<?php val($account["affiliate_interval"]); ?>" class="input-mini" />ツイート毎に<br>
			通常のツイートの代わりにアフィリエイトツイートにする。
		</td>
	</tr>
	<tr>
		<th class="blue header">投稿禁止時間</th>
		<td>
			<input type="text" name="tweet_suspend_start" value="<?php val($account["tweet_suspend_start"]); ?>" class="input-mini" />時〜
			<input type="text" name="tweet_suspend_end" value="<?php val($account["tweet_suspend_end"]); ?>" class="input-mini" />時は投稿しない。
		</td>
	</tr>
	<tr>
	<th class="blue header">フォロー基点のユーザー名</th>
	<td colspan="3"><input type="text" name="root_screen_name" class="input-medium" value="<?php val($account["root_screen_name"]); ?>" /></td>
	</tr>
	<tr>
	<th class="blue header">フォロー基点のキーワード</th>
	<td colspan="3"><input type="text" name="root_keyword" class="input-xxlarge" value="<?php val($account["root_keyword"]); ?>" /></td>
	</tr>
	<tr>
		<td><div class="btn-group">
			<input type="submit" class="btn" name="update" value="アカウントの設定を更新" />
			<a href="accounts.php" class="btn">アカウント一覧に戻る</a>
		</div></td>
	</tr>
	</table>
	</form>
</div>
</div>
</div>
</body>
</html>
