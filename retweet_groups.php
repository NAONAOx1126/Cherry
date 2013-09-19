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
registerRetweetGroup();

// アカウントグループを削除
deleteRetweetGroup();

// メンバーの追加
addRetweetGroupMember();

// メンバーの削除
removeRetweetGroupMember();

// アカウントグループを取得
$retweetGroups = getRetweetGroups();
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
	<table class="table table-bordered table-striped" summary="一覧">
	<tr>
		<th class="blue header">グループ名</th>
		<th class="blue header">メンバー</th>
		<th class="blue header">更新／削除</th>
	</tr>
	<?php foreach($retweetGroups as $retweetGroup): ?>
	<form action="retweet_groups.php" method="POST">
	<input type="hidden" name="retweet_group_id" value="<?php val($retweetGroup["retweet_group_id"]); ?>" />
	<tr>
		<td><input type="text" class="input-medium" name="retweet_group_name" value="<?php val($retweetGroup["retweet_group_name"]); ?>" /></td>
		<td>
			<?php $members = getRetweetGroupMember($retweetGroup["retweet_group_id"]); ?>
			<?php if(is_array($members) && count($members) > 0): ?>
			<?php foreach($members as $member): ?>
			<div><?php val($member["screen_name"]); ?>&nbsp;<a href="retweet_groups.php?remove_member=1&retweet_group_id=<?php val($retweetGroup["retweet_group_id"]); ?>&screen_name=<?php val($member["screen_name"]); ?>">削除</a></div>
			<?php endforeach; ?>
			<?php endif; ?>
			<div><input type="text" class="input-medium" name="screen_name" /><input type="submit" class="btn" name="add_member" value="追加" /></div>
			</td>
		<td><div class="btn-group">
			<input type="submit" class="btn" name="register" value="更新" />
			<a class="btn" href="retweet_groups.php?delete=1&retweet_group_id=<?php val($retweetGroup["retweet_group_id"]); ?>" onclick="return confirm('削除します。よろしいですか？')">削除</a>
		</div></td>
	</tr>
	</form>
	<?php endforeach; ?>
	<form action="retweet_groups.php" method="POST">
	<input type="hidden" name="account_group_id" value="" />
	<tr>
		<td><input type="text" class="input-medium" name="retweet_group_name" value="" /></td>
		<td></td>
		<td><div class="btn-group">
			<input type="submit" class="btn" name="register" value="グループ新規登録" />
		</div></td>
		</tr>
	</form>
	</table>
</div>
</div>
</div>
</body>
</html>
