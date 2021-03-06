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

// アカウントグループを削除
updateTweet();

// アカウントグループを取得
$tweets = getPostedTweets($_POST["account_id"]);
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
	<form action="posted_tweets.php" method="POST">
	<input type="hidden" name="account_id" value="<?php val($_POST["account_id"]); ?>" />
	<input type="text" class="input-small" name="past_days" value="7" />日以上経過している、リツイート数が
	<input type="text" class="input-small" name="retweets" value="20" />未満のツイートを
	<input type="submit" class="btn" name="delete" value="削除" onclick="return confirm('削除します。よろしいですか？');" />
	</form>
	<table class="table table-bordered table-striped" summary="一覧">
	<form action="posted_tweets.php" method="POST">
	<input type="hidden" name="account_id" value="<?php val($_POST["account_id"]); ?>" />
	<tr><td colspan="8">
	<input type="submit" class="btn" name="delete" value="チェックした投稿を削除" onclick="return confirm('削除します。よろしいですか？');" />
	<input type="submit" class="btn" name="update" value="投稿を更新" />
	</td></tr>
	<tr>
		<th class="blue header"></th>
		<th class="blue header">テキスト</th>
		<th class="blue header">画像</th>
		<th class="blue header">元RT数</th>
		<th class="blue header">RT数</th>
		<th class="blue header">投稿日時</th>
		<th class="blue header">削除</th>
	</tr>
	<?php foreach($tweets as $tweet): ?>
	<tr>
		<td><input type="checkbox" name="tweet_ids[]" value="<?php val($tweet["tweet_id"]); ?>" /></td>
		<td >
		<?php if($tweet["post_status"] == "2"): ?>
		<?php val($tweet["tweet_text"]); ?>
		<?php else: ?>
		<textarea name="tweet_text[<?php val($tweet["tweet_id"]); ?>]" class="span8" rows="5"><?php val($tweet["tweet_text"]); ?></textarea>
		<?php endif; ?>
		</td>
		<td>
			<?php $index = 1; while(file_exists(APP_ROOT."/thumbnails/".$tweet["source_post_id"]."-".$index)): ?>
			<a href="<?php val(APP_SUBDIR."/images/".$tweet["source_post_id"]."-".($index)); ?>" target="_blank">
			<img src="<?php val(APP_SUBDIR."/thumbnails/".$tweet["source_post_id"]."-".($index ++)); ?>" />
			</a>
			<?php endwhile; ?>
		</td>
		<td><?php val($tweet["source_retweet_count"]); ?></td>
		<td><?php val($tweet["retweet_count"]); ?></td>
		<td><?php val($tweet["post_time"]); ?></td>
		<td><a class="btn" href="posted_tweets.php?delete=1&account_id=<?php val($tweet["account_id"]); ?>&tweet_id=<?php val($tweet["tweet_id"]); ?>" onclick="return confirm('削除します。よろしいですか？')">削除</a></td>
	</tr>
	<?php endforeach; ?>
	<tr><td colspan="8">
	<input type="submit" class="btn" name="delete" value="チェックした投稿を削除" onclick="return confirm('削除します。よろしいですか？');" />
	<input type="submit" class="btn" name="update" value="投稿を更新" />
	</td></tr>
	</form>
	</table>
</div>
</div>
</div>
</body>
</html>
