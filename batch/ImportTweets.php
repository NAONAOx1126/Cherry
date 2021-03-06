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
$_SERVER["DOCUMENT_ROOT"] = $argv[2];
$_SERVER["REQUEST_URI"] = "/batch/ImportTweets.php";

require_once(dirname(__FILE__)."/../require.php");

$connection = new Connection();
$sql = "SELECT keywords.*, accounts.account_id, account_groups.keyword AS ngword";
$sql .= " FROM keywords, account_groups, accounts";
$sql .= " WHERE (keywords.keyword_id = account_groups.keyword_id1";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id2";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id3";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id4";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id5";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id6";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id7";
$sql .= " OR keywords.keyword_id = account_groups.keyword_id8)";
$sql .= " AND account_groups.account_group_id = accounts.account_group_id";
$sql .= " AND keywords.delete_flg = 0";
$result = $connection->query($sql);
$keywords = $result->fetchAll();
$result->close();
if(is_array($keywords)){
	foreach($keywords as $keyword){
		// そのアカウントのmax_idを取得します。
		$sql = "SELECT * FROM tweet_search_cache WHERE account_id = '".$connection->escape($keyword["keyword_id"])."'";
		$result = $connection->query($sql);
    	$max_id = "";
		if(!empty($result)){
    		$search_caches = $result->fetchAll();
    		$result->close();
    		if(is_array($search_caches) && count($search_caches) > 0){
    			$max_id = $search_caches[0]["max_id"];
    		}
		}
		
		// アカウントグループのキーワードで検索します。
		$tweets = array();
		$twitter = getTwitter($keyword["account_id"]);
		$ngwords = explode(" ", str_replace("　", " ", $keyword["ngword"]));
		$keyw = str_replace("　", " ", $keyword["keyword"]);
		if(is_array($ngwords)){
		    foreach($ngwords as $ng){
		        $keyw .= " -".$ng;
		    }
		}
		$condition = array("q" => $keyw, "lang" => "ja", "result_type" => "recent", "count" => 100);
		if(!empty($max_id)) $condition["max_id"] = $max_id;
		$result = $twitter->search_tweets($condition);
		echo "tweets is ".count($result->statuses)."\r\n";
		foreach($result->statuses as $tweet){
	        if(is_array($ngwords) && count($ngwords) > 0 && preg_match("/(".implode("|", $ngwords).")/u", $tweet->text) > 0) continue;
			if(isset($tweet->retweeted_status) && !empty($tweet->retweeted_status)) $tweet = $tweet->retweeted_status;
			if($tweet->retweet_count > 20 && empty($tweet->entities->urls)){
				$tweets[$tweet->id] = $tweet;
			}
		}
		
		echo "next result : ".$result->search_metadata->next_results."\r\n";
		if(isset($result->search_metadata->next_results)){
			if(preg_match("/max_id=([0-9]+)/", $result->search_metadata->next_results, $params) > 0){
				$sql = "REPLACE INTO tweet_search_cache (keyword_id, max_id) VALUES ('".$connection->escape($keyword["keyword_id"])."', '".$connection->escape($params[1])."')";
        		echo $sql."\r\n";
        		$result = $connection->query($sql);
			}
		}else{
			$sql = "DELETE FROM tweet_search_cache WHERE keyword_id = '".$connection->escape($keyword["keyword_id"])."'";
    		echo $sql."\r\n";
    		$result = $connection->query($sql);
		}
		
		foreach($tweets as $tweet){
			// Tweetの取得元がシステム上のものは除外
			$sql = "SELECT tweets.* FROM tweets WHERE post_id = '".$connection->escape($tweet->id)."'";
			$result = $connection->query($sql);
			$relatedTweets = $result->fetchAll();
			if(is_array($relatedTweets) && count($relatedTweets)) continue;
			
			// URLを含むものは対象外
			if(!empty($tweet->entities->urls)) continue;
			
			// 日本語を含まないツイートは除外
			if(!checkJapaneseText($tweet->text)) continue;
							
			// 画像がある場合は元画像をダウンロードする。
			if(isset($tweet->entities->media) && is_array($tweet->entities->media)){
				foreach($tweet->entities->media as $index => $media){
					$imageFilename = "/images/".$tweet->id."-".($index + 1);
					if(($fp = fopen(APP_ROOT.$imageFilename, "w+")) !== FALSE){
						fwrite($fp, file_get_contents($media->media_url));
						fclose($fp);
						@chmod(APP_ROOT.$imageFilename, 0666);
						// 画像を取得し、テキスト内のURLを削除
						$tweet->text = str_replace($media->url, "", $tweet->text);
					}
				}
			}
			
			$sqlval = array();
			$sqlval["keyword_id"] = $keyword["keyword_id"];
			$sqlval["post_id"] = $tweet->id;
			$sqlval["tweet_text"] = $tweet->text;
			$sqlval["favorite_count"] = $tweet->favorite_count;
			$sqlval["retweet_count"] = $tweet->retweet_count;
			
			// 同じ内容かつIDが別のツイートを取得
			$tweet_id = "";
			$sql = "SELECT * FROM tweet_caches WHERE keyword_id = '".$connection->escape($keyword["keyword_id"])."'";
			$sql .= " AND tweet_text = '".$connection->escape($tweet->text)."'";
			$sql .= " AND post_id != '".$connection->escape($tweet->id)."'";
			$result = $connection->query($sql);
			$sameTweets = $result->fetchAll();
			$result->close();
			if(isset($sameTweets) && count($sameTweets) > 0){
				foreach($sameTweets as $sameTweet){
					if($sameTweet["retweet_count"] < $sqlval["retweet_count"]){
						$tweet_id = $sameTweet["tweet_id"];
					}
				}
			}
			
			if(empty($tweet_id)){
				// 既に登録済みか調べる。(IDか内容が一致するものは除外する。)
				$sql = "SELECT * FROM tweet_caches WHERE keyword_id = '".$connection->escape($keyword["keyword_id"])."' AND post_id = '".$connection->escape($tweet->id)."'";
				$result = $connection->query($sql);
				$registeredTweets = $result->fetchAll();
				$result->close();
				if(is_array($registeredTweets) && count($registeredTweets) > 0){
					$tweet_id = $registeredTweets[0]["tweet_id"];
				}
			}

			if(!empty($tweet_id)){
				$sql = "SELECT * FROM tweet_caches WHERE tweet_id = '".$connection->escape($tweet_id)."'";
				$result = $connection->query($sql);
				$registeredTweets = $result->fetchAll();
				$result->close();
				if(is_array($registeredTweets) && count($registeredTweets) > 0){
					if($registeredTweets[0]["tweet_text"] == $sqlval["tweet_text"] && $registeredTweets[0]["retweet_count"] < $sqlval["retweet_count"]){
						foreach($sqlval as $key => $value){
							$sqlval[$key] = $key." = '".$connection->escape($value)."'";
						}
						$sql = "UPDATE tweet_caches SET ".implode(", ", $sqlval);
						$sql .= " WHERE tweet_id = '".$connection->escape($tweet_id)."'";
						echo $sql."\r\n";
						$result = $connection->query($sql);
					}
				}
			}else{
				foreach($sqlval as $key => $value){
					$sqlval[$key] = $connection->escape($value);
				}
				$sql = "INSERT INTO tweet_caches";
				$sql .= "(".implode(", ", array_keys($sqlval)).") VALUES ('".implode("', '", $sqlval)."')";
				echo $sql."\r\n";
				$result = $connection->query($sql);
			}
		}
	}
}
