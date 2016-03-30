<?php


include_once dirname(__FILE__).'/../Classes/Tweet.php';
include_once dirname(__FILE__).'/../Classes/TweetComment.php';

session_start();
if (isset($_SESSION['user']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user = $_SESSION['user'];
    $id = $_GET['id'];
}
else {
    return false;
}

$tweet = new Tweet();
$tweet->loadTweetFromDb($id);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>myTwitter - Tweet info</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

    <div class="container">
<a href="../">Strona główna</a>
        <div class="row">
            <div class="col-sm-8 col-sm-offset-2" style="margin-top: 10px;">

                <? $tweet->showTweet(); ?>
                <? $tweet->getAllComments() ?>
            </div>
        </div>

    </div>

</body>
</html>