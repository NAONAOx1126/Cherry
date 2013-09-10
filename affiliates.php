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

// アフィリエイトを追加
registerAffiliate();

// アフィリエイトを追加
updateAffiliate();

// アフィリエイトを削除
deleteAffiliate();

// アフィリエイトを取得
$affiliates = getAffiliates($_POST["account_id"]);
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
	<tr><td colspan="8">
		カスタム投稿を追加
	</td></tr>
	<form action="affiliates.php" method="POST">
	<input type="hidden" name="account_id" value="<?php val($_POST["account_id"]); ?>" />
	<tr><td colspan="8">
		<textarea name="tweet_text" class="span8" rows="5"></textarea><br />
		<input type="text" name="frequency" class="span2" value="1" />
		<input type="submit" class="btn" name="register" value="アフィリエイトを追加" />
	</td></tr>
	</form>
	<form action="affiliates.php" method="POST">
	<input type="hidden" name="account_id" value="<?php val($_POST["account_id"]); ?>" />
	<tr><td colspan="8">
	<input type="submit" class="btn" name="delete" value="チェックしたアフィリエイトを削除" onclick="return confirm('削除します。よろしいですか？');" />
	<input type="submit" class="btn" name="update" value="アフィリエイトを更新" />
	</td></tr>
	<tr>
		<th class="blue header"></th>
		<th class="blue header span8">テキスト</th>
		<th class="blue header">頻度</th>
		<th class="blue header">削除</th>
	</tr>
	<?php foreach($affiliates as $affiliate): ?>
	<tr>
		<td><input type="checkbox" name="affiliate_ids[]" value="<?php val($affiliate["affiliate_id"]); ?>" /></td>
		<td>
		<textarea name="tweet_text[<?php val($affiliate["affiliate_id"]); ?>]" class="span8" rows="5"><?php val($affiliate["tweet_text"]); ?></textarea>
		</td>
		<td><input type="text" name="frequency[<?php val($affiliate["affiliate_id"]); ?>]" value="<?php val($affiliate["frequency"]); ?>" /></td>
		<td><a class="btn" href="affiliates.php?delete=1&account_id=<?php val($affiliate["account_id"]); ?>&affiliate_id=<?php val($affiliate["affiliate_id"]); ?>" onclick="return confirm('削除します。よろしいですか？')">削除</a></td>
	</tr>
	<?php endforeach; ?>
	<tr><td colspan="8">
	<input type="submit" class="btn" name="delete" value="チェックしたアフィリエイトを削除" onclick="return confirm('削除します。よろしいですか？');" />
	<input type="submit" class="btn" name="update" value="アフィリエイトを更新" />
	</td></tr>
	</form>
	</table>
</div>
</div>
</div>
</body>
</html>
