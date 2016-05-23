CREATE DATABASE IF NOT EXISTS hangmanLogin;

USE hangmanLogin;

CREATE TABLE IF NOT EXISTS `users_auth` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `gamesWin` int(11) NOT NULL DEFAULT 0,
  `gamesLost` int(11) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=187 ;



CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `word` (
  `id` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `word`
  ADD PRIMARY KEY (`id`);
