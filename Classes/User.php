<?php

require_once 'allClasses.php';

Class User {
    private $userId;
    private $salt;
    private $email;
    private $username;

    public function __construct() {
        $this->userId = -1;
        $this->email = '';
        $this->username = '';
        $this->salt = '';
    }

    public function loadUserFromDb(mysqli $conn, $id) {
        $sqlUser = 'SELECT * FROM users WHERE deleted=0 AND id='.$id;
        $result = $conn->query($sqlUser);
        if ($result->num_rows!=1) {
            return false;
        }
        else {
            $user = $result->fetch_assoc();
            $this->userId = $user['id'];
            $this->email = $user['email'];
            $this->username = $user['username'];
            $this->salt = $user['salt'];
        }
        return $result;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function addUser(mysqli $connection, $mail, $password, $name = null) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || empty($password)) {
            return false;
        }
        $this->generateSalt();
        $hashedPassword = $this->hashPassword($password);
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

    public function logIn(mysqli $conn, $mail, $password) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || empty($password)) {
            return false;
        }

        $getUserQuery = 'SELECT * FROM users WHERE email="' . $mail . '" AND deleted=0';
        $result = $conn->query($getUserQuery);
        if ($result->num_rows == 0) {
            return false;
        }
        $user = $result->fetch_assoc();

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        $loggedUser = new User();
        $loggedUser->userId = $user['id'];
        $loggedUser->setEmail($user['email']);
        $loggedUser->setUsername($user['username']);
        $_SESSION['user'] = $loggedUser;

        return true;
    }

    public function logOut() {
        unset($_SESSION['user']);
    }

    public function updateUser(mysqli $connection, $mail, $name) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) ) {
            return false;
        }

        $sqlIsUser = 'SELECT * FROM users WHERE deleted=0 AND email="'.$mail.'" ';
        $result = $connection->query($sqlIsUser);
        if ($result->num_rows == 1 && !($this->email == $mail)) {
            echo '<div style="margin: 0 auto; margin-top: 10px; width: 400px;" class="alert alert-danger">Użytkownik o takim mailu już istnieje</div>';
            return false;
        }

        $sqlIsUser = 'SELECT * FROM users WHERE deleted=0 AND (email="'.$mail.'" OR id="'.$this->userId.'") ';
        $result = $connection->query($sqlIsUser);
        if ($result->num_rows != 1) {
            return false;
        }

        $updateUserQuery = 'UPDATE users SET username="'.$name.'", email="'.$mail.'",
                        editedUser="'.date('Y-m-d').'" WHERE id="'.$this->userId.'"';
        $result = $connection->query($updateUserQuery);

        if ($result) {
            unset($_SESSION['user']);
            $this->setEmail($mail);
            $this->setUsername($name);
            $_SESSION['user'] = $this;
        }

        return $result;
    }

    public function deleteUser(mysqli $conn, $password) {

        $getUserQuery = 'SELECT * FROM users WHERE deleted=0 AND id="' . $this->userId . '"';
        $result = $conn->query($getUserQuery);

        if ($result->num_rows == 0) {
            return false;
        }
        $user = $result->fetch_assoc();

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        $sentMsq = $this->getSentMessages($conn);
        foreach ($sentMsq as $msg) {
            $msg->senderDeletedMsg($conn);
        }

        $receivedMsg = $this->getReceivedMessages($conn);
        foreach ($receivedMsg as $msg) {
            $msg->receiverDeletedMsg();
        }

        $myComments = $this->getAllMyComments($conn);
        foreach ($myComments as $comment) {
            $comment->deleteComment();
        }

        $myTweets = $this->getAllMyTweets($conn);
        foreach ($myTweets as $deleteMyTweet) {
            $deleteMyTweet->deleteTweet($conn);
        }

        $getUserQuery = 'UPDATE users SET editedUser="'.date('Y-m-d').'", deleted=1 WHERE id="' . $this->userId . '"';
        $result = $conn->query($getUserQuery) or die($conn->error);

        unset($_SESSION['user']);

        /*
         *
         *  jeszcze delete friends
         */
        return $result;
    }

    public function getAllMyTweets(mysqli $conn) {
        return Tweet::GetAllUserTweets($conn, $this->getUserId());
    }

    public function getAllMyComments(mysqli $conn) {
        return TweetComment::GetAllUserComments($conn, $this->getUserId());
    }

    public function getSentMessages(mysqli $conn) {
        return Message::GetAllSendUserMessages($conn, $this->getUserId());
    }

    public function getReceivedMessages(mysqli $conn) {
        return Message::GetAllReceivedUserMessages($conn, $this->getUserId());
    }

    public function numberOfUnreadedMessages(mysqli $conn) {
        return Message::GetNumberOfUnreadedMessages($conn, $this->getUserId());
    }

    public function getAllFriends(mysqli $conn) {

    }

    public function updateUserPassword(mysqli $conn, $oldPassword, $newPassword, $confirmPassword) {
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            return false;
        }

        $getUserQuery = 'SELECT * FROM users WHERE deleted=0 AND id="' . $this->userId . '"';
        $result = $conn->query($getUserQuery);
        if ($result->num_rows == 0) {
            return false;
        }
        $user = $result->fetch_assoc();

        if (!password_verify($oldPassword, $user['password'])) {
            return false;
        }

        if ($newPassword != $confirmPassword) {
            return false;
        }

        $hashedNewPassword = $this->hashPassword($newPassword);
        $updateUserQuery = 'UPDATE users SET password="'.$hashedNewPassword.'",
                            editedUser="'.date('Y-m-d').'" WHERE id="'.$this->userId.'"';
        $result = $conn->query($updateUserQuery) ;

        return $result;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function linkToUser($color = null) {
        if ($color) {
            $color = ' style="color: '.$color.';"';
        }

        if (!empty($this->getUsername())) {
            $link = '<a href="'.ROOT_PATH.'/views/userInfo.php?id='.$this->userId.'"'.$color.'>'.$this->getUsername().'</a>';
        }
        else {
            $link = '<a href="'.ROOT_PATH.'/views/userInfo.php?id='.$this->userId.'"'.$color.'>'.$this->getEmail().'</a>';
        }
        return $link;
    }

    public function findUserByMail(mysqli $conn, $mail) {
        $searUser = 'SELECT * FROM users WHERE deleted=0 AND email="'.$mail.'"';
        $result = $conn->query($searUser);
        $row = $result->fetch_assoc();

        if ($result->num_rows != 1) {
            return false;
        }

        $this->userId = $row['id'];
        $this->setEmail($row['mail']);
        $this->setUsername($row['username']);

        return $result;
    }

    static public function GetAllUsers(mysqli $conn) {
        $allUsers = [];
        $sql = 'SELECT * FROM users WHERE deleted=0';
        $result = $conn->query($sql);
        if ($result->num_rows>0) {
            while ($row = $result->fetch_assoc()) {
                $user = new User();
                $user->userId = $row['id'];
                $user->setEmail($row['email']);
                $user->setUsername($row['username']);
                $allUsers[] = $user;
            }
        }
        return $allUsers;
    }


}