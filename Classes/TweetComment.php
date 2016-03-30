<?php
include_once dirname(__FILE__).'/DbConnection.php';
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', 'http://' . $_SERVER['HTTP_HOST'] . '/CodersWarsztaty_1');
}

Class TweetComment {
    private $id;
    private $tweetId;
    private $authorId;
    private $commentText;
    private $creationDate;


    public function __construct() {
        $this->id = -1;
        $this->tweetId = -1;
        $this->authorId = -1;
        $this->commentText = '';
        $this->creationDate = '';
    }

    public function getId() {
        return $this->id;
    }

    public function getTweetId() {
        return $this->tweetId;
    }

    public function setTweetId($tweetId) {
        $this->tweetId = $tweetId;
    }

    public function getAuthorId() {
        return $this->authorId;
    }

    public function setAuthorId($authorId) {
        $this->authorId = $authorId;
    }

    public function getCommentText() {
        return $this->commentText;
    }

    public function setCommentText($commentText) {
        $this->commentText = $commentText;
    }

    public function getCreationDate() {
        return $this->creationDate;
    }

    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    public function loadCommentFromDb($id){
        $dbConnection = DbConnection::getConnection();
        $sqlGetComment = 'SELECT * FROM tweet_comments WHERE deleted=0 AND id='.$id;
        $result = $dbConnection->query($sqlGetComment);
        if ($result->num_rows!=1) {
            return null;
        }
        else {
            $dbComment = $result->fetch_assoc();
            $this->id = $dbComment['id'];
            $this->tweetId = $dbComment['tweet_id'];
            $this->authorId = $dbComment['author_id'];
            $this->creationDate = $dbComment['creation_date'];
            $this->commentText = $dbComment['text'];
        }
        $dbConnection->close();
        $dbConnection=null;
    }

    public function createCommentAndAddToDb() {
        if (!is_numeric($this->tweetId) || !($this->tweetId>0) || !(strlen($this->commentText)>0) ||
            !is_numeric($this->authorId) || !($this->authorId>0)) {
            return false;
        }

        $dbConnection = DbConnection::getConnection();
        $addCommentSql = 'INSERT INTO tweet_comments
                          (tweet_id, author_id, text, creation_date)
                          VALUES ("'.$this->tweetId.'", "'.$this->authorId.'", "'.$this->commentText.'", "'.date('Y-m-d  H:i:s').'")';
        $result = $dbConnection->query($addCommentSql);
        $dbConnection->close();
        $dbConnection=null;
        return $result;
    }

    public function updateComment() {
        if (!is_numeric($this->tweetId) || !($this->tweetId>0) || !(strlen($this->commentText)>0) ||
            !is_numeric($this->authorId) || !($this->authorId>0)) {
            return false;
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']->getId() != $this->authorId) {
            return false;
        }

        $dbConnection = DbConnection::getConnection();
        $updateCommentSql = 'UPDATE tweet_comments SET text="'.$this->commentText.'"
                            WHERE id='.$this->getId();
        $result = $dbConnection->query($updateCommentSql);
        $dbConnection->close();
        $dbConnection=null;
        return $result;
    }

    public function deleteComment() {
        if (!isset($_SESSION['user']) || $_SESSION['user']->getId() != $this->authorId) {
            return false;
        }

        $dbConnection = DbConnection::getConnection();
        $updateCommentSql = 'UPDATE tweet_comments SET deleted=1  WHERE id='.$this->getId();
        $result = $dbConnection->query($updateCommentSql);
        $dbConnection->close();
        $dbConnection=null;
        return $result;
    }

    public function showComment() {
        if (!isset($_SESSION)) {
            return false;
        }
        $author = new User($this->authorId);
        $author->loadUserFromDb($this->authorId);
        $commentAuthor = $author->linkToUser();

        $editLink = '';
        $deleteLink = '';
        if ($this->authorId == $_SESSION['user']->getId()) {
            $editLink = '<a class="btn btn-xs btn-info" href="tweetComments.php?id='.$this->tweetId.'&editComment='.$this->getId().'">Edytuj</a>';
            $deleteLink = '<a class="btn btn-xs btn-info" href="tweetComments.php?id='.$this->tweetId.'&deleteComment='.$this->getId().'">Usuń</a>';
        }
        $commenttDate = $this->getCreationDate();

        echo '<div class="panel panel-info">';
        echo '<div class="panel-heading">Komentarz ' .$commentAuthor. ' z '
                .substr($commenttDate,0,strlen($commenttDate)-3).' '.$editLink.' '.$deleteLink.'</div>';
        echo '<div class="panel-body">'.$this->commentText.'</div>';
        echo '</div>';
    }

}