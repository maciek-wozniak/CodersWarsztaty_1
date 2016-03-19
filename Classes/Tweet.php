<?php

Class Tweet {
    private $tweetId;
    private $authorId;
    private $tweetText;

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