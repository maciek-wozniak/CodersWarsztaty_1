<?php


Class DbConnection {
    private static $serverName = 'localhost';
    private static $userName = 'twitter-root';
    private static $password = '';
    private static $dataBase = 'twitter_db';

    public static function getConnection() {
        if ($conn = new mysqli(self::$serverName, self::$userName, self::$password, self::$dataBase)) {
            return $conn;
        }
        return false;
    }

}

$conn = DbConnection::getConnection();
$messageType = 'danger';

function showMessage($text, $type) {
    echo '<div class="alert alert-'.$type.'" role="alert" style="width: 400px; margin: 0 auto; margin-top: 20px;">'.$text.'</div>';
}


