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

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tweet = new Tweet();
    if ($tweet->loadTweetFromDb($conn, $_GET['id']) === false ){
        unset($tweet);
    }
}

$buttonName = 'addComment';
$messageType = 'danger';

// Dodawanie komentarza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addComment']) &&
    isset($_POST['commentText']) && strlen($_POST['commentText'])>1 && strlen($_POST['commentText']) <=60) {

    $tweetComment = new TweetComment();
    $tweetComment->setAuthorId($_SESSION['user']->getUserId());
    $tweetComment->setCommentText($_POST['commentText']);
    $tweetComment->setTweetId($tweet->getTweetId());

    if ($tweetComment->createCommentAndAddToDb($conn)) {
        header('Location: tweetComments.php?id='.$tweet->getTweetId());
    }
    else {
        $message = 'Nie udało się dodać komentarza, proszę spróbować jeszcze raz';
    }
}
else if (isset($_POST['addComment']) && (!isset($_POST['commentText']) || !(strlen($_POST['commentText'])>1))) {
    $message = 'Wpisz tekst komentarza';
}
else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addComment']) && strlen($_POST['commentText']) > 60) {
    $message = 'Komentarz jest za długi, max 60 znaków';
}


// Usuwanie komentarza
if (isset($_GET['deleteComment']) && is_numeric($_GET['deleteComment'])) {
    $tweetComment = new TweetComment();
    if ($tweetComment->loadCommentFromDb($conn, $_GET['deleteComment']) === false) {
        unset($tweetComment);
    }

    if ($tweetComment->deleteComment($conn)) {
        header('Location: tweetComments.php?id='.$tweet->getTweetId());
    }
    else {
        $message = 'Nie udało się usunąć komentarza';
    }

}

// Wczytywanie komentarza do edycji
if (isset($_GET['editComment']) && is_numeric($_GET['editComment'])) {
    $editedComment = new TweetComment();

    if ($editedComment->loadCommentFromDb($conn, $_GET['editComment']) === false) {
        unset($editedComment);
    }

    $buttonName = 'editComment';

    // Edytowanie komentarza
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editComment']) && strlen($_POST['commentText']) <= 60) {
        $editedComment->setCommentText($_POST['commentText']);
        if (!$editedComment->updateComment($conn)) {
            $message = 'Nie udało się zmienić treści komentarza';
        }
        else{
                header('Location: tweetComments.php?id='.$tweet->getTweetId());
        }
    }
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editComment']) && strlen($_POST['commentText']) > 60) {
        $message = 'Komentarz za długi - max 60 znaków';
    }
}


if (!isset($tweet)) {
    die('Failed to load tweet');
}

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
        <div class="row">
            <div class="col-sm-12 navbar navbar-default" style="padding-bottom: 20px;"><?php
                include_once dirname(__FILE__).'/userPanel.php'; ?>
            </div>
        </div>

        <?php
        if (isset($message) && isset($messageType)) {
            showMessage($message, $messageType);
        }
        ?>

        <div class="row">
            <div class="col-sm-8 col-sm-offset-2" style="margin-top: 10px;">

                <?  $tweet->showTweet($conn);
                    echo '<br>';
                    $comments = $tweet->getAllComments($conn);
                    foreach ($comments as $comment) {
                        $comment->showComment($conn);
                        echo '<br>';
                    }

                ?>

            </div>
        </div>


        <div class="well" style="width: 500px; margin: 0 auto; margin-top: 20px;">
            <form class="form-horizontal" method="post" action="tweetComments.php?id=<? echo $_GET['id']; if (isset($editedComment)) echo '&editComment='.$editedComment->getCommentId() ?>">

                <div class="form-group ">
                    <div class="col-sm-offset-4 col-sm-7">
                        <strong><? if (isset($editedComment)) echo 'Zmień'; else echo 'Dodaj' ?> komentarz</strong>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-sm-4" for="commentText">Treść komentarza:</label>
                    <div class="col-sm-8">
                        <textarea name="commentText" id="commentText" class="form-control"><? if (isset($editedComment)) echo $editedComment->getCommentText() ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-5 col-sm-7">
                        <button class="btn btn-info btn-xs" type="submit" name="<? echo $buttonName ?>" ><? if (isset($editedComment)) echo 'Zmień'; else echo 'Dodaj' ?></button>
                    </div>
                </div>

            </form>
        </div>


    </div>



</body>
</html>