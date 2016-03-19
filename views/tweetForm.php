<?php

include_once dirname(__FILE__).'/../Classes/Tweet.php';

if (isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addTweet'])) {
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

?>
<div class="well" style="width: 500px; margin: 0 auto; margin-top: 20px;">
    <form class="form-horizontal" method="post" action="">

        <div class="form-group ">
            <div class="col-sm-offset-4 col-sm-7">
                <strong>Dodaj tweeta</strong>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="tweetText">Treść tweeta:</label>
            <div class="col-sm-8">
                <textarea name="tweetText" id="tweetText" class="form-control"></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-5 col-sm-7">
                <button class="btn btn-info btn-xs" type="submit" name="addTweet" >Dodaj</button>
            </div>
        </div>

    </form>
</div>