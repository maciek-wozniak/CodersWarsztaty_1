<?php

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $msgToDelete = new Message();
    $msgToDelete->loadMessageFromDb($_GET['delete']);
    $msgToDelete->senderDeletedMsg();
}

?>


<table class="table table-striped">
    <thead>
        <th>Lp</th>
        <th>Odbiorca</th>
        <th>Tytuł</th>
        <th>Wysłano</th>
    </thead>



<?php


$i = 1;
$sentMessages = $user->getSentMessages();
foreach ($sentMessages as $msg) {
    $usr = new User(-1);
    $usr->loadUserFromDb($msg->getReceiverId());

    ?>
    <tr data-toggle="collapse" data-target=".msg<? echo $msg->getMessageId(); ?>" style="cursor: pointer;">
        <td><? echo $i ?></td>
        <td><a href="userInfo.php?id=<? echo $usr->getUserId() ?>"><? echo $usr->getEmail().' (' . $usr->getUsername() . ')' ?></a></td>
        <td><? echo $msg->getMessageTitle() ?></td>
        <td><? echo $msg->getSendTime() ?></td>
    </tr>

    <tr class="collapse msg<? echo $msg->getMessageId(); ?>">
        <td colspan="4"><? echo nl2br($msg->getMessageText()) ?></td>
    </tr>

    <tr class="collapse msg<? echo $msg->getMessageId(); ?>">
        <td colspan="4" style="text-align: center;"><a href="messagePanel.php?page=outbox&delete=<? echo $msg->getMessageId(); ?>">Usuń wiadomość</a></td>
    </tr>

    <?
    $i++;
}

?>

</table>
