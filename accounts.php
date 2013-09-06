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
	<?php if(is_array($accountGroups) && count($accountGroups) > 0): ?>
	<div>
		<form action="accounts.php" method="POST">
		<select name="account_group_id">
		<?php foreach($accountGroups as $accountGroup): ?>
		<option value="<?php echo $accountGroup["account_group_id"]; ?>"><?php echo $accountGroup["account_group_name"]; ?></option>
		<?php endforeach; ?>
		</select>
		<input type="submit" class="btn" name="add_account" value="新規アカウント追加" />
		</form>
	</div>
	<?php endif; ?>
	<br />
	<table class="table table-bordered table-striped" summary="一覧">
	<tr>
		<th class="blue header">TwitterユーザーID</th>
		<th class="blue header">Twitterユーザー名</th>
		<th class="blue header">投稿間隔</th>
		<th class="blue header">投稿順序</th>
		<th class="blue header">予約済一覧</th>
		<th class="blue header">投稿済一覧</th>
		<th class="blue header">更新</th>
		<th class="blue header">削除</th>
	</tr>
	<?php foreach($accounts as $account): ?>
	<form action="accounts.php" method="POST">
	<input type="hidden" name="account_id" value="<?php echo $account["account_id"]; ?>" />
	<tr>
		<td><?php echo $account["user_id"]; ?></td>
		<td><?php echo $account["screen_name"]; ?></td>
		<td>
			<input type="radio" name="post_interval" value="0"<?php if($account["post_interval"] == "0") echo " checked"; ?> />更新停止&nbsp;
			<input type="radio" name="post_interval" value="30"<?php if($account["post_interval"] == 30) echo " checked"; ?> />30分毎&nbsp;
			<input type="radio" name="post_interval" value="60"<?php if($account["post_interval"] == 60) echo " checked"; ?> />1時間毎&nbsp;
			<input type="radio" name="post_interval" value="120"<?php if($account["post_interval"] == 120) echo " checked"; ?> />2時間毎<br />
			<input type="radio" name="post_interval" value="180"<?php if($account["post_interval"] == 180) echo " checked"; ?> />3時間毎&nbsp;
			<input type="radio" name="post_interval" value="240"<?php if($account["post_interval"] == 240) echo " checked"; ?> />4時間毎&nbsp;
			<input type="radio" name="post_interval" value="300"<?php if($account["post_interval"] == 300) echo " checked"; ?> />5時間毎&nbsp;
			<input type="radio" name="post_interval" value="360"<?php if($account["post_interval"] == 360) echo " checked"; ?> />6時間毎&nbsp;
		</td>
		<td>
			<input type="radio" name="post_order" value="1"<?php if($account["post_order"] == "1") echo " checked"; ?> />RT数順&nbsp;
			<input type="radio" name="post_order" value="2"<?php if($account["post_order"] == "2") echo " checked"; ?> />ランダム&nbsp;
		</td>
		<td><a class="btn" href="tweets.php?account_id=<?php echo $account["account_id"]; ?>">予約済一覧</a></td>
		<td><a class="btn" href="posted_tweets.php?account_id=<?php echo $account["account_id"]; ?>">投稿済一覧</a></td>
		<td><input type="submit" class="btn" name="update" value="更新" /></td>
		<td><a class="btn" href="accounts.php?delete=1&account_id=<?php echo $account["account_id"]; ?>" onclick="return confirm('削除します。よろしいですか？')">削除</a></td>
	</tr>
	</form>
	<?php endforeach; ?>
	</table>
</div>
</div>
</div>
</body>
</html>
