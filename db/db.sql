DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `user_email`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(225) NOT NULL,
  `email` varchar(255) NOT NULL,
  `activation_token` varchar(225) NOT NULL,
  `last_activation_request` int(11) NOT NULL,
  `lost_password_request` int(1) NOT NULL DEFAULT 0,
  `active` int(1) NOT NULL DEFAULT 0,
  `role` VARCHAR(30) NOT NULL DEFAULT 'member',
  `sign_up_date` datetime NOT NULL DEFAULT '2013-01-01 00:00:00',
  `last_sign_in` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `bio` mediumtext,
  `title` varchar(100),
  `organization` varchar(200),
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

CREATE TABLE `user_email` (
  `email` varchar(150) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'active',
  `time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (`email`)
) ENGINE= InnoDB DEFAULT CHARSET=utf8;