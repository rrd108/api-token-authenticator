CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255),
  `token_expiration` datetime,
  `created` datetime,
  `modifed` datetime
);