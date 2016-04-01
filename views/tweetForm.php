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

// edytowanie tweetow
if (isset($_SESSION['user']) && isset($_GET['updateTweet']) && $_GET['updateTweet'] > 0 &&
    $_SERVER['REQUEST_METHOD'] === 'POST' && strlen($_POST['tweetText'])<=140) {
    $updateTweet = new Tweet();
    if ($updateTweet->loadTweetFromDb($conn, $_GET['updateTweet']) === false) {
        unset($updateTweet);
    }

    if ($_SESSION['user']->getUserId() == $updateTweet->getAuthorId()) {
        $updateTweet->setTweetText($_POST['tweetText']);

        if ($updateTweet->updateTweet($conn)) {
            header('Location: index.php');
        }
        else {
            echo 'Nie udało się zmienić tweeta, spróbuj jeszcze raz';
        }
    }
}
else if (isset($_GET['updateTweet']) && strlen($_POST['tweetText'])>140){
    echo 'Nie udało się dodać tweeta - tekst jest za długi';
}

// usuwanie tweetow
if (isset($_SESSION['user']) && isset($_GET['deleteTweet']) && $_GET['deleteTweet'] > 0 ) {
    $deleteTweet = new Tweet();
    if ($deleteTweet->loadTweetFromDb($conn, $_GET['deleteTweet']) === false) {
        unset($deleteTweet);
    }

    if ($_SESSION['user']->getUserId() == $deleteTweet->getAuthorId()) {
        if ($deleteTweet->deleteTweet($conn)) {
            header('Location: index.php');
        }
        else {
            $message = 'Nie udało się usunąć tweeta, spróbuj jeszcze raz';
        }
    }
}


// dodawanie tweeta
if (isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addTweet']) &&
    !isset($_GET['updateTweet'])  && !isset($_GET['deleteTweet']) && strlen($_POST['tweetText'])<=140) {

    if (strlen($_POST['tweetText'])<1) {
        $message = 'Proszę wpisać treść tweeta';
    }
    else {
        $user = $_SESSION['user'];
        $newTweet = new Tweet();
        $newTweet->setAuthorId($user->getUserId());
        $newTweet->setTweetText($_POST['tweetText']);

        if ($newTweet->createTweetAndAddToDb($conn)) {
            header('Location: index.php');;
        } else {
            $message = 'Nie udało się dodać, proszę spróbować ponownie';
        }
    }
}
else if (isset($_POST['addTweet']) && strlen($_POST['tweetText'])>140){
    $message = 'Nie udało się dodać tweeta - tekst jest za długi';
}

// wczytywanie tweeta do edycji
if (isset($_GET['editTweet']) && is_numeric($_GET['editTweet']) && $_GET['editTweet'] > 0 && isset($_SESSION['user'])) {
    $editedTweet = new Tweet();

    if ($editedTweet->loadTweetFromDb($conn, $_GET['editTweet']) === false) {
        unset($editedTweet);
    }

    if (isset($editedTweet) && $editedTweet->getAuthorId() != $_SESSION['user']->getUserId()) {
        unset($editedTweet);
    }
}


if (isset($message) && isset($messageType)) {
    showMessage($message, $messageType);
}

?>
<div class="well" style="width: 500px; margin: 0 auto; margin-top: 20px;">
    <form class="form-horizontal" method="post" action="<? if (isset($editedTweet)) echo '?updateTweet='.$editedTweet->getTweetId() ?>">

        <div class="form-group ">
            <div class="col-sm-offset-4 col-sm-7">
                <strong>Dodaj tweeta</strong>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="tweetText">Treść tweeta:</label>
            <div class="col-sm-8">
                <textarea name="tweetText" id="tweetText" class="form-control"><? if (isset($editedTweet)) echo $editedTweet->getTweetText() ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-5 col-sm-7">
                <button class="btn btn-info btn-xs" type="submit" name="addTweet" ><? if (isset($editedTweet)) echo 'Zmień'; else echo 'Dodaj' ?></button>
            </div>
        </div>

    </form>
</div>