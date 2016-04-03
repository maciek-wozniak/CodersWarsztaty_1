
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `twitter_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE IF NOT EXISTS `friends` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inviting_user_id` int(11) NOT NULL,
  `friend_user_id` int(11) NOT NULL,
  `request_accepted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `inviting_user_id` (`inviting_user_id`),
  KEY `friend_user_id` (`friend_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf16 AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Table structure for table `message_that_propose_friendship`
--

CREATE TABLE IF NOT EXISTS `message_that_propose_friendship` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `friends_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unrelation` (`message_id`,`friends_id`),
  KEY `friends_id` (`friends_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf16 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `readed` tinyint(1) DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `message_text` text NOT NULL,
  `send_time` datetime NOT NULL,
  `sender_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `receinver_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf16 AUTO_INCREMENT=84 ;

-- --------------------------------------------------------

--
-- Table structure for table `tweet_comments`
--

CREATE TABLE IF NOT EXISTS `tweet_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tweet_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `creation_date` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `tweet_id` (`tweet_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf16 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `tweets`
--

CREATE TABLE IF NOT EXISTS `tweets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` int(11) NOT NULL,
  `tweet_text` text NOT NULL,
  `created` datetime NOT NULL,
  `updated` date DEFAULT NULL,
  `deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf16 AUTO_INCREMENT=63 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(80) NOT NULL,
  `password` varchar(80) NOT NULL,
  `username` varchar(80) DEFAULT NULL,
  `salt` varchar(22) NOT NULL,
  `createdUser` date NOT NULL,
  `editedUser` date DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf16 AUTO_INCREMENT=30 ;


--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`inviting_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `message_that_propose_friendship`
--
ALTER TABLE `message_that_propose_friendship`
  ADD CONSTRAINT `message_that_propose_friendship_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`),
  ADD CONSTRAINT `message_that_propose_friendship_ibfk_2` FOREIGN KEY (`friends_id`) REFERENCES `friends` (`id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `tweet_comments`
--
ALTER TABLE `tweet_comments`
  ADD CONSTRAINT `tweet_comments_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `tweet_comments_ibfk_2` FOREIGN KEY (`tweet_id`) REFERENCES `tweets` (`id`);

--
-- Constraints for table `tweets`
--
ALTER TABLE `tweets`
  ADD CONSTRAINT `tweets_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);
