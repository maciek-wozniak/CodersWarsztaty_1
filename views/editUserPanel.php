<?php

include_once dirname(__FILE__).'/../Classes/User.php';

session_start();

if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
}



if (isset($user) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changeUserName'])) {
    if ($user->updateUser($_POST['email'], $_POST['username'])) {
        header('Location: ../');
    }
    else {
        echo 'Nie udało się zmienić danych, spróbuj jeszcze raz';
    }
}

if (isset($user) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changePassword'])) {
    if ($user->updateUserPassword($_POST['password'], $_POST['newPassword'], $_POST['confirmPassword'])) {
        header('Location: ../');
    }
    else {
        echo 'Nie udało się zmienić hasła, spróbuj jeszcze raz';
    }
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>myTwitter</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
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
                <input name="username" style="width:300px;" id="username" type="text" maxlength="255" placeholder="Nazwa użytkownika" class="form-control" value="<? if (isset($user)) echo $user->username ?>"/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="email">E-mail:</label>
            <div class="col-sm-8">
                <input type="email" style="width:300px;" name="email" id="email" maxlength="255" placeholder="E-mail" class="form-control" value="<? if (isset($user)) echo $user->email ?>"/>
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