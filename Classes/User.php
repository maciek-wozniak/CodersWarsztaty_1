<?php

include_once 'DbConnection.php';

Class User {
    private $id;
    private $password;
    private $salt;
    public $email;
    public $username;

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

    public function login($mail, $password) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || empty($password)) {
            return false;
        }

        $conn = DbConnection::getConnection();
        $getUserQuery = 'SELECT * FROM users WHERE email="' . $mail . '"';
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

        $_SESSION['user'] = $user;
        $conn->close();
        $conn=null;
        return true;
    }

    public function updateUser($mail, $name = null) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) ) {
            return false;
        }
        $connection = DbConnection::getConnection();
        $insertUserQuery = 'UPDATE users SET username="'.$name.'", email="'.mail.'" WHERE id="'.$this->id.'")';
        $result = $connection->query($insertUserQuery);

        if ($result->error && $connection->errno == 1062) {
            return 'Istnieje użytkownik z tym adresem e-mail';
        }

        $connection->close();
        $connection=null;
        return $result;
    }

    public function deleteUser() {

    }

    public function getAllPosts() {

    }

    public function getAllMessages() {

    }

    public function getAllFriends() {

    }




}