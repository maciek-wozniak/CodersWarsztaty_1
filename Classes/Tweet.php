<?php

Class Tweet {
    private $tweetId;
    private $authorId;
    private $tweetText;


    public function __construct() {
        $this->tweetId = -1;
        $this->authorId = 0;
        $this->tweetText = '';
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
        }
    }

    public function createTweet() {

    }

    public function updateTweet() {

    }

    public function showTweet() {

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
        $this->authorId = $authorId;
    }

    public function getTweetText() {
        return $this->tweetText;
    }

    public function setTweetText($tweetText) {
        $this->tweetText = $tweetText;
    }



}