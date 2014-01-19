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
$_SERVER["REQUEST_URI"] = "/batch/AutoFollow.php";

require_once(dirname(__FILE__)."/../require.php");

$connection = new Connection();

// フォロー実行対象アカウントを取得
$sql = "SELECT accounts.* FROM accounts";
$result = $connection->query($sql);
$accounts = $result->fetchAll();
$result->close();

if(is_array($accounts)){
	foreach($accounts as $account){
	    echo "Auto following in ".$account["screen_name"]."\r\n";
        $sql = "SELECT * FROM administrators WHERE administrator_id = '".$account["administrator_id"]."'";
        $result = $connection->query($sql);
        $administrator = $result->fetch();
        $result->close();

		$twitter = getTwitter($account["account_id"]);

        // 自分の情報を取得する。
	    $me = $twitter->users_show(array("user_id" => $account["user_id"]));

		// 次のフォローを取得
	    $sql = "SELECT * FROM follower_caches WHERE account_id = '".$account["account_id"]."' ORDER BY follow_id";
	    $result = $connection->query($sql);
	    $follow = $result->fetch();
	    $result->close();

	    if(!empty($follow)){
	        echo "Auto following to followers by ".$follow["user_id"]."\r\n";

	        if($me->followers_count < 50){
	            $max_follows = floor($administrator["max_follows_50"] * $me->followers_count / 100);
	            $daily_follows = $administrator["daily_follows_50"];
	            $daily_unfollows = $administrator["daily_unfollows_50"];
	        }elseif($me->followers_count < 100){
	            $max_follows = floor($administrator["max_follows_100"] * $me->followers_count / 100);
	            $daily_follows = $administrator["daily_follows_100"];
	            $daily_unfollows = $administrator["daily_unfollows_100"];
	        }elseif($me->followers_count < 200){
	            $max_follows = floor($administrator["max_follows_200"] * $me->followers_count / 100);
	            $daily_follows = $administrator["daily_follows_200"];
	            $daily_unfollows = $administrator["daily_unfollows_200"];
	        }elseif($me->followers_count < 400){
	            $max_follows = floor($administrator["max_follows_400"] * $me->followers_count / 100);
	            $daily_follows = $administrator["daily_follows_400"];
	            $daily_unfollows = $administrator["daily_unfollows_400"];
	        }elseif($me->followers_count < 800){
	            $max_follows = floor($administrator["max_follows_800"] * $me->followers_count / 100);
	            $daily_follows = $administrator["daily_follows_800"];
	            $daily_unfollows = $administrator["daily_unfollows_800"];
	        }elseif($me->followers_count < 1200){
	            $max_follows = floor($administrator["max_follows_1200"] * $me->followers_count / 100);
	            $daily_follows = $administrator["daily_follows_1200"];
	            $daily_unfollows = $administrator["daily_unfollows_1200"];
	        }elseif($me->followers_count < 1600){
	            $max_follows = floor($administrator["max_follows_1600"] * $me->followers_count / 100);
	            $daily_follows = $administrator["daily_follows_1600"];
	            $daily_unfollows = $administrator["daily_unfollows_1600"];
	        }elseif($me->followers_count < 2000){
	            $max_follows = floor($administrator["max_follows_2000"] * $me->followers_count / 100);
	            $daily_follows = $administrator["daily_follows_2000"];
	            $daily_unfollows = $administrator["daily_unfollows_2000"];
	        }else{
	            $max_follows = floor($administrator["max_follows_over_2000"] * $me->followers_count / 100);
	            $daily_follows = $administrator["daily_follows_over_2000"];
	            $daily_unfollows = $administrator["daily_unfollows_over_2000"];
	        }

	        echo "Max Follows : ".$max_follows."\r\n";
	        echo "Daily Follows : ".$daily_follows."\r\n";
	        echo "Daily Unfollows : ".$daily_unfollows."\r\n";

	        if($me->friends_count < floor($max_follows * 0.9) || ($me->friends_count < $max_follows * 0.9 && strtotime($account["next_follow_time"]) < strtotime($account["next_unfollow_time"]))){
	            echo "Starting follow action if after ".$account["next_follow_time"]."\r\n";
	            if($daily_follows > 0 && strtotime($account["next_follow_time"]) < time()){
	                // 取得したフォローのフォロワーを取得する。
	                $result = (array) $twitter->followers_ids(array("user_id" => $follow["user_id"], "count" => "1000"));
	                $followers = $result["ids"];
	                echo "followers = ".count($followers)."\r\n";
	                $follow_count = 0;
	                for($i = 0; $i < 5; $i ++){
	                    $index = mt_rand(0, count($followers));
	                    if($index < count($followers)){
	                        $user_id = $followers[$index];
	                        $user = $twitter->users_show(array("user_id" => $user_id));
	                        if(!($user->following > 0)){
	                            if($administrator["ignore_non_japanese_flg"] == "1" && !checkJapaneseText($user->description)){
    	                            continue;
    	                        }
    	                        if($administrator["ignore_bot_flg"] == "1" && preg_match("/bot/i", $user->description) > 0){
    	                            continue;
    	                        }
    	                        if($administrator["ignore_url_flg"] == "1" && preg_match("/http:\\/\\//i", $user->description) > 0){
    	                            continue;
    	                        }

    	                        $twitter->friendships_create(array("user_id" => $user_id, "follow" => true));
    	                        echo "follow to ".$user_id."\r\n";
    	                        $follow_count ++;
    	                        sleep(mt_rand(10, 20));
	                        }

	                        if($follow["depth"] < $administrator["tree_depth"]){
	                            $connection->query("INSERT IGNORE INTO follower_caches(account_id, user_id, depth) VALUES ('".$follow["account_id"]."', '".$user_id."', '".($follow["depth"] + 1)."')");
	                        }
	                    }
	                }
	                $connection->query("DELETE FROM follower_caches WHERE account_id = '".$follow["account_id"]."' AND user_id = '".$follow["user_id"]."'");

	                // 次のスケジュールを組む
	                if(time() - strtotime($account["scheduled_follow_time"]) > 24 * 3600){
	                    $scheduledTime = time();
	                }else{
	                    $scheduledTime = strtotime($account["scheduled_follow_time"]);
	                }
	                $interval = floor(24 * 3600 * $follow_count / $daily_follows);
	                $account["scheduled_follow_time"] = date("Y-m-d H:i:s", $scheduledTime + $interval);
	                $account["next_follow_time"] = date("Y-m-d H:i:s", time() + floor(mt_rand(0, $scheduledTime + $interval - time())));
	                $account["next_unfollow_time"] = $account["scheduled_unfollow_time"] = date("Y-m-d H:i:s", $scheduledTime + 24 * 3600);
                    $sql = "UPDATE accounts SET scheduled_follow_time = '".$account["scheduled_follow_time"]."'";
                    $sql .= ", next_follow_time = '".$account["next_follow_time"]."'";
                    $sql .= ", scheduled_unfollow_time = '".$account["scheduled_unfollow_time"]."'";
                    $sql .= ", next_unfollow_time = '".$account["next_unfollow_time"]."'";
                    $sql .= " WHERE account_id = '".$account["account_id"]."'";
                    $result = $connection->query($sql);
	            }
	        }else{
	            echo "Starting unfollow action if after ".$account["next_unfollow_time"]."\r\n";
	            if($daily_unfollows > 0 && strtotime($account["next_unfollow_time"]) < time()){
	                print_r($twitter->friendships_incoming());
	                print_r($twitter->friendships_outgoing());

	                // 次のスケジュールを組む
	                if(time() - strtotime($account["scheduled_unfollow_time"]) > 24 * 3600){
	                    $scheduledTime = time();
	                }else{
	                    $scheduledTime = strtotime($account["scheduled_unfollow_time"]);
	                }
	                $interval = floor(24 * 3600 * 5 / $daily_unfollows);
	                $account["scheduled_unfollow_time"] = date("Y-m-d H:i:s", $scheduledTime + $interval);
	                $account["next_unfollow_time"] = date("Y-m-d H:i:s", time() + floor(mt_rand(0, $scheduledTime + $interval - time())));
	                $account["next_follow_time"] = $account["scheduled_follow_time"] = date("Y-m-d H:i:s", $scheduledTime + 24 * 3600);
                    $sql = "UPDATE accounts SET scheduled_follow_time = '".$account["scheduled_follow_time"]."'";
                    $sql .= ", next_follow_time = '".$account["next_follow_time"]."'";
                    $sql .= ", scheduled_unfollow_time = '".$account["scheduled_unfollow_time"]."'";
                    $sql .= ", next_unfollow_time = '".$account["next_unfollow_time"]."'";
                    $sql .= " WHERE account_id = '".$account["account_id"]."'";
                    $result = $connection->query($sql);
	            }
	        }
	    }
	}
}
