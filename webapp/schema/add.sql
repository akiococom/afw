

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `artist_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0',
  `target_user_id` int(11) NOT NULL DEFAULT '0',
  `message_body` text COLLATE utf8_unicode_ci NOT NULL,
  `ogp_title` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ogp_description` text COLLATE utf8_unicode_ci NOT NULL,
  `ogp_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `target_user_id` (`target_user_id`);
  
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;
  
  
ALTER TABLE `events` ADD `streaming_url` VARCHAR(255) NOT NULL DEFAULT '0' AFTER `event_url`;
ALTER TABLE `users` ADD `user_expire` DATE NOT NULL DEFAULT '0000-00-00' AFTER `user_facebook`;
