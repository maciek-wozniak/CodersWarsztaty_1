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

echo '<div class="well" style="width: 400px; margin: 0 auto; margin-top: 20px;">';

$arrayMyFriends = $user->getAllFriends($conn);

if (count($arrayMyFriends) > 0) {
    echo 'Moim przyjaciele:<ul>';
    foreach ($arrayMyFriends as $friend) {
        echo '<li style="list-style-type: none;">'.$friend->linkToUser() . '</li>';
    }
    echo '</ul>';
}
else {
    echo 'Nie masz jeszcze przyjaciół';
}

echo '</div>';