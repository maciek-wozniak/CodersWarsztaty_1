<?php

include_once dirname(__FILE__).'/DbConnection.php';
include_once dirname(__FILE__).'/Tweet.php';
include_once dirname(__FILE__).'/Message.php';

Class User {
    private $userId;
    private $password;
    private $salt;
    public $email;
    public $username;

    public function __construct($id = NULL) {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!empty($_SESSION['user']) && $id == NULL) {
            $user = $_SESSION['user'];
            $this->userId = $user['id'];
            $this->email = $user['email'];
            $this->username = $user['username'];
            $this->salt = $user['salt'];
        }
        else if ($id != NULL) {
            $this->userId = -1;
            $this->email = '';
            $this->username = '';
        }
    }

    public function loadUserFromDb($id) {
        $sqlUser = 'SELECT * FROM users WHERE deleted=0 AND id='.$id;
        $conn = DbConnection::getConnection();
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
        $sqlIsUser = 'SELECT * FROM users WHERE deleted=0 AND email="'.$mail.'" ';
        $result = $connection->query($sqlIsUser);
        if ($result->num_rows == 1 && !($this->email == $mail)) {
            echo 'Użytkownik o takim mailu już istnieje<br>';
            return false;
        }

        $connection = DbConnection::getConnection();
        $sqlIsUser = 'SELECT * FROM users WHERE deleted=0 AND (email="'.$mail.'" OR id="'.$this->userId.'") ';
        $result = $connection->query($sqlIsUser);
        if ($result->num_rows != 1) {
            return false;
        }

        $updateUserQuery = 'UPDATE users SET username="'.$name.'", email="'.$mail.'",
                        editedUser="'.date('Y-m-d').'" WHERE id="'.$this->userId.'"';
        $result = $connection->query($updateUserQuery);

        if ($result) {
            $getUserQuery = 'SELECT * FROM users WHERE id="' . $this->userId . '" AND deleted=0';
            $resultUser = $connection->query($getUserQuery);
            if ($resultUser->num_rows == 0) {
                return false;
            }
            $user = $resultUser->fetch_assoc();
            unset($_SESSION['user']);
            $_SESSION['user'] = $user;
            $loggedUser = new User();
            unset($_SESSION['user']);
            $_SESSION['user'] = $loggedUser;
        }

        $connection->close();
        $connection=null;
        return $result;
    }

    public function deleteUser($password) {
        $conn = DbConnection::getConnection();

        $getUserQuery = 'SELECT * FROM users WHERE deleted=0 AND id="' . $this->userId . '"';
        $result = $conn->query($getUserQuery);
        if ($result->num_rows == 0) {
            return false;
        }
        $user = $result->fetch_assoc();
        $this->salt = $user['salt'];

        $hashedOldPassword = $this->hashPassword($password);
        if ($hashedOldPassword != $user['password']) {
            return false;
        }

        $sentMsq = $this->getSentMessages();
        foreach ($sentMsq as $msg) {
            $msg->senderDeletedMsg();
        }

        $receivedMsg = $this->getReceivedMessages();
        foreach ($receivedMsg as $msg) {
            $msg->receiverDeletedMsg();
        }

        $myComments = $this->getAllMyComments();
        foreach ($myComments as $comment) {
            $comment->deleteComment();
        }

        $myTweets = $this->getAllMyTweets();
        foreach ($myTweets as $deleteMyTweet) {
            $deleteMyTweet->deleteTweet();
        }

        $getUserQuery = 'UPDATE users SET editedUser="'.date('Y-m-d').'", deleted=1 WHERE id="' . $this->userId . '"';
        $result = $conn->query($getUserQuery) or die($conn->error);

        unset($_SESSION['user']);
        $conn->close();
        $conn=null;

        /*
         *
         *  jeszcze delete friends
         */
        return $result;
    }

    public function getAllMyTweets() {
        $myTweets = array();

        $dbConnection = DbConnection::getConnection();
        $getTweets = 'SELECT id FROM tweets WHERE deleted=0 AND author_id=' .$this->userId .' ORDER BY id DESC';
        $result = $dbConnection->query($getTweets);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $myTweet = new Tweet();
                $myTweet->loadTweetFromDb($row['id']);
                $myTweets[$myTweet->getTweetId()] = $myTweet;
            }
        }
        $dbConnection->close();
        $dbConnection=null;
        return $myTweets;
    }

    public function getAllMyComments() {
        $comments = [];
        $dbConnection = DbConnection::getConnection();
        $getCommentsSql = 'SELECT * FROM tweet_comments WHERE deleted=0 AND author_id='.$this->getUserId();
        $result = $dbConnection->query($getCommentsSql);

        while ($row = $result->fetch_assoc()) {
            $comment = new TweetComment();
            $comment->loadCommentFromDb($row['id']);
            $comments[$comment->getCommentId()] = $comment;
        }

        $dbConnection->close();
        $dbConnection=null;
        return $comments;
    }

    public function getSentMessages() {
        $messages = [];
        $dbConnection = DbConnection::getConnection();
        $getMessagesSentSql = 'SELECT * FROM messages WHERE sender_deleted=0 AND sender_id='.$this->getUserId();
        $result = $dbConnection->query($getMessagesSentSql);

        while ($row = $result->fetch_assoc()) {
            $sentMsg = new Message();
            $sentMsg->loadMessageFromDb($row['id']);
            $messages[$sentMsg->getMessageId()] = $sentMsg;
        }

        $dbConnection->close();
        $dbConnection=null;
        return $messages;
    }

    public function getReceivedMessages() {
        $messages = [];
        $dbConnection = DbConnection::getConnection();
        $getMessagesReceivedSql = 'SELECT * FROM messages WHERE receinver_deleted=0 AND receiver_id='.$this->getUserId();
        $result = $dbConnection->query($getMessagesReceivedSql);

        while ($row = $result->fetch_assoc())  {
            $receivedMsg = new Message();
            $receivedMsg->loadMessageFromDb($row['id']);
            $messages[$receivedMsg->getMessageId()] = $receivedMsg;
        }

        $dbConnection->close();
        $dbConnection=null;
        return $messages;

    }

    public function getAllFriends() {

    }

    public function updateUserPassword($oldPassword, $newPassword, $confirmPassword) {
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            return false;
        }

        $conn = DbConnection::getConnection();
        $getUserQuery = 'SELECT * FROM users WHERE deleted=0 AND id="' . $this->userId . '"';
        $result = $conn->query($getUserQuery);
        if ($result->num_rows == 0) {
            return false;
        }
        $user = $result->fetch_assoc();
        $this->salt = $user['salt'];

        $hashedOldPassword = $this->hashPassword($oldPassword);
        if ($hashedOldPassword != $user['password']) {
            return false;
        }

        if ($newPassword != $confirmPassword) {
            return false;
        }

        $hashedNewPassword = $this->hashPassword($newPassword);
        $updateUserQuery = 'UPDATE users SET password="'.$hashedNewPassword.'",
                            editedUser="'.date('Y-m-d').'" WHERE id="'.$this->userId.'"';
        $result = $conn->query($updateUserQuery) or die ($conn->error.'<br>'.$updateUserQuery);

        $conn->close();
        $conn=null;
        return $result;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function linkToUser($color = null) {
        $link = '';
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

    public function findUserByMail($mail) {
        $conn = DbConnection::getConnection();
        $searUser = 'SELECT * FROM users WHERE deleted=0 AND email="'.$mail.'"';
        $result = $conn->query($searUser);
        $row = $result->fetch_assoc();

        if ($result->num_rows != 1) {
            return false;
        }

        $this->userId = $row['id'];
        $this->setEmail($row['mail']);
        $this->setUsername($row['username']);

        $conn->close();
        $conn=null;
        return $result;
    }

}