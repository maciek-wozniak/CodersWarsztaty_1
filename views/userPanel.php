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

?>
<?php

// Wylogowanie
if (isset($_GET['login']) && $_GET['login'] == 'false' && isset($_SESSION['user'])) {
    unset($_SESSION['user']);
    header('Location: .');
}

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}

?>
Witaj <? echo $user->getEmail() . ' ' . $user->getUsername() . ' ' ?>
<a class="btn btn-primary btn-xs" href="<? echo ROOT_PATH; ?>">Moje tweety</a>
<a class="btn btn-primary btn-xs" href="<? echo ROOT_PATH; ?>/index.php?page=friendsTweets">Tweety przyjaciół</a>
<a class="btn btn-primary btn-xs" href="<? echo ROOT_PATH; ?>/index.php?page=friends">Przyjaciele</a>
<a class="btn btn-primary btn-xs" href="<? echo ROOT_PATH; ?>/views/messagePanel.php?page=inbox" style="margin: 5px; margin-left: 0px;">Wiadomości <span class="badge"><? echo $user->numberOfUnreadedMessages($conn) ?></span></a>
<a class="btn btn-primary btn-xs" href="<? echo ROOT_PATH; ?>/views/editUserPanel.php">Edytuj profil</a>
<a class="btn btn-primary btn-xs" href="<? echo ROOT_PATH; ?>/?login=false" style="margin: 5px; margin-left: 0px;">Wyloguj</a>


