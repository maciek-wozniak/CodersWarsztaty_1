<?php

require_once 'allClasses.php';

Class TweetComment {
    private $commentId;
    private $tweetId;
    private $authorId;
    private $commentText;
    private $creationDate;


    public function __construct() {
        $this->commentId = -1;
        $this->tweetId = -1;
        $this->authorId = -1;
        $this->commentText = '';
        $this->creationDate = '';
    }

    public function getCommentId() {
        return $this->commentId;
    }

    public function setTweetId($tweetId) {
        $this->tweetId = $tweetId;
    }

    public function getTweetId() {
        return $this->tweetId;
    }

    public function setAuthorId($authorId) {
        $this->authorId = $authorId;
    }

    public function getAuthorId() {
        return $this->authorId;
    }

    public function setCommentText($commentText) {
        $this->commentText = $commentText;
    }

    public function getCommentText() {
        return $this->commentText;
    }

    public function setCreationDate($creationDate) {
        $this->creationDate = $creationDate;
    }

    public function getCreationDate() {
        return $this->creationDate;
    }

    static public function GetNumberOfTweetComments(mysqli $conn, $tweetId) {
        $sqlCount = 'SELECT id FROM tweet_comments WHERE deleted=0 AND tweet_id='.$tweetId;
        $result = $conn->query($sqlCount);
        return $result->num_rows;
    }

    static public function GetAllTweetComments(mysqli $conn, $tweetId) {
        $comments = [];
        $getCommentsSql = 'SELECT * FROM tweet_comments WHERE deleted=0 AND tweet_id='.$tweetId.' ORDER BY creation_date DESC';
        $result = $conn->query($getCommentsSql);

        while ($row = $result->fetch_assoc()) {
            $comment = new TweetComment();
            $comment->commentId = $row['id'];
            $comment->setAuthorId($row['author_id']);
            $comment->setTweetId($row['tweet_id']);
            $comment->setCommentText($row['comment_text']);
            $comment->setCreationDate($row['creation_date']);
            $comments[] = $comment;
        }

        return $comments;
    }

    static public function GetAllUserComments(mysqli $conn, $userId) {
        $comments = [];
        $getCommentsSql = 'SELECT * FROM tweet_comments WHERE deleted=0 AND author_id='.$userId;
        $result = $conn->query($getCommentsSql);

        while ($row = $result->fetch_assoc()) {
            $comment = new TweetComment();
            $comment->commentId = $row['id'];
            $comment->setAuthorId($row['author_id']);
            $comment->setTweetId($row['tweet_id']);
            $comment->setCommentText($row['comment_text']);
            $comment->setCreationDate($row['creation_date']);
            $comments[] = $comment;
        }

        return $comments;
    }

    public function createCommentAndAddToDb(mysqli $conn) {
        if (!is_numeric($this->tweetId) || !($this->tweetId>0) || !(strlen($this->commentText)>0) ||
            !is_numeric($this->authorId) || !($this->authorId>0)) {
            return false;
        }

        $addCommentSql = 'INSERT INTO tweet_comments
                          (tweet_id, author_id, comment_text, creation_date)
                          VALUES ("'.$this->tweetId.'", "'.$this->authorId.'", "'.$this->commentText.'", "'.date('Y-m-d  H:i:s').'")';
        $result = $conn->query($addCommentSql);
        return $result;
    }

    public function loadCommentFromDb(mysqli $conn, $id){
        $sqlGetComment = 'SELECT * FROM tweet_comments WHERE deleted=0 AND id='.$id;
        $result = $conn->query($sqlGetComment);
        if ($result->num_rows!=1) {
            return null;
        }
        else {
            $dbComment = $result->fetch_assoc();
            $this->commentId = $dbComment['id'];
            $this->tweetId = $dbComment['tweet_id'];
            $this->authorId = $dbComment['author_id'];
            $this->creationDate = $dbComment['creation_date'];
            $this->commentText = $dbComment['comment_text'];
        }
    }

    public function updateComment(mysqli $conn) {
        if (!is_numeric($this->tweetId) || !($this->tweetId>0) || !(strlen($this->commentText)>0) ||
            !is_numeric($this->authorId) || !($this->authorId>0)) {
            return false;
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']->getUserId() != $this->authorId) {
            return false;
        }

        $updateCommentSql = 'UPDATE tweet_comments SET comment_text="'.$this->commentText.'"
                            WHERE id='.$this->getCommentId();
        $result = $conn->query($updateCommentSql);
        return $result;
    }

    public function deleteComment(mysqli $conn) {
        if (!isset($_SESSION['user']) || $_SESSION['user']->getUserId() != $this->authorId) {
            return false;
        }

        $updateCommentSql = 'UPDATE tweet_comments SET deleted=1  WHERE id='.$this->getCommentId();
        $result = $conn->query($updateCommentSql);
        return $result;
    }

    public function showComment(mysqli $conn) {
        if (!isset($_SESSION)) {
            return false;
        }
        $author = new User($this->authorId);
        $author->loadUserFromDb($conn, $this->authorId);
        $commentAuthor = $author->linkToUser();

        $editLink = '';
        $deleteLink = '';
        if ($this->authorId == $_SESSION['user']->getUserId()) {
            $editLink = '<a class="btn btn-xs btn-info" href="tweetComments.php?id='.$this->tweetId.'&editComment='.$this->getCommentId().'">Edytuj</a>';
            $deleteLink = '<a class="btn btn-xs btn-info" href="tweetComments.php?id='.$this->tweetId.'&deleteComment='.$this->getCommentId().'">Usuń</a>';
        }
        $commenttDate = $this->getCreationDate();

        echo '<div class="panel panel-info" style="width: 500px;margin: 0 auto;">';
        echo '<div class="panel-heading">Komentarz ' .$commentAuthor. ' z '
                .substr($commenttDate,0,strlen($commenttDate)-3).' '.$editLink.' '.$deleteLink.'</div>';
        echo '<div class="panel-body">'.nl2br($this->commentText).'</div>';
        echo '</div>';
    }
}