<?php
include_once dirname(__FILE__).'/../Classes/DbConnection.php';
include_once dirname(__FILE__).'/../Classes/User.php';
include_once dirname(__FILE__).'/../Classes/Message.php';

session_start();
if (isset($_SESSION['user']) ) {
    $user = $_SESSION['user'];
}
else {
    return false;
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userInfo = new User(-1);
    $userInfo->loadUserFromDb($_GET['id']);
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

                </div>
            </div>




    </div>

</body>
</html>