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

    public function getReaded() {
        return $this->readed;
    }

    public function setReaded($readed) {
        $this->readed = $readed;
    }

    public function getMessageTitle() {
        return $this->messageTitle;
    }

    public function setMessageTitle($messageTitle) {
        $this->messageTitle = $messageTitle;
    }

    public function getSenderId() {
        return $this->senderId;
    }

    public function setSenderId($senderId) {
        $this->senderId = $senderId;
    }

    public function getReceiverId() {
        return $this->receiverId;
    }

    public function setReceiverId($receiverId) {
        $this->receiverId = $receiverId;
    }

    public function getSendTime() {
        return $this->sendTime;
    }

    public function setSendTime($sendTime) {
        $this->sendTime = $sendTime;
    }

    public function getMessageText() {
        return $this->messageText;
    }

    public function setMessageText($messageText) {
        $this->messageText = $messageText;
    }

    public function sendMessage(mysqli $conn) {
        $sendSql = 'INSERT INTO messages (sender_id, receiver_id, title, message_text, send_time) VALUES
            ("'.$this->getSenderId().'", "'.$this->getReceiverId().'", "'.$this->getMessageTitle().'", "'.$this->getMessageText().'",  "'.date('Y-m-d  H:i:s').'")';

        $result = $conn->query($sendSql);
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

}