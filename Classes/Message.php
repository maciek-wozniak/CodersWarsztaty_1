<?php

require_once 'allClasses.php';

Class Message {

    private $messageId;
    private $senderId;
    private $receiverId;
    private $sendTime;
    private $messageTitle;
    private $messageText;
    private $readed;

    public function __construct() {
        $this->messageId = -1;
        $this->senderId = -1;
        $this->receiverId = -1;
        $this->sendTime = '';
        $this->messageTitle = '';
        $this->messageText = '';
    }

    public function getMessageId() {
        return $this->messageId;
    }

    public function setReaded($readed) {
        $this->readed = $readed;
    }

    public function getReaded() {
        return $this->readed;
    }

    public function setMessageTitle($messageTitle) {
        $this->messageTitle = $messageTitle;
    }

    public function getMessageTitle() {
        return $this->messageTitle;
    }

    public function setSenderId($senderId) {
        $this->senderId = $senderId;
    }

    public function getSenderId() {
        return $this->senderId;
    }

    public function setReceiverId($receiverId) {
        $this->receiverId = $receiverId;
    }

    public function getReceiverId() {
        return $this->receiverId;
    }

    public function setSendTime($sendTime) {
        $this->sendTime = $sendTime;
    }

    public function getSendTime() {
        return $this->sendTime;
    }

    public function setMessageText($messageText) {
        $this->messageText = $messageText;
    }

    public function getMessageText() {
        return $this->messageText;
    }

    static public function GetAllReceivedUserMessages(mysqli $conn, $userId){
        $allMessages = [];

        $sqlMessages = 'SELECT * FROM messages WHERE receinver_deleted=0 AND receiver_id='.$userId.' ORDER BY send_time DESC';
        $result = $conn->query($sqlMessages);
        if ($result !== false) {
            if ($result->num_rows>0) {
                while ($row = $result->fetch_assoc()) {
                    $message = new Message();
                    $message->messageId = $row['id'];
                    $message->setMessageText($row['message_text']);
                    $message->setMessageTitle($row['title']);
                    $message->setReaded($row['readed']);
                    $message->setSenderId($row['sender_id']);
                    $message->setReceiverId($row['receiver_id']);
                    $message->setSendTime($row['send_time']);
                    $allMessages[] = $message;
                }
            }
        }
        return $allMessages;
    }

    static public function GetAllSendUserMessages(mysqli $conn, $userId){
        $allMessages = [];

        $sqlMessages = 'SELECT * FROM messages WHERE sender_deleted=0 AND sender_id='.$userId.' ORDER BY send_time DESC';
        $result = $conn->query($sqlMessages);
        if ($result !== false) {
            if ($result->num_rows>0) {
                while ($row = $result->fetch_assoc()) {
                    $message = new Message();
                    $message->messageId = $row['id'];
                    $message->setMessageText($row['message_text']);
                    $message->setMessageTitle($row['title']);
                    $message->setReaded($row['readed']);
                    $message->setSenderId($row['sender_id']);
                    $message->setReceiverId($row['receiver_id']);
                    $message->setSendTime($row['send_time']);
                    $allMessages[] = $message;
                }
            }
        }
        return $allMessages;
    }

    static public function GetNumberOfUnreadedMessages(mysqli $conn, $userId) {
        $sqlCount = 'SELECT id FROM messages WHERE readed=0 AND receiver_id='.$userId.' AND receinver_deleted=0 ';
        $result = $conn->query($sqlCount);
        return $result->num_rows;
    }

    static public function sendDoNotAcceptFriendshipMessage($conn, $sender, $receiver) {
        $deletedFriendRequestMsg = new Message();
        $deletedFriendRequestMsg->setSenderId($sender);
        $deletedFriendRequestMsg->setReceiverId($receiver);
        $deletedFriendRequestMsg->setMessageTitle('Propozycja przyjaźni odrzucona');
        $deletedFriendRequestMsg->setMessageText('Użytkownik '.$_SESSION['user']->linkToUser().' odrzucił propozycję przyjaźni');
        $deletedFriendRequestMsg->sendMessage($conn);
        $deletedFriendRequestMsg->senderDeletedMsg($conn);
    }

    static public function sendAcceptFriendshipMessage($conn, $sender, $receiver) {
        $deletedFriendRequestMsg = new Message();
        $deletedFriendRequestMsg->setSenderId($sender);
        $deletedFriendRequestMsg->setReceiverId($receiver);
        $deletedFriendRequestMsg->setMessageTitle('Propozycja przyjaźni zaakceptowana');
        $deletedFriendRequestMsg->setMessageText('Użytkownik '.$_SESSION['user']->linkToUser().' przyjął propozycję przyjaźni');
        $deletedFriendRequestMsg->sendMessage($conn);
        $deletedFriendRequestMsg->senderDeletedMsg($conn);
    }

    static public function findFriendshipPropositionMsg(mysqli $conn, $sender, $receiver) {
        $sql = 'SELECT m.id as id, m.message_text AS msg_text, m.sender_id AS sender, m.receiver_id AS receiver, m.title AS title
                FROM messages m
                JOIN message_that_propose_friendship mp ON m.id=mp.message_id
                JOIN friends f ON f.id=mp.friends_id
                WHERE m.sender_id='.$sender.' AND m.receiver_id='.$receiver.' ';


        $result = $conn->query($sql);
        if ($result->num_rows==1) {
            $row = $result->fetch_assoc();

            $msg = new Message();
            $msg->messageId = $row['id'];
            $msg->setMessageText($row['msg_text']);
            $msg->setSenderId($row['sender']);
            $msg->setReceiverId($row['receiver']);
            $msg->setMessageTitle($row['title']);

            return $msg;
        }
        return false;
    }

    public function sendMessage(mysqli $conn) {
        $sendSql = 'INSERT INTO messages (sender_id, receiver_id, title, message_text, send_time) VALUES
            ("'.$this->getSenderId().'", "'.$this->getReceiverId().'", "'.$this->getMessageTitle().'",
            "'.$conn->real_escape_string($this->getMessageText()).'",  "'.date('Y-m-d  H:i:s').'")';

        $result = $conn->query($sendSql) ;
        $this->messageId = $conn->insert_id;
        return $result;
    }

    public function loadMessageFromDb(mysqli $conn, $id){
        $sqlGetMessage = 'SELECT * FROM messages WHERE id='.$id;
        $result = $conn->query($sqlGetMessage);
        if ($result->num_rows!=1) {
            return false;
        }
        else {
            $dbMessage = $result->fetch_assoc();
            $this->messageId = $dbMessage['id'];
            $this->setSenderId($dbMessage['sender_id']);
            $this->setReceiverId($dbMessage['receiver_id']);
            $this->setMessageTitle($dbMessage['title']);
            $this->setMessageText($dbMessage['message_text']);
            $this->setSendTime($dbMessage['send_time']);
            $this->setReaded($dbMessage['readed']);
        }
        return $result;
    }

    public function receiverReadedMsg(mysqli $conn) {
        if ($_SESSION['user']->getUserId() != $this->getReceiverId()) {
            return false;
        }

        $this->readed = 1;
        $readedSql = 'UPDATE messages SET readed=1 WHERE id='.$this->getMessageId();

        $result = $conn->query($readedSql);
        return $result;
    }

    public function receiverDeletedMsg(mysqli $conn) {
        if ($_SESSION['user']->getUserId() != $this->getReceiverId()) {
            return false;
        }

        // JEżeli jest to propozycja przyjaźni:
        $proposalMsg = $this->isFriendshipProposalNotAcceptedMsg($conn);
        if ($proposalMsg !== false) {
            $deleteFriend = 'DELETE FROM friends WHERE id='.$proposalMsg;
            $deleteFriendMsgRelation = 'DELETE FROM message_that_propose_friendship WHERE message_id='.$this->getMessageId();

            if ($conn->query($deleteFriendMsgRelation) == false) {
                return false;
            }

            if ($conn->query($deleteFriend) == false) {
                return false;
            }

            self::sendDoNotAcceptFriendshipMessage($conn, $this->getReceiverId(), $this->getSenderId());
        }

        $deleteSql = 'UPDATE messages SET receinver_deleted=1 WHERE id='.$this->getMessageId();

        $result = $conn->query($deleteSql);
        return $result;
    }

    public function senderDeletedMsg(mysqli $conn) {
        if ($_SESSION['user']->getUserId() != $this->getSenderId()) {
            return false;
        }

        $deleteSql = 'UPDATE messages SET sender_deleted=1 WHERE id='.$this->getMessageId();

        $result = $conn->query($deleteSql);
        return $result;
    }

    public function isFriendshipProposalNotAcceptedMsg(mysqli $conn) {
        $sql = 'SELECT f.id AS id FROM message_that_propose_friendship mp
                JOIN friends f ON f.id=mp.friends_id
                WHERE f.request_accepted=0
                  AND mp.message_id='.$this->getMessageId();

        $result = $conn->query($sql);
        if ($result->num_rows==1) {
            return $result->fetch_assoc()['id'];
        }
        return false;
    }
}