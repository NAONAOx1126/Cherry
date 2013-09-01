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

// アカウントグループを削除
deleteTweet();

// アカウントグループを取得
$tweets = getTweets($_POST["account_id"]);
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
	<form action="tweets.php" method="POST">
	<input type="hidden" name="account_id" value="<?php echo $_POST["account_id"]; ?>" />
	<input type="text" class="input-small" name="past_days" value="7" />日以上経過している、リツイート数が
	<input type="text" class="input-small" name="retweets" value="20" />未満のツイートを
	<input type="submit" class="btn" name="delete" value="削除" />
	</form>
	<table class="table table-bordered table-striped" summary="一覧">
	<tr>
		<th class="blue header"></th>
		<th class="blue header">テキスト</th>
		<th class="blue header">投稿状態</th>
		<th class="blue header">元RT数</th>
		<th class="blue header">RT数</th>
		<th class="blue header">投稿日時</th>
		<th class="blue header">削除</th>
	</tr>
	<form action="tweets.php" method="POST">
	<input type="hidden" name="account_id" value="<?php echo $_POST["account_id"]; ?>" />
	<?php foreach($tweets as $tweet): ?>
	<tr>
		<td><input type="checkbox" name="tweet_ids[]" value="<?php echo $tweet["tweet_id"]; ?>" /></td>
		<td><?php echo $tweet["tweet_text"]; ?></td>
		<td><?php echo $_SERVER["TWEET_STATUS"][$tweet["post_status"]]; ?></td>
		<td><?php echo $tweet["source_retweet_count"]; ?></td>
		<td><?php echo $tweet["retweet_count"]; ?></td>
		<td><?php echo $tweet["post_time"]; ?></td>
		<td><a class="btn" href="tweets.php?delete=1&account_id=<?php echo $tweet["account_id"]; ?>&tweet_id=<?php echo $tweet["tweet_id"]; ?>" onclick="return confirm('削除します。よろしいですか？')">削除</a></td>
	</tr>
	<?php endforeach; ?>
	<tr><td colspan="7">
	チェックした投稿を
	<input type="submit" class="btn" name="delete" value="削除" />
	</td></tr>
	</form>
	</table>
</div>
</div>
</div>
</body>
</html>
