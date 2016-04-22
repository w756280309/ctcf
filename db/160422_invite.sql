CREATE TABLE IF NOT EXISTS `Invite` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `code` varchar(50) NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  `updated_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `InviterRelation` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `inviterUid` int(10) NOT NULL COMMENT '邀请人uid',
  `inviteeUid` int(10) NOT NULL COMMENT '被邀请人uid',
  `code` varchar(50) NOT NULL COMMENT '邀请码',
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `inviterUid` (`inviterUid`,`inviteeUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请关系表' AUTO_INCREMENT=1;
