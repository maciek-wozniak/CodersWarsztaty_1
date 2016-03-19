<?php

include_once dirname(__FILE__).'/DbConnection.php';
include_once dirname(__FILE__).'/Tweet.php';

Class User {
    private $id;
    private $password;
    private $salt;
    public $email;
    public $username;

    public function __construct() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!empty($_SESSION['user'])) {
            $user = $_SESSION['user'];
            $this->id = $user['id'];
            $this->email = $user['email'];
            $this->username = $user['username'];
            $this->salt = $user['salt'];
        }
    }

    public function addUser($mail, $password, $name = null) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || empty($password)) {
            return false;
        }
        $this->generateSalt();
        $hashedPassword = $this->hashPassword($password);
        $connection = DbConnection::getConnection();
        $insertUserQuery = 'INSERT INTO users (username, email, password, salt, createdUser) VALUES
                ("'.$name.'", "'.$mail.'", "'.$hashedPassword.'", "'.$this->salt.'","'.date('Y-m-d').'")';
        $result = $connection->query($insertUserQuery);

        if ($connection->error && $connection->errno == 1062) {
             return 'Istnieje użytkownik z tym adresem e-mail';
        }

        $connection->close();
        return $result;
    }

    private function hashPassword($password) {
        $options = array(
            'cost' => 11,
            'salt' => $this->salt
        );
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    private function generateSalt() {
        $this->salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
    }

    public function logIn($mail, $password) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || empty($password)) {
            return false;
        }

        $conn = DbConnection::getConnection();
        $getUserQuery = 'SELECT * FROM users WHERE email="' . $mail . '" AND deleted=0';
        $result = $conn->query($getUserQuery);
        if ($result->num_rows == 0) {
            return false;
        }
        $user = $result->fetch_assoc();

        $this->salt = $user['salt'];
        $hashedPassword = $this->hashPassword($password);

        if ($hashedPassword != $user['password']) {
            return false;
        }

        unset($user['password']);
        $_SESSION['user'] = $user;
        $conn->close();
        $conn=null;
        return true;
    }

    public function logOut() {
        unset($_SESSION['user']);
    }

    public function updateUser($mail, $name) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) ) {
            return false;
        }

        $connection = DbConnection::getConnection();
        $sqlIsUser = 'SELECT * FROM users WHERE deleted=0 AND id="'.$this->id.'" ';
        $result = $connection->query($sqlIsUser);
        if ($result->num_rows != 1) {
            return false;
        }

        $updateUserQuery = 'UPDATE users SET username="'.$name.'", email="'.$mail.'",
                        updatedUser="'.date('Y-m-d').'" WHERE id="'.$this->id.'")';
        $result = $connection->query($updateUserQuery);

        $connection->close();
        $connection=null;
        return $result;
    }

    public function deleteUser() {
        $conn = DbConnection::getConnection();
        $getUserQuery = 'UPDATE users SET updatedUser="'.date('Y-m-d').'", deleted=1 WHERE id="' . $this->id . '"';
        $result = $conn->query($getUserQuery);

        unset($_SESSION['user']);

        $conn->close();
        $conn=null;
        return $result;
    }

    public function getAllMyTweets() {
        $myTweets = array();

        $connection = DbConnection::getConnection();
        $getTweets = 'SELECT * FROM tweets WHERE deleted=0 AND author_id=' .$this->id;
        $result = $connection->query($getTweets);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $myTweet = new Tweet();
                $myTweet->loadTweetFromDb($row['id']);
                $myTweets[$myTweet->getTweetId()] = $myTweet;
            }
        }
        return $myTweets;
    }

    public function getAllMessages() {

    }

    public function getAllFriends() {

    }

    public function updateUserPassword($oldPassword, $newPassword, $confirmPassword) {
        $conn = DbConnection::getConnection();
        $getUserQuery = 'SELECT * FROM users WHERE deleted=0 id="' . $this->id . '"';
        $result = $conn->query($getUserQuery);
        if ($result->num_rows == 0) {
            return false;
        }
        $user = $result->fetch_assoc();
        $this->salt = $user['salt'];

        $hashedOldPassword = $this->hashPassword($oldPassword);
        if ($hashedOldPassword != $user['password']) {
            return 'Niepoprawne stare hasło';
        }

        if ($newPassword != $confirmPassword) {
            return 'Hasła nowe różne';
        }

        $hashedNewPassword = $this->hashPassword($newPassword);
        $updateUserQuery = 'UPDATE users SET password="'.$hashedNewPassword.'",
                            updatedUser="'.date('Y-m-d').'" WHERE id="'.$this->id.'")';
        $result = $conn->query($updateUserQuery);

        $conn->close();
        $conn=null;
        return $result;
    }


}