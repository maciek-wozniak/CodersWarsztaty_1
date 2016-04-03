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

foreach ($arrayMyFriends as $friend) {
    $friendsTweets = $friend->getAllMyTweets($conn);
        foreach($friendsTweets as $tweet) {
            echo $tweet->showTweet($conn).'<br>';
        }
}