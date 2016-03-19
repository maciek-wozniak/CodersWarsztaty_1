<?php

Class Tweet {
    private $tweetId;
    private $authorId;
    private $createDate;
    private $tweetText;
    private $isDeleted;


    public function __construct() {
        $this->tweetId = -1;
        $this->authorId = 0;
        $this->tweetText = '';
        $this->isDeleted = 0;
    }

    public function loadTweetFromDb($id){
        $dbConnection = DbConnection::getConnection();
        $sqlGetTweet = 'SELECT * FROM tweets WHERE deleted=0 AND id='.$id;
        $result = $dbConnection->query($sqlGetTweet);
        if ($result->num_rows!=1) {
            return null;
        }
        else {
            $dbTweet = $result->fetch_assoc();
            $this->tweetId = $dbTweet['id'];
            $this->setAuthorId($dbTweet['author_id']);
            $this->setTweetText($dbTweet['text']);
            $this->setCreateDate($dbTweet['created']);
            $this->setIsDeleted(0);
        }
        $dbConnection->close();
        $dbConnection=null;
    }

    public function createTweetAndAddToDb() {
        if (!is_numeric($this->authorId) || !($this->authorId>0) || !(strlen($this->tweetText)>0)) {
            return false;
        }

        $dbConnection = DbConnection::getConnection();
        $addTweetSql = 'INSERT INTO tweets (author_id, text, created)
                          VALUES ("'.$this->authorId.'", "'.$this->tweetText.'", "'.date('Y-m-d').'")';
        $result = $dbConnection->query($addTweetSql);
        $dbConnection->close();
        $dbConnection=null;
        return $result;
    }

    public function updateTweet() {
        if (!is_numeric($this->authorId) || !($this->authorId>0) || !(strlen($this->tweetText)>0)) {
            return false;
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']->getId() != $this->authorId) {
            return false;
        }

        $dbConnection = DbConnection::getConnection();
        $updateTweetSql = 'UPDATE tweets SET text="'.$this->tweetText.'",
                            updated="'.date('Y-m-d').'" ';
        $result = $dbConnection->query($updateTweetSql);
        $dbConnection->close();
        $dbConnection=null;
        return $result;
    }

    public function showTweet() {
        if (!isset($_SESSION)) {
            return false;
        }

        $editLink = '';
        if ($this->authorId == $_SESSION['user']->getId()) {
            $editLink = '<a class="btn btn-xs btn-primary" href="index.php?editTweet='.$this->getTweetId().'">Edytuj</a>';
        }

        echo '<div class="panel panel-primary">';
            echo '<div class="panel-heading">Tweet z '.$this->createDate.' '.$editLink.'</div>';
            echo '<div class="panel-body">'.$this->tweetText.'</div>';
        echo '</div>';
    }

    public function deleteTweet() {
        if (!isset($_SESSION['user']) || $_SESSION['user']->getId() != $this->authorId) {
            return false;
        }

        $dbConnection = DbConnection::getConnection();
        $updateTweetSql = 'UPDATE tweets SET deleted=1,
                            updated="'.date('Y-m-d').'" ';
        $result = $dbConnection->query($updateTweetSql);
        $dbConnection->close();
        $dbConnection=null;
        return $result;
    }

    public function getAllComments() {

    }

    public function getTweetId() {
        return $this->tweetId;
    }

    public function getAuthorId() {
        return $this->authorId;
    }

    public function setAuthorId($authorId) {
        if (is_numeric($authorId) && $authorId > 0) {
            $this->authorId = $authorId;
        }
    }

    public function getTweetText() {
        return $this->tweetText;
    }

    public function setTweetText($tweetText) {
        if (strlen($tweetText) > 0) {
            $this->tweetText = $tweetText;
        }
    }

    public function getIsDeleted() {
        return $this->isDeleted;
    }

    public function setIsDeleted($isDeleted) {
        $this->isDeleted = $isDeleted;
    }

    public function getCreateDate() {
        return $this->createDate;
    }

    public function setCreateDate($createDate) {
        $this->createDate = $createDate;
    }



}