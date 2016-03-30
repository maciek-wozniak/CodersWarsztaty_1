<?php
include_once dirname(__FILE__).'/DbConnection.php';
include_once dirname(__FILE__).'/User.php';
include_once dirname(__FILE__).'/TweetComment.php';
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

    public function loadTweetFromDb($id){
        $dbConnection = DbConnection::getConnection();
        $sqlGetTweet = 'SELECT * FROM tweets WHERE deleted=0 AND id='.$id;
        $result = $dbConnection->query($sqlGetTweet);
        if ($result->num_rows!=1) {
            return false;
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
        return $result;
    }

    public function createTweetAndAddToDb() {
        if (!is_numeric($this->authorId) || !($this->authorId>0) || !(strlen($this->tweetText)>0)) {
            return false;
        }

        $dbConnection = DbConnection::getConnection();
        $addTweetSql = 'INSERT INTO tweets (author_id, text, created)
                          VALUES ("'.$this->authorId.'", "'.$this->tweetText.'", "'.date('Y-m-d  H:i:s').'")';
        $result = $dbConnection->query($addTweetSql);
        $dbConnection->close();
        $dbConnection=null;
        return $result;
    }

    public function updateTweet() {
        if (!is_numeric($this->authorId) || !($this->authorId>0) || !(strlen($this->tweetText)>0)) {
            return false;
        }

        if (!isset($_SESSION['user']) || $_SESSION['user']->getUserId() != $this->authorId) {
            return false;
        }

        $dbConnection = DbConnection::getConnection();
        $updateTweetSql = 'UPDATE tweets SET text="'.$this->tweetText.'",
                            updated="'.date('Y-m-d').'" WHERE id='.$this->getTweetId();
        $result = $dbConnection->query($updateTweetSql);
        $dbConnection->close();
        $dbConnection=null;
        return $result;
    }

    public function showTweet() {
        if (!isset($_SESSION)) {
            return false;
        }
        $author = new User($this->authorId);
        $author->loadUserFromDb($this->authorId);
        $tweetAuthorLink = $author->linkToUser('white');

        $editLink = '';
        $deleteLink = '';
        $commentsLink = '';
        $commentsLink = '<a href="'.ROOT_PATH.'/views/tweetComments.php?id=' . $this->tweetId . '" style="color:white;">Komentarzy: [' . $this->numberOfComments() . ']</a>';

        if ($this->authorId == $_SESSION['user']->getUserId()) {
            $editLink = '<a class="btn btn-xs btn-primary" href="'.ROOT_PATH.'/index.php?editTweet='.$this->getTweetId().'">Edytuj</a>';
            $deleteLink = '<a class="btn btn-xs btn-primary" href="'.ROOT_PATH.'/index.php?deleteTweet='.$this->getTweetId().'">Usuń</a>';
        }
        $tweetDate = $this->getCreateDate();

        echo '<div class="panel panel-primary">';
            echo '<div class="panel-heading">Tweet '.$tweetAuthorLink.' z '.substr($tweetDate,0,strlen($tweetDate)-3).' '
                    .$editLink.' '.$deleteLink.' '.$commentsLink.'</div>';
            echo '<div class="panel-body">'.$this->tweetText.'</div>';
        echo '</div>';
    }

    public function deleteTweet() {
        if (!isset($_SESSION['user']) || $_SESSION['user']->getUserId() != $this->authorId) {
            return false;
        }

        $dbConnection = DbConnection::getConnection();
        $updateTweetSql = 'UPDATE tweets SET deleted=1,
                            updated="'.date('Y-m-d').'" WHERE id='.$this->getTweetId();
        $result = $dbConnection->query($updateTweetSql);
        $dbConnection->close();
        $dbConnection=null;
        return $result;
    }

    public function getAllComments() {
        $dbConnection = DbConnection::getConnection();
        $sqlComments = 'SELECT id FROM tweet_comments WHERE deleted=0 AND tweet_id='.$this->tweetId.' ORDER BY creation_date DESC';
        $result = $dbConnection->query($sqlComments);
        while ($row = $result->fetch_assoc() ) {
            $comment = new TweetComment();
            $comment->loadCommentFromDb($row['id']);
            $comment->showComment();
        }
    }

    public function numberOfComments() {
        $dbConnection = DbConnection::getConnection();
        $sqlCount = 'SELECT id FROM tweet_comments WHERE tweet_id='.$this->tweetId.' AND deleted=0 ';
        $result = $dbConnection->query($sqlCount);
        return $result->num_rows;
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