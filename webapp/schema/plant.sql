--
-- テーブルの構造 `artists`
--

CREATE TABLE `artists` (
  `artist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `artist_key` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `artist_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `artist_description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `artist_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `artist_urls`
--

CREATE TABLE `artist_urls` (
  `artist_url_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0',
  `message_id` int(11) NOT NULL DEFAULT '0',
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `site_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image_file_id` int(11) NOT NULL DEFAULT '0',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `artist_users`
--

CREATE TABLE `artist_users` (
  `artist_user_id` int(11) NOT NULL,
  `artist_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  `artist_user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `artist_part` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_key` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `event_open_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `event_start_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `event_close_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `event_name` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `event_description` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `event_url` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `streaming_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `view_open_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `view_close_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recommend_number` int(11) NOT NULL DEFAULT '0',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `event_artists`
--

CREATE TABLE `event_artists` (
  `event_artist_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL DEFAULT '0',
  `artist_id` int(11) NOT NULL DEFAULT '0',
  `view_order` int(11) NOT NULL DEFAULT '0',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `event_fees`
--

CREATE TABLE `event_fees` (
  `event_fee_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL DEFAULT '0',
  `fee_name` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `view_order` int(11) NOT NULL DEFAULT '0',
  `fee` int(11) NOT NULL DEFAULT '0',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `event_photos`
--

CREATE TABLE `event_photos` (
  `event_photo_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL DEFAULT '0',
  `file_id` int(11) NOT NULL DEFAULT '0',
  `view_order` int(11) NOT NULL DEFAULT '0',
  `photo_comment` text COLLATE utf8_unicode_ci NOT NULL,
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `favorites`
--

CREATE TABLE `favorites` (
  `favorite_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0',
  `page_id` int(11) NOT NULL DEFAULT '0',
  `file_id` int(11) NOT NULL DEFAULT '0',
  `artist_id` int(11) NOT NULL DEFAULT '0',
  `video_id` int(11) NOT NULL DEFAULT '0',
  `message_id` int(11) NOT NULL DEFAULT '0',
  `target_user_id` int(11) NOT NULL DEFAULT '0',
  `favorite_type` int(11) NOT NULL DEFAULT '0',
  `favorite_level` int(11) NOT NULL DEFAULT '0',
  `view_order` int(11) NOT NULL DEFAULT '0',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `server_filename` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `file_type` int(11) NOT NULL DEFAULT '0',
  `file_size` int(11) NOT NULL DEFAULT '0',
  `file_ext` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `artist_id` int(11) NOT NULL DEFAULT '0',
  `event_id` int(11) NOT NULL DEFAULT '0',
  `live_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `target_user_id` int(11) NOT NULL DEFAULT '0',
  `message_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `message_body` text COLLATE utf8_unicode_ci NOT NULL,
  `ogp_title` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ogp_description` text COLLATE utf8_unicode_ci NOT NULL,
  `ogp_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `open_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `close_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_direct` tinyint(1) NOT NULL DEFAULT '0',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `pages`
--

CREATE TABLE `pages` (
  `page_id` int(11) NOT NULL,
  `page_filename` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_title` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `page_html` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `page_description` text COLLATE utf8_unicode_ci NOT NULL,
  `page_image` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `is_index` tinyint(1) NOT NULL DEFAULT '0',
  `page_open_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `page_close_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `settings`
--

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `setting_key` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `setting_value` text COLLATE utf8_unicode_ci NOT NULL,
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_auth` int(11) NOT NULL DEFAULT '0',
  `user_key` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_mail` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password_md5` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `twitter_auth` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `facebook_auth` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `line_auth` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `insta_auth` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_nickname` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_profile` text COLLATE utf8_unicode_ci NOT NULL,
  `user_photo_file_id` int(11) NOT NULL DEFAULT '0',
  `user_url` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_twitter` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_facebook` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_expire` date NOT NULL DEFAULT '0000-00-00',
  `user_point` int(11) NOT NULL DEFAULT '0',
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `is_removed` tinyint(1) NOT NULL DEFAULT '0',
  `m_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `r_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `user_points`
--

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`artist_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artist_key` (`artist_key`);

--
-- Indexes for table `artist_urls`
--
ALTER TABLE `artist_urls`
  ADD PRIMARY KEY (`artist_url_id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `image_file_id` (`image_file_id`);

--
-- Indexes for table `artist_users`
--
ALTER TABLE `artist_users`
  ADD PRIMARY KEY (`artist_user_id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `event_key` (`event_key`);

--
-- Indexes for table `event_artists`
--
ALTER TABLE `event_artists`
  ADD PRIMARY KEY (`event_artist_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `event_fees`
--
ALTER TABLE `event_fees`
  ADD PRIMARY KEY (`event_fee_id`);

--
-- Indexes for table `event_photos`
--
ALTER TABLE `event_photos`
  ADD PRIMARY KEY (`event_photo_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `file_id` (`file_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`favorite_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `page_id` (`page_id`),
  ADD KEY `file_id` (`file_id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `video_id` (`video_id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `target_user_id` (`target_user_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artist_id` (`artist_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `target_user_id` (`target_user_id`),
  ADD KEY `live_key` (`live_key`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`),
  ADD KEY `page_filename` (`page_filename`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_mail` (`user_mail`),
  ADD KEY `user_photo_file_id` (`user_photo_file_id`),
  ADD KEY `user_key` (`user_key`);

--
-- Indexes for table `user_points`
--
ALTER TABLE `user_points`
  ADD PRIMARY KEY (`user_point_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reason` (`reason`),
  ADD KEY `point_date` (`point_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `artist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `artist_urls`
--
ALTER TABLE `artist_urls`
  MODIFY `artist_url_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `artist_users`
--
ALTER TABLE `artist_users`
  MODIFY `artist_user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_artists`
--
ALTER TABLE `event_artists`
  MODIFY `event_artist_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_fees`
--
ALTER TABLE `event_fees`
  MODIFY `event_fee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_photos`
--
ALTER TABLE `event_photos`
  MODIFY `event_photo_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `favorite_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_points`
--
ALTER TABLE `user_points`
  MODIFY `user_point_id` int(11) NOT NULL AUTO_INCREMENT;