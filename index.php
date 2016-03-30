<?php
require_once 'Classes/DbConnection.php';
include_once 'Classes/User.php';
session_start();


$conn = DbConnection::getConnection();
if (!$conn) {
    echo "Nie udało się połączyć z bazą! " .$conn->error;
}


?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>myTwitter</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>


<div class="container">

    <div class="row">
        <!-- First panel -->
        <div class="col-sm-12" style="background-color: lime; min-height: 100%;"><?php

            if (isset($_SESSION['user'])) {
                include_once dirname(__FILE__).'/views/userPanel.php';
            }
            else {
                include_once dirname(__FILE__).'/views/login.php';
            }

        ?>
        </div>
    </div>


        <!-- Second panel -->
    <div class="row">
        <div class="col-sm-12" style="background-color: red; height: 200px;">
            <?php
                if (isset($_SESSION['user'])) {
                    $arrayMyTweets = $user->getAllMyTweets();

                    foreach ($arrayMyTweets as $tweet) {
                        echo $tweet->showTweet().'<br>';
                    }
                }
            ?>
        </div>
    </div>

        <!-- Next panel -->
    <div class="row">
        <div class="col-sm-12" style="background-color: yellow; height: 200px;">

        <?
            if (isset($_SESSION['user'])) {
                include_once dirname(__FILE__).'/views/tweetForm.php';
            }
        ?>


        </div>
    </div>



</div>


</body>
</html>