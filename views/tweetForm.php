<?php

include_once dirname(__FILE__).'/../Classes/Tweet.php';

// edytowanie tweetow
if (isset($_SESSION['user']) && isset($_GET['updateTweet']) && $_GET['updateTweet'] > 0 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $updateTweet = new Tweet();
    $updateTweet->loadTweetFromDb($_GET['updateTweet']);
    if ($_SESSION['user']->getId() == $updateTweet->getAuthorId()) {
        $updateTweet->setTweetText($_POST['tweetText']);

        if ($updateTweet->updateTweet()) {
            header('Location: index.php');
        }
        else {
            echo 'Nie udało się zmienić tweeta, spróbuj jeszcze raz';
        }
    }
}

// dodawanie tweeta
if (isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addTweet']) && !isset($_GET['updateTweet'])) {
    $user = $_SESSION['user'];
    $newTweet = new Tweet();
    $newTweet->setAuthorId($user->getId());
    $newTweet->setTweetText($_POST['tweetText']);

    if ($newTweet->createTweetAndAddToDb()) {
        echo 'Dodano tweeta !';
    }
    else {
        echo 'Nie udało się dodać, proszę spróbować ponownie';
    }
}

// wczytywanie tweeta do edycji
if (isset($_GET['editTweet']) && is_numeric($_GET['editTweet']) && $_GET['editTweet'] > 0 && isset($_SESSION['user'])) {
    $editedTweet = new Tweet();
    $editedTweet->loadTweetFromDb($_GET['editTweet']);
    if ($editedTweet->getAuthorId() != $_SESSION['user']->getiD()) {
        unset($editedTweet);
    }
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
                <button class="btn btn-info btn-xs" type="submit" name="addTweet" >Dodaj</button>
            </div>
        </div>

    </form>
</div>