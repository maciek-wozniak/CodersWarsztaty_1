<?php
include_once dirname(__FILE__).'/../Classes/DbConnection.php';
include_once dirname(__FILE__).'/../Classes/User.php';
include_once dirname(__FILE__).'/../Classes/Message.php';

session_start();
if (isset($_SESSION['user']) ) {
    $user = $_SESSION['user'];
}
else {
    return false;
}


$messageType = 'danger';
function showMessage($text, $type) {
    echo '<div class="alert alert-'.$type.'" role="alert" style="width: 400px; margin: 0 auto; margin-top: 20px;">'.$text.'</div>';
}


?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>myTwitter - wiadomości</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

    <div class="container">

        <div class="row">
            <div class="col-sm-2 col-sm-offset-2">
                <a class="btn btn-primary btn-xs" href="../" style="margin: 5px; margin-left: 0px;">Strona główna</a>
            </div>

            <div class="col-sm-2">
                <a class="btn btn-primary btn-xs" href="messagePanel.php" style="margin: 5px; margin-left: 0px;">Nowa wiadomość</a>
            </div>

            <div class="col-sm-2">
                <a class="btn btn-primary btn-xs" href="messagePanel.php?page=inbox" style="margin: 5px; margin-left: 0px;">Srzynka odbiorcza</a>
            </div>

            <div class="col-sm-2">
                <a class="btn btn-primary btn-xs" href="messagePanel.php?page=outbox" style="margin: 5px; margin-left: 0px;">Skrzynka nadawcza</a>
            </div>
        </div>



        <?php

        if (isset($_GET['page']) && $_GET['page'] == 'inbox' ) {
            include_once 'messageInbox.php';
        }
        else if (isset($_GET['page']) && $_GET['page'] == 'outbox') {
            include_once 'messageOutbox.php';
        }
        else {
            include_once 'messageForm.php';
        }

        ?>
    </div>

</body>
</html>