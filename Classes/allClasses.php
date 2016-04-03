<?php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', 'http://' . $_SERVER['HTTP_HOST'] . '/CodersWarsztaty_1');
}

require_once 'DbConnection.php';
require_once 'User.php';
require_once 'Message.php';
require_once 'Tweet.php';
require_once 'TweetComment.php';