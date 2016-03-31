<?php

require_once dirname(__FILE__).'/../Classes/allClasses.php';

// logujemy sie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $messageType = 'danger';
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Proszę wypełnić wszystkie pola";
    }
    else  if ( !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Proszę podać prawidłowy e-mail';
    }

    else{ // udalo sie zalogowac
        $userObject = new User();
        $userLogin = $userObject -> login($conn, $email, $password);
        if ($userLogin === true) {
            unset($userObject);
            unset($userLogin);
            header("Location: index.php");
        }
        else {
            $message = 'Nie udało się zalogować, spróbuj ponownie.';
        }
    }
}

?>

<?php

if (isset($message) && isset($messageType)) {
    showMessage($message, $messageType);
}

function showMessage($text, $type) {
    echo '<div class="alert alert-'.$type.'" role="alert" style="width: 400px; margin: 0 auto; margin-top: 20px;">'.$text.'</div>';
}

?>

<div class="row">
<div class="well" style="width: 400px; margin: 0 auto; margin-top: 20px;">
    <form class="form-horizontal" method="post" action="">

        <div class="form-group ">
            <div class="col-sm-offset-5 col-sm-7">
                <strong>Logowanie</strong>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="email">E-mail:</label>
            <div class="col-sm-8">
                <input name="email" id="email" type="text" maxlength="255" value=""/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-sm-4" for="password">Hasło:</label>
            <div class="col-sm-7">
                <input name="password" id="password" type="password" maxlength="255" value=""/>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <button class="btn btn-info btn-xs" type="submit" name="login">Loguj</button>
                <a class="btn btn-info btn-xs" href="views/registration.php">Zarejestruj</a>
            </div>
        </div>

    </form>
</div>
</div>