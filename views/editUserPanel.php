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

// Usuwamy usera
if (isset($user) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteAccount'])) {
    if ($user->deleteUser($conn, $_POST['password'])) {
        header('Location: ../');
    }
    else if (empty($_POST['password'])){
        $message = 'Nie podano hasła';
        $messageType = 'danger';
    }
    else {
        $message = 'Nie udało się usunąć konta';
        $messageType = 'danger';
    }
}

// Zmieniamy usera dane bez hasla
if (isset($user) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changeUserName'])) {
    if ($user->updateUser($conn, $_POST['email'], $_POST['username'])) {
        header('Location: ../');
    }
    else {
        $message = 'Nie udało się zmienić danych, spróbuj jeszcze raz';
        $messageType = 'danger';
    }
}

// Zmeiniamy usera haslo
if (isset($user) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changePassword'])) {
    if ($user->updateUserPassword($conn, $_POST['password'], $_POST['newPassword'], $_POST['confirmPassword'])) {
        header('Location: ../');
    }
    else if ($_POST['newPassword'] !=  $_POST['confirmPassword']) {
        $message = 'Nie udało się zmienić hasła, powtórzone hasło nie jest takie samo jak nowe';
        $messageType = 'danger';
    }
    else {
        $message = 'Nie udało się zmienić hasła, spróbuj jeszcze raz';
        $messageType = 'danger';
    }
}

if (isset($message) && isset($messageType)) {
    showMessage($message, $messageType);
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>myTwitter</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>

<div class="well" style="width: 550px; margin: 0 auto; margin-top: 20px;">
    <form class="form-horizontal" method="post" action="">

        <div class="form-group ">
            <div class="col-sm-offset-5 col-sm-7">
                <strong>Edycja danych</strong>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="username">Nazwa użytkownika:</label>
            <div class="col-sm-8">
                <input name="username" style="width:300px;" id="username" type="text" maxlength="255" placeholder="Nazwa użytkownika" class="form-control" value="<? if (isset($user)) echo $user->getUsername() ?>"/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="email">E-mail:</label>
            <div class="col-sm-8">
                <input type="email" style="width:300px;" name="email" id="email" maxlength="255" placeholder="E-mail" class="form-control" value="<? if (isset($user)) echo $user->getEmail() ?>"/>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <button class="btn btn-info btn-xs" type="submit" name="changeUserName">Zmień</button>
                <a class="btn btn-info btn-xs" href="../">Anuluj</a>
            </div>
        </div>

    </form>
</div>


<div class="well" style="width: 550px; margin: 0 auto; margin-top: 20px;">
    <form class="form-horizontal" method="post" action="">

        <div class="form-group ">
            <div class="col-sm-offset-5 col-sm-7">
                <strong>Zmiana hasła</strong>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="password">Aktualne hasło:</label>
            <div class="col-sm-8">
                <input type="password" style="width:300px;" name="password" id="password" maxlength="255" placeholder="Aktualne hasło" class="form-control" value=""/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="newPassword">Nowe hasło:</label>
            <div class="col-sm-7">
                <input name="newPassword" style="width:300px;" id="newPassword" type="password" maxlength="255" placeholder="Nowe hasło" class="form-control" value=""/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="confirmPassword">Powtórz nowe hasło:</label>
            <div class="col-sm-7">
                <input name="confirmPassword" style="width:300px;" id="confirmPassword" type="password" maxlength="255" placeholder="Powtórz hasło" class="form-control" value=""/>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <button class="btn btn-info btn-xs" type="submit" name="changePassword">Zmień</button>
                <a class="btn btn-info btn-xs" href="../">Anuluj</a>
            </div>
        </div>

    </form>
</div>

<div class="alert alert-danger" style="width: 550px; margin: 0 auto; margin-top: 20px;">
    <form class="form-horizontal" method="post" action="">

        <div class="form-group ">
            <div data-toggle="collapse" data-target=".deleteAccount" class="col-sm-offset-5 col-sm-7">
                <strong style="cursor: pointer;">Usuń konto</strong>
            </div>
        </div>

        <div class="form-group collapse deleteAccount" >
            <label class="control-label col-sm-4" for="password">Hasło:</label>
            <div class="col-sm-8">
                <input type="password" style="width:300px;" name="password" id="password" maxlength="255" placeholder="Podaj swoje hasło" class="form-control" value=""/>
            </div>
        </div>


        <div class="form-group collapse deleteAccount" >
            <div class="col-sm-offset-4 col-sm-8">
                <button class="btn btn-warning btn-xs" type="submit" name="deleteAccount">Usuń konto</button>
                <a class="btn btn-info btn-xs" href="../">Anuluj</a>
            </div>
        </div>

    </form>
</div><br>