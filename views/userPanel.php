<?php

require_once dirname(__FILE__).'/../Classes/allClasses.php';

session_start();
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}
else {
    header('Location: ../');
}

if (isset($message) && isset($messageType)) {
    showMessage($message, $messageType);
}

function showMessage($text, $type) {
    echo '<div class="alert alert-'.$type.'" role="alert" style="width: 400px; margin: 0 auto; margin-top: 20px;">'.$text.'</div>';
}

?>
<?php

if (isset($_GET['login']) && $_GET['login'] == 'false' && isset($_SESSION['user'])) {
    unset($_SESSION['user']);
    header('Location: .');
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}

?>
Witaj <? echo $user->getEmail() . ' ' . $user->getUsername() . ' ' ?>
Moje tweety
Tweety przyjaciół
Przyjaciele
<a class="btn btn-primary btn-xs" href="views/messagePanel.php?page=inbox" style="margin: 5px; margin-left: 0px;">Wiadomości <span class="badge"><? echo $user->numberOfUnreadedMessages($conn) ?></span></a>
<a class="btn btn-primary btn-xs" href="views/editUserPanel.php">Edytuj profil</a>
<a class="btn btn-primary btn-xs" href="?login=false" style="margin: 5px; margin-left: 0px;">Wyloguj</a>


