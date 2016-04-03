<?php

require_once dirname(__FILE__).'/../Classes/allClasses.php';

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}
else {
    header('Location: ../');
}

$arrayMyFriends = $user->getAllFriends($conn);
$allFriendsTweets = [];

foreach ($arrayMyFriends as $friend) {
    $friendsTweets = $friend->getAllMyTweets($conn);
        foreach($friendsTweets as $tweet) {
            $allFriendsTweets[] = $tweet;
         }
}

usort($allFriendsTweets, array('Tweet', 'messageDateComparision'));

foreach ($allFriendsTweets as $tweet) {
    echo $tweet->showTweet($conn).'<br>';
}