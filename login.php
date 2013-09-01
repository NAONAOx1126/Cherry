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
 * ログイン画面の表示を行います。
 */
require_once(dirname(__FILE__)."/require.php");
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
	<form action="<?php echo APP_SUBDIR; ?>/index.php" method="POST">
		<?php foreach($_SERVER["ERRORS"] as $error): ?>
		<div class="error"><?php echo $error; ?></div>
		<?php endforeach; ?>
		<label for="login_id">ログインID</label>
		<input type="text" id="login_id" name="login_id" value="" />
		<label for="password">パスワード</label>
		<input type="password" id="password" name="password" value="" />
		<div><input type="submit" id="login" name="login" class="btn" value="ログイン" /></div>
	</form>
</div>
</div>
</div>
</body>
</html>
