CREATE TABLE `follower` (
  `user_id` int(11) DEFAULT NULL,
  `follower_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `followered` (
  `user_id` int(11) DEFAULT NULL,
  `followered_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `tweet` (
  `tweet_user_id` int(11) DEFAULT NULL,
  `tweet_id` int(11) NOT NULL AUTO_INCREMENT,
  `tweet_messages` text COLLATE utf8mb4_unicode_520_ci,
  `tweet_created` datetime DEFAULT NULL,
  PRIMARY KEY (`tweet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=225 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_password` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `user_created` datetime DEFAULT NULL,
  `user_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`,`user_name`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;