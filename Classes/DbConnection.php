<?php


Class DbConnection {
    private static $serverName = 'localhost';
    private static $userName = 'twitter-root';
    private static $password = 'axZP447dmDtty6GM';
    private static $dataBase = 'twitter_db';

    public static function getConnection() {
        if ($conn = new mysqli(self::$serverName, self::$userName, self::$password, self::$dataBase)) {
            return $conn;
        }
        return false;
    }

}