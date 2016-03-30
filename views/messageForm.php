<?php



// Wysyłanie wiadomości
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['sendMessage'])){
    $receiver = new User(-1);
    if (!isset($_POST['messageReceiver']) || !(strlen($_POST['messageReceiver'])>1)) {
        $message = 'Proszę podać odbiorcę wiadomości';
    }
    else if (!isset($_POST['messageTitle']) || !(strlen($_POST['messageTitle'])>1)) {
        $message = 'Proszę wpisać tytuł wiadomości';
    }
    else if (!isset($_POST['messageText']) || !(strlen($_POST['messageText'])>1)) {
        $message = 'Proszę wpisać tekst wiadomości';
    }
    else if (!($receiver->findUserByMail($_POST['messageReceiver']))) {
        $message = 'Nie znaleziono użytkownika o podanym e-mailu';
    }
    else {
        $message = new Message();
        $message->setReceiverId($receiver->getUserId());
        $message->setMessageTitle($_POST['messageTitle']);
        $message->setMessageText($_POST['messageText']);
        $message->setSenderId($_SESSION['user']->getUserId());

        if ($message->sendMessage()){
            header('Location: messagePanel.php?page=outbox');
        }
        else {
            $message = 'Nie udało się wysłać wiadomości, proszę spróbować jeszcze raz';
        }
    }
}

if (isset($message) && isset($messageType)) {
    showMessage($message, $messageType);
}
?>

<div class="well" style="width: 500px; margin: 0 auto; margin-top: 20px;">
    <form class="form-horizontal" method="post" action="messagePanel.php">

        <div class="form-group ">
            <div class="col-sm-offset-4 col-sm-7">
                <strong>Wyślij wiadomość</strong>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="messageReceiver">Adresat:</label>
            <div class="col-sm-8">
                <input type="text" name="messageReceiver" id="messageReceiver" class="form-control" value="<? if (isset($_POST['sendMessage']) && isset($_POST['messageReceiver'])) echo $_POST['messageReceiver'] ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="messageTitle">Tytuł wiadomości:</label>
            <div class="col-sm-8">
                <input type="text" name="messageTitle" id="messageTitle" class="form-control" value="<? if (isset($_POST['sendMessage']) && isset($_POST['messageTitle'])) echo $_POST['messageTitle'] ?>" />
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="messageText">Treść wiadomości:</label>
            <div class="col-sm-8">
                <textarea name="messageText" id="messageText" class="form-control"><? if (isset($_POST['sendMessage']) && isset($_POST['messageText'])) echo $_POST['messageText'] ?></textarea>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-5 col-sm-7">
                <button class="btn btn-info btn-xs" type="submit" name="sendMessage" >Wyślij</button>
            </div>
        </div>

    </form>
</div>