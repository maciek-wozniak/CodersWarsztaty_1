<?php
require_once 'Classes/DbConnection.php';
include_once 'Classes/User.php';


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




</body>
</html>