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
 * ツイートの取り込み処理を行います。
 */
$_SERVER["HTTPS"] = "";
$_SERVER["SERVER_NAME"] = $argv[1];
$_SERVER["REQUEST_URI"] = "/batch/CrateThumbnail.php";

require_once(dirname(__FILE__)."/../require.php");

// 画像ファイルを取得
if ($dir = opendir(APP_ROOT."/images/")) {
	while (($file = readdir($dir)) !== false) {
		if (!file_exists(APP_ROOT."/thumbnails/".$file)) {
			// 存在しない場合はサムネイルを作成
			$info = getimagesize(APP_ROOT."/images/".$file);
			// 変換後のサイズを計算
			$tinfo = $info;
			if($info[0]  > 150){
				$tinfo[1] = ceil($info[1] * 150 / $info[0]);
				$tinfo[0] = 150;
			}
			if($tinfo[1] > 250){
				$tinfo[0] = ceil($tinfo[0] * 250 / $info[1]);
				$tinfo[1] = 250;
			}
			
			if($tinfo[0] != $info[0] || $tinfo[1] != $info[1]){
				// 変換後のサイズが変わっている場合はリサイズ
				$image = FALSE;
				switch($info[2]){
					case IMAGETYPE_GIF:
						$image = imagecreatefromgif(APP_ROOT."/images/".$file);
						break;
					case IMAGETYPE_JPEG:
					case IMAGETYPE_JPEG2000:
						$image = imagecreatefromjpeg(APP_ROOT."/images/".$file);
						break;
					case IMAGETYPE_PNG:
						$image = imagecreatefrompng(APP_ROOT."/images/".$file);
						break;
				}
				if($image !== FALSE){
					$timage = imagecreatetruecolor($tinfo[0], $tinfo[1]);
					imagecopyresampled($timage, $image, 0, 0, 0, 0, $tinfo[0], $tinfo[1], $info[0], $info[1]);
					imagepng($timage, APP_ROOT."/thumbnails/".$file);
				}
			}else{
				// 画像サイズが同じ場合はコピー
				copy(APP_ROOT."/images/".$file, APP_ROOT."/thumbnails/".$file);
			}
		}
	}
	closedir($dir);
}
