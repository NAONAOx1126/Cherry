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

// キーワード一覧を取得
$keywords = getKeywords();

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
		<th class="blue header">グループ名／RT数／件数制限</th>
		<th class="blue header">キーワード</th>
		<th class="blue header">NGワード(スペース区切り)</th>
		<th class="blue header">自動取得</th>
		<th class="blue header">更新／削除</th>
	</tr>
	<?php foreach($accountGroups as $accountGroup): ?>
	<form action="account_groups.php" method="POST">
	<input type="hidden" name="account_group_id" value="<?php val($accountGroup["account_group_id"]); ?>" />
	<input type="hidden" name="post_interval" value="0" />
	<tr>
		<td>
		    <input type="text" class="input-medium" name="account_group_name" value="<?php val($accountGroup["account_group_name"]); ?>" /><br />
			<input type="text" class="input-mini" name="pickup_limit" value="<?php val($accountGroup["pickup_limit"]); ?>" />RT<br />
			<input type="text" class="input-mini" name="pickup_count" value="<?php val($accountGroup["pickup_count"]); ?>" />件
		  </td>
		<td>
			<?php for($i = 1; $i < 9; $i ++): ?>
			<select name="keyword_id<?php val($i); ?>">
			<option value="">------</option>
			<?php foreach($keywords as $keyword): ?>
			<option value="<?php val($keyword["keyword_id"]); ?>"<?php if($keyword["keyword_id"] == $accountGroup["keyword_id".$i]) val(" selected"); ?>><?php val($keyword["keyword"]); ?></option>
			<?php endforeach; ?>
			</select><?php if($i % 2 == 0) val("<br />"); ?>
			<?php endfor; ?>
		<td>
		  <textarea name="keyword" cols="40" rows="5"><?php echo $accountGroup["keyword"]; ?></textarea>
		</td>
		<td>
			<input type="radio" name="import_flg" value="1"<?php if($accountGroup["import_flg"] == "1") val(" checked"); ?> />する<br />
			<input type="radio" name="import_flg" value="2"<?php if($accountGroup["import_flg"] == "2") val(" checked"); ?> />しない
		</td>
		<td><div class="btn-group">
			<input type="submit" class="btn" name="register" value="更新" />
			<a class="btn" href="account_groups.php?delete=1&account_group_id=<?php val($accountGroup["account_group_id"]); ?>" onclick="return confirm('削除します。よろしいですか？')">削除</a>
		</div></td>
	</tr>
	</form>
	<?php endforeach; ?>
	<form action="account_groups.php" method="POST">
	<input type="hidden" name="account_group_id" value="" />
	<input type="hidden" name="post_interval" value="0" />
	<tr>
		<td><input type="text" class="input-medium" name="account_group_name" value="" /></td>
		<td>
			<?php for($i = 1; $i < 9; $i ++): ?>
			<select name="keyword_id<?php val($i); ?>">
			<option value="">------</option>
			<?php foreach($keywords as $keyword): ?>
			<option value="<?php val($keyword["keyword_id"]); ?>"><?php val($keyword["keyword"]); ?></option>
			<?php endforeach; ?>
			</select><?php if($i % 2 ==0) val("<br />"); ?>
			<?php endfor; ?>
		</td>
		<td>
			<input type="text" class="input-mini" name="pickup_limit" value="100" />RT<br />
			<input type="text" class="input-mini" name="pickup_count" value="0" />件
		</td>
		<td>
			<input type="radio" name="import_flg" value="1" checked />する<br />
			<input type="radio" name="import_flg" value="2" />しない&nbsp;
		</td>
		<td>
			<input type="submit" class="btn" name="register" value="登録" />
		</td>
	</tr>
	</form>
	</table>
</div>
</div>
</div>
</body>
</html>
