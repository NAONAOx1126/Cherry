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
 * アカウントグループ管理画面の表示を行います。
 */
require_once(dirname(__FILE__)."/require.php");

// ログインチェックを行う。
checkLoginAdministrator();

// アカウントグループを登録
registerAccountGroup();

// アカウントグループを削除
deleteAccountGroup();

// アカウントグループを取得
$accountGroups = getAccountGroups();
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
	<table class="table table-bordered table-striped" summary="一覧">
	<tr>
		<th class="blue header">ID</th>
		<th class="blue header">グループ名</th>
		<th class="blue header">キーワード</th>
		<th class="blue header">RT数制限</th>
		<th class="blue header">投稿間隔(デフォルト)</th>
		<th class="blue header">更新</th>
		<th class="blue header">削除</th>
	</tr>
	<?php foreach($accountGroups as $accountGroup): ?>
	<form action="account_groups.php" method="POST">
	<input type="hidden" name="account_group_id" value="<?php echo $accountGroup["account_group_id"]; ?>" />
	<tr>
		<td><?php echo $accountGroup["account_group_id"]; ?></td>
		<td><input type="text" class="input-large" name="account_group_name" value="<?php echo $accountGroup["account_group_name"]; ?>" /></td>
		<td><input type="text" class="input-large" name="keyword" value="<?php echo $accountGroup["keyword"]; ?>" /></td>
		<td><input type="text" class="input-small" name="pickup_limit" value="<?php echo $accountGroup["pickup_limit"]; ?>" /></td>
		<td>
			<input type="radio" name="post_interval" value="0"<?php if($accountGroup["post_interval"] == "") echo " checked"; ?> />更新停止&nbsp;
			<input type="radio" name="post_interval" value="30"<?php if($accountGroup["post_interval"] == 30) echo " checked"; ?> />30分毎&nbsp;
			<input type="radio" name="post_interval" value="60"<?php if($accountGroup["post_interval"] == 60) echo " checked"; ?> />1時間毎&nbsp;
			<input type="radio" name="post_interval" value="120"<?php if($accountGroup["post_interval"] == 120) echo " checked"; ?> />2時間毎<br />
			<input type="radio" name="post_interval" value="180"<?php if($accountGroup["post_interval"] == 180) echo " checked"; ?> />3時間毎&nbsp;
			<input type="radio" name="post_interval" value="240"<?php if($accountGroup["post_interval"] == 240) echo " checked"; ?> />4時間毎&nbsp;
			<input type="radio" name="post_interval" value="300"<?php if($accountGroup["post_interval"] == 300) echo " checked"; ?> />5時間毎&nbsp;
			<input type="radio" name="post_interval" value="360"<?php if($accountGroup["post_interval"] == 360) echo " checked"; ?> />6時間毎&nbsp;
		</td>
		<td>
			<input type="submit" class="btn" name="register" value="更新" />
		</td>
		<td><a class="btn" href="account_groups.php?delete=1&account_group_id=<?php echo $accountGroup["account_group_id"]; ?>" onclick="return confirm('削除します。よろしいですか？')">削除</a></td>
	</tr>
	</form>
	<?php endforeach; ?>
	<form action="account_groups.php" method="POST">
	<input type="hidden" name="account_group_id" value="" />
	<tr>
		<td></td>
		<td><input type="text" class="input-large" name="account_group_name" value="" /></td>
		<td><input type="text" class="input-large" name="keyword" value="" /></td>
		<td><input type="text" class="input-small" name="pickup_limit" value="100" /></td>
		<td>
			<input type="radio" name="post_interval" value="0" checked />更新停止&nbsp;
			<input type="radio" name="post_interval" value="30" />30分毎&nbsp;
			<input type="radio" name="post_interval" value="60" />1時間毎&nbsp;
			<input type="radio" name="post_interval" value="120" />2時間毎<br />
			<input type="radio" name="post_interval" value="180" />3時間毎&nbsp;
			<input type="radio" name="post_interval" value="240" />4時間毎&nbsp;
			<input type="radio" name="post_interval" value="300" />5時間毎&nbsp;
			<input type="radio" name="post_interval" value="360" />6時間毎&nbsp;
		</td>
		<td>
			<input type="submit" class="btn" name="register" value="登録" />
		</td>
		<td></td>
	</tr>
	</form>
	</table>
</div>
</div>
</div>
</body>
</html>
