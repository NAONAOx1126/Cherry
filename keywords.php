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

// キーワードを登録
registerKeyword();

// キーワードを削除
deleteKeyword();

// キーワードを取得
$keywords = getKeywords();
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
		<th class="blue header">キーワード</th>
		<th class="blue header">更新</th>
		<th class="blue header">削除</th>
	</tr>
	<?php foreach($keywords as $keyword): ?>
	<form action="keywords.php" method="POST">
	<input type="hidden" name="keyword_id" value="<?php val($keyword["keyword_id"]); ?>" />
	<tr>
		<td><input type="text" class="input-xxlarge" name="keyword" value="<?php val($keyword["keyword"]); ?>" /></td>
		<td>
			<input type="submit" class="btn" name="register" value="更新" />
		</td>
		<td><a class="btn" href="keywords.php?delete=1&keyword_id=<?php val($keyword["keyword_id"]); ?>" onclick="return confirm('削除します。よろしいですか？')">削除</a></td>
	</tr>
	</form>
	<?php endforeach; ?>
	<form action="keywords.php" method="POST">
	<input type="hidden" name="keyword_id" value="" />
	<tr>
		<td><input type="text" class="input-xxlarge" name="keyword" value="" /></td>
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
