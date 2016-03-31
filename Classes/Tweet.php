<?php

require_once 'allClasses.php';

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', 'http://' . $_SERVER['HTTP_HOST'] . '/CodersWarsztaty_1');
}


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

    static public function GetAllUserTweets(mysqli $conn, $userId){
        $allTweets = [];
        $sqlTweets = 'SELECT * FROM tweets WHERE deleted=0 AND author_id='.$userId;
        $result = $conn->query($sqlTweets);

        if ($result !== false) {
            if ($result->num_rows>0) {
                while ($row = $result->fetch_assoc()) {
                    $tweet = new Tweet();
                    $tweet->tweetId = $row['id'];
                    $tweet->setAuthorId($row['author_id']);
                    $tweet->setTweetText($row['tweet_text']);
                    $tweet->setCreateDate($row['created']);
                    $allTweets[] = $tweet;
                }
            }
        }

        return $allTweets;
    }

    public function loadTweetFromDb(mysqli $conn, $id){

        $sqlGetTweet = 'SELECT * FROM tweets WHERE deleted=0 AND id='.$id;
        $result = $conn->query($sqlGetTweet);
        if ($result->num_rows!=1) {
            return false;
        }
        else {
            $dbTweet = $result->fetch_assoc();
            $this->tweetId = $dbTweet['id'];
            $this->setAuthorId($dbTweet['author_id']);
            $this->setTweetText($dbTweet['tweet_text']);
            $this->setCreateDate($dbTweet['created']);
            $this->setIsDeleted(0);
        }
        return $result;
    }

    public function createTweetAndAddToDb(mysqli $conn) {
        if (!is_numeric($this->authorId) || !($this->authorId>0) || !(strlen($this->tweetText)>0)) {
            return false;
        }

        $addTweetSql = 'INSERT INTO tweets (author_id, tweet_text, created)
                          VALUES ("'.$this->authorId.'", "'.$this->tweetText.'", "'.date('Y-m-d  H:i:s').'")';
        $result = $conn->query($addTweetSql);
        return $result;
    }

    public function updateTweet(mysqli $conn) {
        if (!is_numeric($this->authorId) || !($this->authorId>0) || !(strlen($this->tweetText)>0)) {
            return false;
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']->getUserId() != $this->authorId) {
            return false;
        }

        $updateTweetSql = 'UPDATE tweets SET tweet_text="'.$this->tweetText.'",
                            updated="'.date('Y-m-d').'" WHERE id='.$this->getTweetId();
        $result = $conn->query($updateTweetSql);
        return $result;
    }

    public function showTweet(mysqli $conn) {
        if (!isset($_SESSION)) {
            return false;
        }
        $author = new User($this->authorId);
        $author->loadUserFromDb($conn, $this->authorId);
        $tweetAuthorLink = $author->linkToUser('white');

        $editLink = '';
        $deleteLink = '';
        $commentsLink = '';
        $commentsLink = '<a href="'.ROOT_PATH.'/views/tweetComments.php?id=' . $this->getTweetId() . '" style="color:white;">Komentarzy: [' . $this->numberOfComments($conn) . ']</a>';

        if ($this->authorId == $_SESSION['user']->getUserId()) {
            $editLink = '<a class="btn btn-xs btn-primary" href="'.ROOT_PATH.'/index.php?editTweet='.$this->getTweetId().'">Edytuj</a>';
            $deleteLink = '<a class="btn btn-xs btn-primary" href="'.ROOT_PATH.'/index.php?deleteTweet='.$this->getTweetId().'">Usuń</a>';
        }
        $tweetDate = $this->getCreateDate();

        echo '<div class="panel panel-primary" style="width: 500px;margin: 0 auto;">';
            echo '<div class="panel-heading">Tweet '.$tweetAuthorLink.' z '.substr($tweetDate,0,strlen($tweetDate)-3).' '
                    .$editLink.' '.$deleteLink.' '.$commentsLink.'</div>';
            echo '<div class="panel-body">'.$this->tweetText.'</div>';
        echo '</div>';
    }

    public function deleteTweet(mysqli $conn) {
        if (!isset($_SESSION['user']) || $_SESSION['user']->getUserId() != $this->authorId) {
            return false;
        }

        $updateTweetSql = 'UPDATE tweets SET deleted=1,
                            updated="'.date('Y-m-d').'" WHERE id='.$this->getTweetId();
        $result = $conn->query($updateTweetSql);
        return $result;
    }

    public function getAllComments(mysqli $conn) {
        TweetComment::GetAllTweetComments($conn, $this->getTweetId());
    }

    public function numberOfComments(mysqli $conn) {
        TweetComment::GetNumberOfTweetComments($conn, $this->getTweetId());
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