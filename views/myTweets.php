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

$arrayMyTweets = $user->getAllMyTweets($conn);

foreach ($arrayMyTweets as $tweet) {
    echo $tweet->showTweet($conn).'<br>';
}