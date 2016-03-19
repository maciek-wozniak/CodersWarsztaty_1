<?php
require_once 'Classes/DbConnection.php';
include_once 'Classes/User.php';
session_start();


$conn = DbConnection::getConnection();
if (!$conn) {
    echo "Nie udało się połączyć z bazą! " .$conn->error;
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

<div class="container">

    <div class="row">
        <!-- Left panel -->
        <div class="col-sm-3" style="background-color: lime; min-height: 100%;"><?php

            if (isset($_SESSION['user'])) {
                include_once dirname(__FILE__).'/views/userPanel.php';
            }
            else {
                include_once dirname(__FILE__).'/views/login.php';
            }

        ?>
        </div>


        <!-- Main panel -->
        <div class="col-sm-6" style="background-color: red; height: 200px;"></div>

        <!-- Left panel -->
        <div class="col-sm-3" style="background-color: yellow; height: 200px;"></div>
    </div>

</div>


</body>
</html>