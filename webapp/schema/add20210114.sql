ALTER TABLE `messages` ADD `open_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `ogp_image`, ADD `close_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `open_datetime`;
ALTER TABLE `messages` ADD `is_direct` TINYINT(1) NOT NULL DEFAULT '0' AFTER `close_datetime`;
ALTER TABLE `messages` ADD `message_title` VARCHAR(255) NOT NULL DEFAULT '' AFTER `target_user_id`;


ALTER TABLE `users` ADD `user_point` INT(11) NOT NULL DEFAULT '0' AFTER `user_expire`;


CREATE TABLE `user_points` (
  `user_point_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `point` int(11) NOT NULL DEFAULT '0',
  `reason` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `point_date` date NOT NULL DEFAULT '0000-00-00',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `user_points`
  ADD PRIMARY KEY (`user_point_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reason` (`reason`),
  ADD KEY `point_date` (`point_date`);


ALTER TABLE `user_points`
  MODIFY `user_point_id` int(11) NOT NULL AUTO_INCREMENT;