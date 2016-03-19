<?php

include_once dirname(__FILE__).'/../Classes/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $messageType = 'danger';
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Proszę wypełnić wszystkie pola";
    }
    else {
        $userObject = new User();
        $userLogin = $userObject -> login($email, $password);
        if ($userLogin === true) {

            $messageType = 'success';
            unset($userObject);
            unset($userLogin);
            $loggedUser = new User();
            unset($_SESSION['user']);
            $_SESSION['user'] = $loggedUser;
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
            <div class="col-sm-offset-5 col-sm-7">
                <button class="btn btn-info btn-xs" type="submit" name="login">Loguj</button>
            </div>
        </div>

    </form>
</div>
</div>