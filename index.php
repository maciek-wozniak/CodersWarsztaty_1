<?php

require_once dirname(__FILE__).'/Classes/allClasses.php';

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
        <div class="col-sm-12 navbar navbar-default" style="padding-bottom: 20px;"><?php

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
        <div class="col-sm-12" style="margin-top: 10px;">
            <?php
                if (isset($_SESSION['user'])) {
                    $arrayMyTweets = $user->getAllMyTweets($conn);

                    foreach ($arrayMyTweets as $tweet) {
                        echo $tweet->showTweet($conn).'<br>';
                    }
                }
            ?>
        </div>
    </div>

        <!-- Next panel -->
    <div class="row">
        <div class="col-sm-12">

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