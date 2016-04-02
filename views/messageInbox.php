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

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $msgToDelete = new Message();
    $msgToDelete->loadMessageFromDb($conn, $_GET['delete']);
    $msgToDelete->receiverDeletedMsg($conn);
}

?>


<table class="table table-striped">
    <thead>
    <th>Lp</th>
    <th>Nadawca</th>
    <th>Tytuł</th>
    <th>Początek wiadomości</th>
    <th>Odebrano</th>
    <th> </th>
    </thead>

    <?php

    $i = 1;
    $sentMessages = $user->getReceivedMessages($conn);
    foreach ($sentMessages as $msg) {

        if ($msg->getReaded()) {
            $style = '';
            $styleTextBold = '';
            $class = 'msg'. $msg->getMessageId();
        }
        else {
            $style = 'style="display: none;"';
            $styleTextBold = 'font-weight: bold;';
            $class = '';
        }

        if (isset($_GET['msg']) && $_GET['msg'] == $msg->getMessageId()) {
            $style = 'style="display: table-row;"';
            $styleTextBold = '';
            $msg->receiverReadedMsg($conn);
        }

        $usr = new User();
        $usr->loadUserFromDb($conn, $msg->getSenderId());

        ?>
        <tr data-toggle="collapse" data-target=".msg<? echo $msg->getMessageId(); ?>" style="cursor: pointer; <? echo $styleTextBold ?>">
            <td style="vertical-align: middle;"><? echo $i ?></td>
            <td style="vertical-align: middle;"><a href="userInfo.php?id=<? echo $usr->getUserId() ?>"><? echo $usr->getEmail().' (' . $usr->getUsername() . ')' ?></a></td>
            <td style="vertical-align: middle;"><? echo $msg->getMessageTitle() ?></td>
            <td style="vertical-align: middle;"><? echo strip_tags(substr($msg->getMessageText(),0,30)) ?></td>
            <td style="vertical-align: middle;"><? echo $msg->getSendTime() ?></td>
            <td> <a href="<? if (!($msg->getReaded())) echo 'messagePanel.php?page=inbox&msg='.$msg->getMessageId(); else echo '#' ?>">Otwórz</a><br>
                 <a href="messagePanel.php?reply=<? echo $msg->getMessageId() ?>">Odpowiedz</a> <br>
                 <a href="messagePanel.php?reply=<? echo $msg->getMessageId() ?>&quote=yes">Odpowiedz cytując</a>
            </td>
        </tr>

        <tr class="collapse <? echo $class ?>" <? echo $style ?>>
            <td colspan="6"><? echo stripslashes(nl2br($msg->getMessageText())) ?></td>
        </tr>

        <tr class="collapse <? echo $class ?>" <? echo $style ?>>
            <td colspan="6S" style="text-align: center;">
                <a href="messagePanel.php?page=inbox&delete=<? echo $msg->getMessageId(); ?>">Usuń wiadomość</a>
            </td>
        </tr>

        <?
        $i++;
    }

    ?>

</table>