<?php
require_once dirname(__FILE__).'/../Classes/allClasses.php';

session_start();
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}
else {
    header('Location: ../');
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userInfo = new User();
    $userInfo->loadUserFromDb($conn, $_GET['id']);
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
</head>
<body>

    <div class="container">

            <a href="../">Strona główna</a>


            <div class="row">
                <div class="well col-sm-6 col-sm-offset-3" style="margin-top: 10px;">

                    <?
                    if (isset($userInfo)) {
                        echo $userInfo->getEmail().'<br>';
                        echo $userInfo->getUsername();
                    }


                    ?>
                    Stronę wyświetlania użytkownika: Strona ta ma pokazać wszystkie wpisy danego
                    użytkownika (i pod każdym ilość komentarzy które ma).
                    Na tej stronie ma być też guzik który umożliwi nam wysłanie wiadomości do tego
                    użytkownika.
                     Stronę wyświetlania pos


                    Wiadomości wysłane mają wyświetlać odbiorcę, datę wysłania i początek
                    wiadomości (pierwsze 30 znaków).
                    Wiadomości odebrane mają wyświetlać nadawcę, datę wysłania i początek
                    wiadomości (pierwsze 30 znaków). Wiadomości jeszcze nie przeczytane mają być
                    jakoś oznaczone.

                    nie można do siebie wysyłać wiadomości

                </div>
            </div>




    </div>

</body>
</html>