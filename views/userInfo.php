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

// Ładujemy użytkownika, zeby wyswietlic o nim informacje
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userInfo = new User();
    if ($userInfo->loadUserFromDb($conn, $_GET['id']) === false) {
        unset($userInfo);
    }
}

if (!isset($userInfo)) {
    header('Location: ../');
}


// Proponujemy przyjaźń
if (isset($_GET['friend']) && $_GET['friend'] == 'yup' && !$_SESSION['user']->areWeFriends($conn, $userInfo->getUserId())) {
    if ($_SESSION['user']->proposeFriendship($conn, $userInfo->getUserId())) {
        $message = 'Przyjaźń zaproponowana';
        $messageType = 'success';
    }
    else {
        $message = 'Coś poszło nie tak';
    }
}


// Akceptujemy przyjazn
if (isset($_GET['acceptFriendship']) && $_GET['acceptFriendship'] == 'yes' && !$_SESSION['user']->areWeFriends($conn, $userInfo->getUserId())) {
    if ($_SESSION['user']->acceptFriendship($conn, $userInfo->getUserId()) == true) {
        $message = 'Przyjaźń zaakceptowana';
        $messageType = 'success';
    }
    else {
        $message = 'Coś poszło nie tak';
    }
}

// Nie akceptujemy przyjazni
if (isset($_GET['dontAcceptFriendship']) && $_GET['dontAcceptFriendship'] == 'yes' && !$_SESSION['user']->areWeFriends($conn, $userInfo->getUserId())) {
    if ($_SESSION['user']->doNotAcceptFriendship($conn, $userInfo->getUserId()) == true) {
        $message = 'Przyjaźń odrzucona';
        $messageType = 'success';
    }
    else {
        $message = 'Coś poszło nie tak';
    }
}


// Usuwamy przyjaciela
if (isset($_GET['deleteFriend']) && $_GET['deleteFriend'] == 'yup' && $_SESSION['user']->areWeFriends($conn, $userInfo->getUserId())) {
    if ($_SESSION['user']->deleteFriend($conn, $userInfo->getUserId()) == true) {
        $message = 'Przyjaciel usunięty';
        $messageType = 'success';
    }
    else {
        $message = 'Coś poszło nie tak';
    }
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>myTwitter - user info</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="../js/script.js" type="text/javascript"></script>
</head>
<body>

    <div class="container">

        <div class="row">
            <div class=" col-sm-6 col-sm-offset-3" style="margin-top: 10px;">
                <a href="../">Strona główna</a>
            </div>
        </div>

        <div class="row">
            <div class=" col-sm-6 col-sm-offset-3" style="margin-top: 10px;"><?

                if (isset($message) && isset($messageType)) {
                    showMessage($message, $messageType);
                }        ?>

            </div>
        </div>


            <div class="row">
                <div class="well col-sm-6 col-sm-offset-3" style="margin-top: 10px;">

                    <?

                    $proposeFriendshipLink = 'userInfo.php?id='.$userInfo->getUserId().'&friend=yup';
                    $btnDeleteFriendship = '';

                    if ($_SESSION['user']->areWeFriends($conn, $userInfo->getUserId())) {
                        $linkText = 'Mój przyjaciel <3';
                        $buttonType = 'success';
                        $class = '';
                        $proposeFriendshipLink = 'userInfo.php?id='.$userInfo->getUserId();
                        $btnDeleteFriendship = ' <a href="userInfo.php?id='.$userInfo->getUserId().'&deleteFriend=yup" class="btn btn-danger btn-xs">Usuń przyjaciela</a>';
                    }
                    else if ($_SESSION['user']->isThereFriendshipRequest($conn, $userInfo->getUserId())) {
                        if ($_SESSION['user']->didIProposeFriendship($conn, $userInfo->getUserId())) {
                            $linkText = 'Przyjaźń zaproponowana - czekamy na odpowiedź';
                            $class = 'disabledLink';
                        }
                        else {
                            $friendMsg = $_SESSION['user']->findMyProposalFriendshipMsgWith($conn, $userInfo->getUserId());
                            $linkText = 'Odpowiedz na propozycję przyjaźni';
                            $proposeFriendshipLink = 'messagePanel.php?page=inbox&msg='.$friendMsg->getMessageId();
                            $class = '';
                        }
                        $buttonType = 'warning';
                    }
                    else {
                        $linkText = 'Zaproponuj przyjaźń';
                        $buttonType = 'primary';
                        $class = '';
                    }

                    if (isset($userInfo)) {
                        echo 'E-mail: '.$userInfo->getEmail().'<br>';
                        echo 'Username: '.$userInfo->getUsername().'<br>';
                        if ($_SESSION['user']->getUserId() != $userInfo->getUserId()) {
                            echo '<a class="btn btn-'.$buttonType.' btn-xs '.$class.'" href="'.$proposeFriendshipLink.'">'.$linkText.'</a> ';
                            echo '<a class="btn btn-primary btn-xs" href="messagePanel.php?userId='.$userInfo->getUserId().'">Wyślij wiadomość</a>';
                            echo $btnDeleteFriendship.'<br>';
                        }
                        echo '<br>';

                        echo 'Tweety użytkownika:<br>';
                        $allUserTweets = $userInfo->getAllMyTweets($conn);
                        foreach ($allUserTweets as $tweet) {
                            $tweet->showTweet($conn);
                            echo '<br>';
                        }

                    }


                    ?>


                </div>
            </div>




    </div>

</body>
</html>