CREATE TABLE `NotifyLog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `query` text NOT NULL,
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
