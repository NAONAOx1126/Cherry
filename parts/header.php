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
?>
<div class="navbar navbar-fixed-top">
<div class="navbar-inner">
<div class="container-fluid">
<a class="brand" href="<?php echo APP_SUBDIR; ?>/index.php">CHERRY - Twitter自動投稿ツール</a>
<?php if(is_array($_SESSION["ADMINISTRATOR"]) && $_SESSION["ADMINISTRATOR"]["administrator_id"] > 0): ?>
<div class="nav-collapse">
	<ul class="nav pull-right">
		<li><a href="<?php echo APP_SUBDIR; ?>/setting.php">自動フォロー設定</a></li>
		<li><a href="<?php echo APP_SUBDIR; ?>/keywords.php">キーワード一覧</a></li>
		<li><a href="<?php echo APP_SUBDIR; ?>/account_groups.php">アカウントグループ一覧</a></li>
		<li><a href="<?php echo APP_SUBDIR; ?>/accounts.php">アカウント一覧</a></li>
		<li><a href="<?php echo APP_SUBDIR; ?>/retweet_groups.php">リツイートグループ一覧</a></li>
		</ul>
</div>
<?php endif; ?>
</div>
</div>
</div>
