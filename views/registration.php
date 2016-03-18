<?php

include '../Classes/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageType = 'danger';
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $username = $_POST['username'];

    if (empty($email) || empty($password) || empty($confirmPassword)) {
        $message = "Proszę wypełnić wszystkie pola";
    }
    else if($password != $confirmPassword) {
        $message = 'Hasła nie pasują do siebie';
    }
    else {
        $userObject = new User();
        $userAdd = $userObject -> addUser($email, $password, $username);
        if ($userAdd === true) {
            $message = 'Użytkownik został dodany. Możesz się teraz zalogować<br><a href="login.php">Zaloguj się</a>';
            $messageType = 'success';
        }
        else if ($userAdd == false) {
            $message = 'Nie udało się dodać użytkownika. Spróbuj ponownie';
        }
        else {
            $message = $userAdd;
        }
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

<?php

if (isset($message) && isset($messageType)) {
    showMessage($message, $messageType);
}

function showMessage($text, $type) {
    echo '<div class="alert alert-'.$type.'" role="alert" style="width: 550px; margin: 0 auto; margin-top: 20px;">'.$text.'</div>';
}

?>

<div class="well" style="width: 550px; margin: 0 auto; margin-top: 20px;">
    <form class="form-horizontal" method="post" action="registration.php">

        <div class="form-group ">
            <div class="col-sm-offset-5 col-sm-7">
                <strong>Rejestracja</strong>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="username">Nazwa użytkownika:</label>
            <div class="col-sm-8">
                <input name="username" style="width:300px;" id="username" type="text" maxlength="255" placeholder="Nazwa użytkownika" class="form-control" value=""/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="email">E-mail:</label>
            <div class="col-sm-8">
                <input type="email" style="width:300px;" name="email" id="email" maxlength="255" placeholder="E-mail" class="form-control" value=""/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="password">Hasło:</label>
            <div class="col-sm-7">
                <input name="password" style="width:300px;" id="password" type="password" maxlength="255" placeholder="Hasło" class="form-control" value=""/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="confirmPassword">Powtórz hasło:</label>
            <div class="col-sm-7">
                <input name="confirmPassword" style="width:300px;" id="confirmPassword" type="password" maxlength="255" placeholder="Powtórz hasło" class="form-control" value=""/>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-5 col-sm-7">
                <button class="btn btn-info btn-xs" type="submit" name="login">Loguj</button>
            </div>
        </div>

    </form>
</div>

</body>
</html>