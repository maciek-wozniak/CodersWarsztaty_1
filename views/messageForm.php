<?php

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}
else {
    header('Location: ../');
}

// Wysyłanie wiadomości
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['sendMessage'])){
    $receiver = new User();
    if (!isset($_POST['messageReceiver']) || !(strlen($_POST['messageReceiver'])>1)) {
        $message = 'Proszę podać odbiorcę wiadomości';
    }
    else if (!isset($_POST['messageTitle']) || !(strlen($_POST['messageTitle'])>1)) {
        $message = 'Proszę wpisać tytuł wiadomości';
    }
    else if (!isset($_POST['messageText']) || !(strlen($_POST['messageText'])>1)) {
        $message = 'Proszę wpisać tekst wiadomości';
    }
    else if (!($receiver->findUserByMail($conn, $_POST['messageReceiver']))) {
        $message = 'Nie znaleziono użytkownika o podanym e-mailu';
    }
    else {
        $message = new Message();
        $message->setReceiverId($receiver->getUserId());
        $message->setMessageTitle($_POST['messageTitle']);
        $message->setMessageText($_POST['messageText']);
        $message->setSenderId($_SESSION['user']->getUserId());

        if ($message->sendMessage($conn)){
            header('Location: messagePanel.php?page=outbox');
        }
        else {
            $message = 'Nie udało się wysłać wiadomości, proszę spróbować jeszcze raz';
        }
    }
}

// Jeżeli odpowiadamy na maila
if (isset($_GET['reply']) && is_numeric($_GET['reply'])) {
    $replyToMsg = new Message();
    if ($replyToMsg->loadMessageFromDb($conn, $_GET['reply']) === false) {
        unset($replyToMsg);
    }

    if ($replyToMsg->getReceiverId() != $_SESSION['user']->getUserId()) {
        return false;
    }

    $replyToUser = new User();
    if ($replyToUser->loadUserFromDb($conn, $replyToMsg->getSenderId()) === false) {
        unset($replyToMsg);
        unset($replyToMsg);
    }
}

// Jeżeli wybraliśmy użytkownika do którego chcemy pisać

if (isset($_GET['userId']) && is_numeric($_GET['userId'])) {
    $replyToUser = new User();
    if ($replyToUser->loadUserFromDb($conn, $_GET['userId'])===false) {
        unset($replyToUser);
    }
}

if (isset($message) && isset($messageType)) {
    showMessage($message, $messageType);
}
?>

<div class="well" style="width: 900px; margin: 0 auto; margin-top: 20px;">
    <form class="form-horizontal" method="post" action="messagePanel.php">

        <div class="form-group ">
            <div class="col-sm-offset-5 col-sm-7">
                <strong>Wyślij wiadomość</strong>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="messageReceiver">Adresat:*</label>
            <div class="col-sm-8">
                <input type="text" name="messageReceiver" id="messageReceiver" class="form-control"
                       value="<?
                            if (isset($_POST['sendMessage']) && isset($_POST['messageReceiver'])) {
                                echo $_POST['messageReceiver'];
                            }
                            else if (isset($replyToUser)) {
                                echo $replyToUser->getEmail() ;
                            } ?>" />
                <select class="form-control" name="messageReceiverSelect" id="messageReceiverSelect">
                    <option value="">Wybierz adresata</option>
                    <?php
                        $allUsers = User::GetAllUsers($conn);
                        foreach ($allUsers as $user) {
                            if ($user->getUserId() != $_SESSION['user']->getUserId()) {

                                $selected = '';
                                if (isset($replyToUser) && $replyToUser->getUserId() == $user->getUserId()) {
                                    $selected = ' selected';
                                }

                                echo '<option value="' . $user->getEmail() . '"'.$selected.'>' . $user->getEmail() . ' : ' . $user->getUsername().'</option>';
                            }
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="messageTitle">Tytuł wiadomości:</label>
            <div class="col-sm-8">
                <input type="text" name="messageTitle" id="messageTitle" class="form-control"
                       value="<?
                            if (isset($_POST['sendMessage']) && isset($_POST['messageTitle'])) {
                                echo $_POST['messageTitle'];
                            }
                            else if (isset($replyToMsg)) {
                                echo 'Re: '.$replyToMsg->getMessageTitle();
                            } ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3" for="messageText">Treść wiadomości:</label>
            <div class="col-sm-8">
                <textarea name="messageText" cols="20" rows="10" id="messageText" class="form-control"><?
                        if (isset($_POST['sendMessage']) && isset($_POST['messageText'])) {
                            echo $_POST['messageText'];
                        }
                        else if (isset($replyToMsg) && isset($_GET['quote']) && $_GET['quote'] == 'yes') {
                            echo $replyToUser->getUsername();
                            echo " napisał ".$replyToMsg->getSendTime().":\n";
                            echo "---------------------------\n";
                            echo $replyToMsg->getMessageText();
                        }
                    ?></textarea>
                * - wybierz z listy lub wprowadź adres mailowy użytkownika, któremu chcesz wysłać maila
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-5 col-sm-7">
                <button class="btn btn-info btn-xs" type="submit" name="sendMessage" >Wyślij</button>
            </div>
        </div>

    </form>
</div>