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

// Twitterの認証を行い、アカウントを追加する。
addTwitterAccount();

// アカウントを更新
updateAccount();

// アカウントを削除
deleteAccount();

// アカウントグループを取得
$accountGroups = getAccountGroups();

// アカウントを取得
$accounts = getAccounts();
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
	<?php if(is_array($accountGroups) && count($accountGroups) > 0): ?>
	<div>
		<form action="accounts.php" method="POST">
		<select name="account_group_id">
		<?php foreach($accountGroups as $accountGroup): ?>
		<option value="<?php val($accountGroup["account_group_id"]); ?>"><?php val($accountGroup["account_group_name"]); ?></option>
		<?php endforeach; ?>
		</select>
		<input type="submit" class="btn" name="add_account" value="新規アカウント追加" />
		</form>
	</div>
	<?php endif; ?>
	<br />
	<table class="table table-bordered table-striped" summary="一覧">
	<tr>
		<th class="blue header">Twitterユーザー</th>
		<th class="blue header">投稿間隔</th>
		<th class="blue header">投稿順序</th>
		<th class="blue header">投稿一覧</th>
		<th class="blue header">&nbsp;</th>
	</tr>
	<?php foreach($accounts as $account): ?>
	<form action="accounts.php" method="POST">
	<input type="hidden" name="account_id" value="<?php val($account["account_id"]); ?>" />
	<tr>
		<td><a href="account_details.php?account_id=<?php val($account["account_id"]); ?>">
			<?php val($account["screen_name"]); ?>
		</a></td>
		<td>
			ツイート：<?php if($account["post_interval"] > 0): ?><?php number_format(val($account["post_interval"] - $account["post_flactuation"])) ?>分〜
			<?php number_format(val($account["post_interval"] + $account["post_flactuation"])) ?>分毎に１回<?php else: ?>投稿しない<?php endif; ?><br>
			リツイート：<?php if($account["retweet_interval"] > 0): ?><?php number_format(val($account["retweet_interval"] - $account["retweet_flactuation"])) ?>分〜
			<?php number_format(val($account["retweet_interval"] + $account["retweet_flactuation"])) ?>分毎に１回<?php else: ?>投稿しない<?php endif; ?><br>
			<?php if($account["tweet_suspend_start"] != $account["tweet_suspend_end"]): ?>
			ただし、<?php val($account["tweet_suspend_start"]); ?>時〜<?php val($account["tweet_suspend_end"]); ?>時は投稿しない。
			<?php endif; ?>
		</td>
		<td>
			<?php if($account["post_order"] == "1") val("RT数順"); ?>
			<?php if($account["post_order"] == "2") val("ランダム"); ?>
		</td>
		<td>
			<?php val($account["affiliate_interval"]); ?>ツイート毎に通常の投稿に代わって<br>
			アフィリエイト投稿を行う。
		</td>
		<td><div class="btn-group">
			<a class="btn" href="tweets.php?account_id=<?php val($account["account_id"]); ?>">予約済一覧</a>
			<a class="btn" href="posted_tweets.php?account_id=<?php val($account["account_id"]); ?>">投稿済一覧</a>
			<a class="btn" href="affiliates.php?account_id=<?php val($account["account_id"]); ?>">アフィリエイト一覧</a>
			<a class="btn" href="accounts.php?delete=1&account_id=<?php val($account["account_id"]); ?>" onclick="return confirm('削除します。よろしいですか？')">削除</a>
		</div></td>
	</tr>
	</form>
	<?php endforeach; ?>
	</table>
</div>
</div>
</div>
</body>
</html>
