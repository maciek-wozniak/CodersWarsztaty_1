<?php

include_once 'DbConnection.php';

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
        $insertUserQuery = 'INSERT INTO users (username, email, password, salt) VALUES
                ("'.$name.'", "'.$mail.'", "'.$hashedPassword.'", "'.$this->salt.'")';
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
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, $options);
        return $hashedPassword;
    }

    private function generateSalt() {
        $this->salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
    }

    public function logIn($mail, $password) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || empty($password)) {
            return false;
        }

        $conn = DbConnection::getConnection();
        $getUserQuery = 'SELECT * FROM users WHERE email="' . $mail . '" WHERE deleted=0';
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
        $updateUserQuery = 'UPDATE users SET username="'.$name.'", email="'.$mail.'" WHERE id="'.$this->id.'")';
        $result = $connection->query($updateUserQuery);

        $connection->close();
        $connection=null;
        return $result;
    }

    public function deleteUser() {
        $conn = DbConnection::getConnection();
        $getUserQuery = 'UPDATE users SET deleted=1 WHERE id="' . $this->id . '"';
        $result = $conn->query($getUserQuery);

        $conn->close();
        $conn=null;
        return $result;
    }

    public function getAllPosts() {

    }

    public function getAllMessages() {

    }

    public function getAllFriends() {

    }

    public function updateUserPassword($oldPassword, $newPassword, $confirmPassword) {
        $conn = DbConnection::getConnection();
        $getUserQuery = 'SELECT * FROM users WHERE id="' . $this->id . '"';
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
        $updateUserQuery = 'UPDATE users SET password="'.$hashedNewPassword.'" WHERE id="'.$this->id.'")';
        $result = $conn->query($updateUserQuery);

        $conn->close();
        $conn=null;
        return $result;
    }


}