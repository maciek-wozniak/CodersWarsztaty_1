<?php

require_once 'allClasses.php';

Class User {
    private $userId;
    private $salt;
    private $email;
    private $username;

    static public $frienshipMsgProposalTitile = 'Nowa propozycja przyjaźni';

    public function __construct() {
        $this->userId = -1;
        $this->email = '';
        $this->username = '';
        $this->salt = '';
    }

    public function getUserId() {
        return $this->userId;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getEmail() {
        return $this->email;
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
        $this->salt = $user['salt'];

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
        unset($this->salt);

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

        $myFriends = $this->getAllFriends($conn);
        foreach ($myFriends as $friend) {
            $this->deleteFriend($conn, $friend->getUserId());
        }

        $getUserQuery = 'UPDATE users SET editedUser="'.date('Y-m-d').'", deleted=1 WHERE id="' . $this->userId . '"';
        $result = $conn->query($getUserQuery) or die($conn->error);

        unset($_SESSION['user']);
        return $result;
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

    public function proposeFriendship(mysqli $conn, $userId){
        if ($this->isThereFriendshipRequest($conn, $userId) != 0) {
            return false;
        }

        $sqlProposeFriendship = 'INSERT INTO friends (inviting_user_id, friend_user_id) VALUES
                                  ('.$this->getUserId().', '.$userId.')';
        $result = $conn->query($sqlProposeFriendship);
        if ($result === true) {
            $proposalId = $conn->insert_id;
            $msgText = $this->createProposalFriendshipMsgText($conn);
            $newFriendMsg = new Message();
            $newFriendMsg->setSenderId($this->getUserId());
            $newFriendMsg->setReceiverId($userId);
            $newFriendMsg->setMessageTitle(self::$frienshipMsgProposalTitile);
            $newFriendMsg->setMessageText($msgText);
            if ($newFriendMsg->sendMessage($conn) === true) {
                $newFriendMsg->senderDeletedMsg($conn);

                $sqlMsgFriendsRelation = 'INSERT INTO message_that_propose_friendship (message_id, friends_id)
                            VALUES ('.$newFriendMsg->getMessageId().', '.$proposalId.')';
                if ($conn->query($sqlMsgFriendsRelation) == true) {
                    return true;
                }
            }
        }
        return false;
    }

    public function acceptFriendship(mysqli $conn, $myFriendId) {
        Message::sendAcceptFriendshipMessage($conn, $this->getUserId(), $myFriendId);
        $msg = $this->findMyProposalFriendshipMsgWith($conn, $myFriendId);

        $sql = 'DELETE FROM message_that_propose_friendship WHERE message_id='.$msg->getMessageId().' ';
        if ($conn->query($sql) != true) {
            return false;
        }

        $sql = 'UPDATE friends SET request_accepted=1 WHERE inviting_user_id='.$myFriendId.' AND friend_user_id='.$this->getUserId().' ';
        if ($conn->query($sql) != true) {
            return false;
        }
        if ($msg->receiverDeletedMsg($conn) ) {
            return true;
        }

        return false;
    }

    public function doNotAcceptFriendship(mysqli $conn, $notMyFriendId) {
        Message::sendDoNotAcceptFriendshipMessage($conn, $this->getUserId(), $notMyFriendId);
        $msg = $this->findMyProposalFriendshipMsgWith($conn, $notMyFriendId);

        $sql = 'DELETE FROM message_that_propose_friendship WHERE message_id='.$msg->getMessageId().' ';
        if ($conn->query($sql) != true) {
            return false;
        }

        $sql = 'DELETE FROM friends WHERE inviting_user_id='.$notMyFriendId.' AND friend_user_id='.$this->getUserId().' ';
        if ($conn->query($sql) != true) {
            return false;
        }

        if ($msg->receiverDeletedMsg($conn) ) {
            return true;
        }

        return false;
    }

    public function areWeFriends(mysqli $conn, $userId) {
        $sql = 'SELECT * FROM friends WHERE request_accepted=1 AND ((inviting_user_id='.$this->getUserId().' AND friend_user_id='.$userId.')
                  OR (friend_user_id='.$this->getUserId().' AND inviting_user_id='.$userId.'))';
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            return true;
        }
        return false;
    }

    public function isThereFriendshipRequest(mysqli $conn, $userId) {
        $sql = 'SELECT * FROM friends WHERE request_accepted=0 AND ((inviting_user_id='.$this->getUserId().' AND friend_user_id='.$userId.')
                  OR (friend_user_id='.$this->getUserId().' AND inviting_user_id='.$userId.'))';
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            return true;
        }
        return false;
    }

    public function didIProposeFriendship(mysqli $conn, $userId) {
        $sql = 'SELECT inviting_user_id FROM friends WHERE (inviting_user_id='.$this->getUserId().' AND friend_user_id='.$userId.')';
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            return true;
        }
        return false;
    }

    public function getAllFriends(mysqli $conn) {
        $allFriends = [];
        $sqlFriends = 'SELECT *
                        FROM friends f
                        JOIN users u ON u.id=f.inviting_user_id
                        WHERE request_accepted=1 AND friend_user_id='.$this->getUserId();

        $result = $conn->query($sqlFriends);
        if ($result->num_rows>0) {
            while ($row = $result->fetch_assoc()) {
                $friend = new User();
                $friend->userId = $row['id'];
                $friend->setUsername($row['username']);
                $friend->setEmail($row['email']);
                $allFriends[] = $friend;
            }
        }

        $sqlFriends = 'SELECT *
                        FROM friends f
                        JOIN users u ON u.id=f.friend_user_id
                        WHERE request_accepted=1 AND inviting_user_id='.$this->getUserId();

        $result = $conn->query($sqlFriends);
        if ($result->num_rows>0) {
            while ($row = $result->fetch_assoc()) {
                $friend = new User();
                $friend->userId = $row['id'];
                $friend->setUsername($row['username']);
                $friend->setEmail($row['email']);
                $allFriends[] = $friend;
            }
        }

        return $allFriends;
    }

    public function deleteFriend(mysqli $conn, $userId) {
        $sql = 'DELETE FROM friends WHERE (inviting_user_id='.$userId.' AND friend_user_id='.$this->getUserId().') OR
                                            (inviting_user_id='.$this->getUserId().' AND friend_user_id='.$userId.') ';
        $result = $conn->query($sql);
        if ($result == true) {
            return true;
        }
        return false;
    }

    public function createProposalFriendshipMsgText(mysqli $conn) {
        return $conn->real_escape_string( 'Użytkownik '.$this->linkToUser().' zaproponował Ci przyjaźć. <a href="userInfo.php?id='.$this->getUserId().'&acceptFriendship=yes">Zaakceptuj</a> lub <a href="userInfo.php?id='.$this->getUserId().'&dontAcceptFriendship=yes">odrzuć</a>');
    }

    public function findMyProposalFriendshipMsgWith(mysqli $conn, $userId) {
        return Message::findFriendshipPropositionMsg($conn, $userId, $this->getUserId());
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

}