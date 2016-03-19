<?php

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
Witaj <? echo $user->email ?><br>
<a href="?login=false">Wyloguj</a>


