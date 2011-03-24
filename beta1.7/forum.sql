/*******************************************************************************
    The Kingdoms of Chaos - An online browser text game - <http://www.tkoc.net>
    Copyright (C) 2011 - Administrators of The Kingdoms of Chaos

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    Contact Information:
	Petros Karipidis  - petros@rufunka.com - <http://www.rufunka.com/>
	Anastasios Nistas - tasosos@gmail.com  - <http://tasos.pavta.com/>
	
	Other Information
	=================
	The exact Author of each source file should be specified after this license
	notice. If not specified then the "Current Administrators" found at
	<http://www.tkoc.net/about.php> are considered the Authors of the source
	file.

	As stated at the License Section 5.d: "If the work has interactive user
	interfaces, each must display Appropriate Legal Notices; however, if the
	Program has interactive interfaces that do not display Appropriate Legal
	Notices, your work need not make them do so.", we require you give
	credits at the appropriate section of your interface.
********************************************************************************/
-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 24, 2011 at 01:00 AM
-- Server version: 5.0.92
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forum`
--

-- --------------------------------------------------------

--
-- Table structure for table `smf_admin_info_files`
--

CREATE TABLE `smf_admin_info_files` (
  `id_file` tinyint(4) unsigned NOT NULL auto_increment,
  `filename` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `parameters` varchar(255) NOT NULL default '',
  `data` text character set latin1 NOT NULL,
  `filetype` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_file`),
  KEY `filename` (`filename`(30))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_approval_queue`
--

CREATE TABLE `smf_approval_queue` (
  `id_msg` int(10) unsigned NOT NULL default '0',
  `id_attach` int(10) unsigned NOT NULL default '0',
  `id_event` smallint(5) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `smf_attachments`
--

CREATE TABLE `smf_attachments` (
  `id_attach` int(10) unsigned NOT NULL auto_increment,
  `id_thumb` int(10) unsigned NOT NULL default '0',
  `id_msg` int(10) unsigned NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `id_folder` tinyint(3) NOT NULL default '1',
  `attachment_type` tinyint(3) unsigned NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `file_hash` varchar(40) NOT NULL default '',
  `fileext` varchar(8) NOT NULL default '',
  `size` int(10) unsigned NOT NULL default '0',
  `downloads` mediumint(8) unsigned NOT NULL default '0',
  `width` mediumint(8) unsigned NOT NULL default '0',
  `height` mediumint(8) unsigned NOT NULL default '0',
  `mime_type` varchar(20) NOT NULL default '',
  `approved` tinyint(3) NOT NULL default '1',
  PRIMARY KEY  (`id_attach`),
  UNIQUE KEY `id_member` (`id_member`,`id_attach`),
  KEY `id_msg` (`id_msg`),
  KEY `attachment_type` (`attachment_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_ban_groups`
--

CREATE TABLE `smf_ban_groups` (
  `id_ban_group` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  `ban_time` int(10) unsigned NOT NULL default '0',
  `expire_time` int(10) unsigned default NULL,
  `cannot_access` tinyint(3) unsigned NOT NULL default '0',
  `cannot_register` tinyint(3) unsigned NOT NULL default '0',
  `cannot_post` tinyint(3) unsigned NOT NULL default '0',
  `cannot_login` tinyint(3) unsigned NOT NULL default '0',
  `reason` varchar(255) NOT NULL default '',
  `notes` text NOT NULL,
  PRIMARY KEY  (`id_ban_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_ban_items`
--

CREATE TABLE `smf_ban_items` (
  `id_ban` mediumint(8) unsigned NOT NULL auto_increment,
  `id_ban_group` smallint(5) unsigned NOT NULL default '0',
  `ip_low1` tinyint(3) unsigned NOT NULL default '0',
  `ip_high1` tinyint(3) unsigned NOT NULL default '0',
  `ip_low2` tinyint(3) unsigned NOT NULL default '0',
  `ip_high2` tinyint(3) unsigned NOT NULL default '0',
  `ip_low3` tinyint(3) unsigned NOT NULL default '0',
  `ip_high3` tinyint(3) unsigned NOT NULL default '0',
  `ip_low4` tinyint(3) unsigned NOT NULL default '0',
  `ip_high4` tinyint(3) unsigned NOT NULL default '0',
  `hostname` varchar(255) NOT NULL default '',
  `email_address` varchar(255) NOT NULL default '',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `hits` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_ban`),
  KEY `id_ban_group` (`id_ban_group`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_boards`
--

CREATE TABLE `smf_boards` (
  `id_board` smallint(5) unsigned NOT NULL auto_increment,
  `id_cat` tinyint(4) unsigned NOT NULL default '0',
  `child_level` tinyint(4) unsigned NOT NULL default '0',
  `id_parent` smallint(5) unsigned NOT NULL default '0',
  `board_order` smallint(5) NOT NULL default '0',
  `id_last_msg` int(10) unsigned NOT NULL default '0',
  `id_msg_updated` int(10) unsigned NOT NULL default '0',
  `member_groups` varchar(255) NOT NULL default '-1,0',
  `id_profile` smallint(5) unsigned NOT NULL default '1',
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `num_topics` mediumint(8) unsigned NOT NULL default '0',
  `num_posts` mediumint(8) unsigned NOT NULL default '0',
  `count_posts` tinyint(4) NOT NULL default '0',
  `id_theme` tinyint(4) unsigned NOT NULL default '0',
  `override_theme` tinyint(4) unsigned NOT NULL default '0',
  `unapproved_posts` smallint(5) NOT NULL default '0',
  `unapproved_topics` smallint(5) NOT NULL default '0',
  `redirect` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_board`),
  UNIQUE KEY `categories` (`id_cat`,`id_board`),
  KEY `id_parent` (`id_parent`),
  KEY `id_msg_updated` (`id_msg_updated`),
  KEY `member_groups` (`member_groups`(48))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_board_permissions`
--

CREATE TABLE `smf_board_permissions` (
  `id_group` smallint(5) NOT NULL default '0',
  `id_profile` smallint(5) unsigned NOT NULL default '0',
  `permission` varchar(30) NOT NULL default '',
  `add_deny` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id_group`,`id_profile`,`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_calendar`
--

CREATE TABLE `smf_calendar` (
  `id_event` smallint(5) unsigned NOT NULL auto_increment,
  `start_date` date NOT NULL default '0001-01-01',
  `end_date` date NOT NULL default '0001-01-01',
  `id_board` smallint(5) unsigned NOT NULL default '0',
  `id_topic` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(60) NOT NULL default '',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_event`),
  KEY `start_date` (`start_date`),
  KEY `end_date` (`end_date`),
  KEY `topic` (`id_topic`,`id_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_calendar_holidays`
--

CREATE TABLE `smf_calendar_holidays` (
  `id_holiday` smallint(5) unsigned NOT NULL auto_increment,
  `event_date` date NOT NULL default '0001-01-01',
  `title` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`id_holiday`),
  KEY `event_date` (`event_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_categories`
--

CREATE TABLE `smf_categories` (
  `id_cat` tinyint(4) unsigned NOT NULL auto_increment,
  `cat_order` tinyint(4) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `can_collapse` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id_cat`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_collapsed_categories`
--

CREATE TABLE `smf_collapsed_categories` (
  `id_cat` tinyint(4) unsigned NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_cat`,`id_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_custom_fields`
--

CREATE TABLE `smf_custom_fields` (
  `id_field` smallint(5) NOT NULL auto_increment,
  `col_name` varchar(12) NOT NULL default '',
  `field_name` varchar(40) NOT NULL default '',
  `field_desc` varchar(255) NOT NULL default '',
  `field_type` varchar(8) NOT NULL default 'text',
  `field_length` smallint(5) NOT NULL default '255',
  `field_options` text NOT NULL,
  `mask` varchar(255) NOT NULL default '',
  `show_reg` tinyint(3) NOT NULL default '0',
  `show_display` tinyint(3) NOT NULL default '0',
  `show_profile` varchar(20) NOT NULL default 'forumProfile',
  `private` tinyint(3) NOT NULL default '0',
  `active` tinyint(3) NOT NULL default '1',
  `bbc` tinyint(3) NOT NULL default '0',
  `can_search` tinyint(3) NOT NULL default '0',
  `default_value` varchar(255) NOT NULL default '',
  `enclose` text NOT NULL,
  `placement` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id_field`),
  UNIQUE KEY `col_name` (`col_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_group_moderators`
--

CREATE TABLE `smf_group_moderators` (
  `id_group` smallint(5) unsigned NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_group`,`id_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_actions`
--

CREATE TABLE `smf_log_actions` (
  `id_action` int(10) unsigned NOT NULL auto_increment,
  `id_log` tinyint(3) unsigned NOT NULL default '1',
  `log_time` int(10) unsigned NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `ip` char(16) NOT NULL default '',
  `action` varchar(30) NOT NULL default '',
  `id_board` smallint(5) unsigned NOT NULL default '0',
  `id_topic` mediumint(8) unsigned NOT NULL default '0',
  `id_msg` int(10) unsigned NOT NULL default '0',
  `extra` text NOT NULL,
  PRIMARY KEY  (`id_action`),
  KEY `id_log` (`id_log`),
  KEY `log_time` (`log_time`),
  KEY `id_member` (`id_member`),
  KEY `id_board` (`id_board`),
  KEY `id_msg` (`id_msg`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_activity`
--

CREATE TABLE `smf_log_activity` (
  `date` date NOT NULL default '0001-01-01',
  `hits` mediumint(8) unsigned NOT NULL default '0',
  `topics` smallint(5) unsigned NOT NULL default '0',
  `posts` smallint(5) unsigned NOT NULL default '0',
  `registers` smallint(5) unsigned NOT NULL default '0',
  `most_on` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`date`),
  KEY `most_on` (`most_on`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_banned`
--

CREATE TABLE `smf_log_banned` (
  `id_ban_log` mediumint(8) unsigned NOT NULL auto_increment,
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `ip` char(16) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `log_time` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_ban_log`),
  KEY `log_time` (`log_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_boards`
--

CREATE TABLE `smf_log_boards` (
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `id_board` smallint(5) unsigned NOT NULL default '0',
  `id_msg` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_member`,`id_board`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_comments`
--

CREATE TABLE `smf_log_comments` (
  `id_comment` mediumint(8) unsigned NOT NULL auto_increment,
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `member_name` varchar(80) NOT NULL default '',
  `comment_type` varchar(8) NOT NULL default 'warning',
  `id_recipient` mediumint(8) unsigned NOT NULL default '0',
  `recipient_name` varchar(255) NOT NULL default '',
  `log_time` int(10) NOT NULL default '0',
  `id_notice` mediumint(8) unsigned NOT NULL default '0',
  `counter` tinyint(3) NOT NULL default '0',
  `body` text NOT NULL,
  PRIMARY KEY  (`id_comment`),
  KEY `id_recipient` (`id_recipient`),
  KEY `log_time` (`log_time`),
  KEY `comment_type` (`comment_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_digest`
--

CREATE TABLE `smf_log_digest` (
  `id_topic` mediumint(8) unsigned NOT NULL,
  `id_msg` int(10) unsigned NOT NULL,
  `note_type` varchar(10) NOT NULL default 'post',
  `daily` smallint(3) unsigned NOT NULL default '0',
  `exclude` mediumint(8) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_errors`
--

CREATE TABLE `smf_log_errors` (
  `id_error` mediumint(8) unsigned NOT NULL auto_increment,
  `log_time` int(10) unsigned NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `ip` char(16) NOT NULL default '',
  `url` text NOT NULL,
  `message` text NOT NULL,
  `session` char(32) NOT NULL default '',
  `error_type` char(15) NOT NULL default 'general',
  `file` varchar(255) NOT NULL default '',
  `line` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_error`),
  KEY `log_time` (`log_time`),
  KEY `id_member` (`id_member`),
  KEY `ip` (`ip`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_floodcontrol`
--

CREATE TABLE `smf_log_floodcontrol` (
  `ip` char(16) NOT NULL default '',
  `log_time` int(10) unsigned NOT NULL default '0',
  `log_type` varchar(8) NOT NULL default 'post',
  PRIMARY KEY  (`ip`,`log_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_group_requests`
--

CREATE TABLE `smf_log_group_requests` (
  `id_request` mediumint(8) unsigned NOT NULL auto_increment,
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `id_group` smallint(5) unsigned NOT NULL default '0',
  `time_applied` int(10) unsigned NOT NULL default '0',
  `reason` text character set latin1 NOT NULL,
  PRIMARY KEY  (`id_request`),
  UNIQUE KEY `id_member` (`id_member`,`id_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_karma`
--

CREATE TABLE `smf_log_karma` (
  `id_target` mediumint(8) unsigned NOT NULL default '0',
  `id_executor` mediumint(8) unsigned NOT NULL default '0',
  `log_time` int(10) unsigned NOT NULL default '0',
  `action` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id_target`,`id_executor`),
  KEY `log_time` (`log_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_mark_read`
--

CREATE TABLE `smf_log_mark_read` (
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `id_board` smallint(5) unsigned NOT NULL default '0',
  `id_msg` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_member`,`id_board`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_member_notices`
--

CREATE TABLE `smf_log_member_notices` (
  `id_notice` mediumint(8) unsigned NOT NULL auto_increment,
  `subject` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  PRIMARY KEY  (`id_notice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_notify`
--

CREATE TABLE `smf_log_notify` (
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `id_topic` mediumint(8) unsigned NOT NULL default '0',
  `id_board` smallint(5) unsigned NOT NULL default '0',
  `sent` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_member`,`id_topic`,`id_board`),
  KEY `id_topic` (`id_topic`,`id_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_online`
--

CREATE TABLE `smf_log_online` (
  `session` varchar(32) NOT NULL default '',
  `log_time` int(10) NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `id_spider` smallint(5) unsigned NOT NULL default '0',
  `ip` int(10) unsigned NOT NULL default '0',
  `url` text NOT NULL,
  PRIMARY KEY  (`session`),
  KEY `log_time` (`log_time`),
  KEY `id_member` (`id_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_packages`
--

CREATE TABLE `smf_log_packages` (
  `id_install` int(10) NOT NULL auto_increment,
  `filename` varchar(255) NOT NULL default '',
  `package_id` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `version` varchar(255) NOT NULL default '',
  `id_member_installed` mediumint(8) NOT NULL default '0',
  `member_installed` varchar(255) NOT NULL default '',
  `time_installed` int(10) NOT NULL default '0',
  `id_member_removed` mediumint(8) NOT NULL default '0',
  `member_removed` varchar(255) NOT NULL default '',
  `time_removed` int(10) NOT NULL default '0',
  `install_state` tinyint(3) NOT NULL default '1',
  `failed_steps` text NOT NULL,
  `themes_installed` varchar(255) NOT NULL default '',
  `db_changes` text NOT NULL,
  PRIMARY KEY  (`id_install`),
  KEY `filename` (`filename`(15))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_polls`
--

CREATE TABLE `smf_log_polls` (
  `id_poll` mediumint(8) unsigned NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `id_choice` tinyint(3) unsigned NOT NULL default '0',
  KEY `id_poll` (`id_poll`,`id_member`,`id_choice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_reported`
--

CREATE TABLE `smf_log_reported` (
  `id_report` mediumint(8) unsigned NOT NULL auto_increment,
  `id_msg` int(10) unsigned NOT NULL default '0',
  `id_topic` mediumint(8) unsigned NOT NULL default '0',
  `id_board` smallint(5) unsigned NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `membername` varchar(255) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  `time_started` int(10) NOT NULL default '0',
  `time_updated` int(10) NOT NULL default '0',
  `num_reports` mediumint(6) NOT NULL default '0',
  `closed` tinyint(3) NOT NULL default '0',
  `ignore_all` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id_report`),
  KEY `id_member` (`id_member`),
  KEY `id_topic` (`id_topic`),
  KEY `closed` (`closed`),
  KEY `time_started` (`time_started`),
  KEY `id_msg` (`id_msg`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_reported_comments`
--

CREATE TABLE `smf_log_reported_comments` (
  `id_comment` mediumint(8) unsigned NOT NULL auto_increment,
  `id_report` mediumint(8) NOT NULL default '0',
  `id_member` mediumint(8) NOT NULL,
  `membername` varchar(255) NOT NULL default '',
  `comment` varchar(255) NOT NULL default '',
  `time_sent` int(10) NOT NULL,
  PRIMARY KEY  (`id_comment`),
  KEY `id_report` (`id_report`),
  KEY `id_member` (`id_member`),
  KEY `time_sent` (`time_sent`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_scheduled_tasks`
--

CREATE TABLE `smf_log_scheduled_tasks` (
  `id_log` mediumint(8) NOT NULL auto_increment,
  `id_task` smallint(5) NOT NULL default '0',
  `time_run` int(10) NOT NULL default '0',
  `time_taken` float NOT NULL default '0',
  PRIMARY KEY  (`id_log`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_search_messages`
--

CREATE TABLE `smf_log_search_messages` (
  `id_search` tinyint(3) unsigned NOT NULL default '0',
  `id_msg` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_search`,`id_msg`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_search_results`
--

CREATE TABLE `smf_log_search_results` (
  `id_search` tinyint(3) unsigned NOT NULL default '0',
  `id_topic` mediumint(8) unsigned NOT NULL default '0',
  `id_msg` int(10) unsigned NOT NULL default '0',
  `relevance` smallint(5) unsigned NOT NULL default '0',
  `num_matches` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_search`,`id_topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_search_subjects`
--

CREATE TABLE `smf_log_search_subjects` (
  `word` varchar(20) NOT NULL default '',
  `id_topic` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`word`,`id_topic`),
  KEY `id_topic` (`id_topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_search_topics`
--

CREATE TABLE `smf_log_search_topics` (
  `id_search` tinyint(3) unsigned NOT NULL default '0',
  `id_topic` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_search`,`id_topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_spider_hits`
--

CREATE TABLE `smf_log_spider_hits` (
  `id_hit` int(10) unsigned NOT NULL auto_increment,
  `id_spider` smallint(5) unsigned NOT NULL default '0',
  `log_time` int(10) unsigned NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `processed` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id_hit`),
  KEY `id_spider` (`id_spider`),
  KEY `log_time` (`log_time`),
  KEY `processed` (`processed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_spider_stats`
--

CREATE TABLE `smf_log_spider_stats` (
  `id_spider` smallint(5) unsigned NOT NULL default '0',
  `page_hits` smallint(5) unsigned NOT NULL default '0',
  `last_seen` int(10) unsigned NOT NULL default '0',
  `stat_date` date NOT NULL default '0001-01-01',
  PRIMARY KEY  (`stat_date`,`id_spider`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_subscribed`
--

CREATE TABLE `smf_log_subscribed` (
  `id_sublog` int(10) unsigned NOT NULL auto_increment,
  `id_subscribe` mediumint(8) unsigned NOT NULL default '0',
  `id_member` int(10) NOT NULL default '0',
  `old_id_group` smallint(5) NOT NULL default '0',
  `start_time` int(10) NOT NULL default '0',
  `end_time` int(10) NOT NULL default '0',
  `status` tinyint(3) NOT NULL default '0',
  `payments_pending` tinyint(3) NOT NULL default '0',
  `pending_details` text NOT NULL,
  `reminder_sent` tinyint(3) NOT NULL default '0',
  `vendor_ref` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_sublog`),
  UNIQUE KEY `id_subscribe` (`id_subscribe`,`id_member`),
  KEY `end_time` (`end_time`),
  KEY `reminder_sent` (`reminder_sent`),
  KEY `payments_pending` (`payments_pending`),
  KEY `status` (`status`),
  KEY `id_member` (`id_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_topics`
--

CREATE TABLE `smf_log_topics` (
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `id_topic` mediumint(8) unsigned NOT NULL default '0',
  `id_msg` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_member`,`id_topic`),
  KEY `id_topic` (`id_topic`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_log_treasurey`
--

CREATE TABLE `smf_log_treasurey` (
  `id` int(11) NOT NULL auto_increment,
  `log_date` int(10) NOT NULL default '1292183361',
  `payment_date` int(10) NOT NULL default '1292183361',
  `logentry` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_mail_queue`
--

CREATE TABLE `smf_mail_queue` (
  `id_mail` int(10) unsigned NOT NULL auto_increment,
  `time_sent` int(10) NOT NULL default '0',
  `recipient` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  `subject` varchar(255) NOT NULL default '',
  `headers` text NOT NULL,
  `send_html` tinyint(3) NOT NULL default '0',
  `priority` tinyint(3) NOT NULL default '1',
  `private` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id_mail`),
  KEY `time_sent` (`time_sent`),
  KEY `mail_priority` (`priority`,`id_mail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_membergroups`
--

CREATE TABLE `smf_membergroups` (
  `id_group` smallint(5) unsigned NOT NULL auto_increment,
  `group_name` varchar(80) NOT NULL default '',
  `description` text NOT NULL,
  `online_color` varchar(20) NOT NULL default '',
  `min_posts` mediumint(9) NOT NULL default '-1',
  `max_messages` smallint(5) unsigned NOT NULL default '0',
  `stars` varchar(255) NOT NULL default '',
  `group_type` tinyint(3) NOT NULL default '0',
  `hidden` tinyint(3) NOT NULL default '0',
  `id_parent` smallint(5) NOT NULL default '-2',
  PRIMARY KEY  (`id_group`),
  KEY `min_posts` (`min_posts`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_members`
--

CREATE TABLE `smf_members` (
  `id_member` mediumint(8) unsigned NOT NULL auto_increment,
  `member_name` varchar(80) NOT NULL default '',
  `date_registered` int(10) unsigned NOT NULL default '0',
  `posts` mediumint(8) unsigned NOT NULL default '0',
  `id_group` smallint(5) unsigned NOT NULL default '0',
  `lngfile` varchar(255) NOT NULL default '',
  `last_login` int(10) unsigned NOT NULL default '0',
  `real_name` varchar(255) NOT NULL default '',
  `instant_messages` smallint(5) NOT NULL default '0',
  `unread_messages` smallint(5) NOT NULL default '0',
  `new_pm` tinyint(3) unsigned NOT NULL default '0',
  `buddy_list` text NOT NULL,
  `pm_ignore_list` varchar(255) NOT NULL default '',
  `pm_prefs` mediumint(8) NOT NULL default '0',
  `mod_prefs` varchar(20) NOT NULL default '',
  `message_labels` text NOT NULL,
  `passwd` varchar(64) NOT NULL default '',
  `openid_uri` text NOT NULL,
  `email_address` varchar(255) NOT NULL default '',
  `personal_text` varchar(255) NOT NULL default '',
  `gender` tinyint(4) unsigned NOT NULL default '0',
  `birthdate` date NOT NULL default '0001-01-01',
  `website_title` varchar(255) NOT NULL default '',
  `website_url` varchar(255) NOT NULL default '',
  `location` varchar(255) NOT NULL default '',
  `icq` varchar(255) NOT NULL default '',
  `aim` varchar(16) NOT NULL default '',
  `yim` varchar(32) NOT NULL default '',
  `msn` varchar(255) NOT NULL default '',
  `hide_email` tinyint(4) NOT NULL default '0',
  `show_online` tinyint(4) NOT NULL default '1',
  `time_format` varchar(80) NOT NULL default '',
  `signature` text NOT NULL,
  `time_offset` float NOT NULL default '0',
  `avatar` varchar(255) NOT NULL default '',
  `pm_email_notify` tinyint(4) NOT NULL default '0',
  `karma_bad` smallint(5) unsigned NOT NULL default '0',
  `karma_good` smallint(5) unsigned NOT NULL default '0',
  `usertitle` varchar(255) NOT NULL default '',
  `notify_announcements` tinyint(4) NOT NULL default '1',
  `notify_regularity` tinyint(4) NOT NULL default '1',
  `notify_send_body` tinyint(4) NOT NULL default '0',
  `notify_types` tinyint(4) NOT NULL default '2',
  `member_ip` varchar(255) NOT NULL default '',
  `member_ip2` varchar(255) NOT NULL default '',
  `secret_question` varchar(255) NOT NULL default '',
  `secret_answer` varchar(64) NOT NULL default '',
  `id_theme` tinyint(4) unsigned NOT NULL default '0',
  `is_activated` tinyint(3) unsigned NOT NULL default '1',
  `validation_code` varchar(10) NOT NULL default '',
  `id_msg_last_visit` int(10) unsigned NOT NULL default '0',
  `additional_groups` varchar(255) NOT NULL default '',
  `smiley_set` varchar(48) NOT NULL default '',
  `id_post_group` smallint(5) unsigned NOT NULL default '0',
  `total_time_logged_in` int(10) unsigned NOT NULL default '0',
  `password_salt` varchar(255) NOT NULL default '',
  `ignore_boards` text NOT NULL,
  `warning` tinyint(4) NOT NULL default '0',
  `passwd_flood` varchar(12) NOT NULL default '',
  `pm_receive_from` tinyint(3) unsigned NOT NULL default '1',
  `referrals_no` mediumint(8) NOT NULL default '0',
  `referred_by` mediumint(8) NOT NULL default '0',
  `referrals_hits` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_member`),
  KEY `memberName` (`member_name`(30)),
  KEY `date_registered` (`date_registered`),
  KEY `id_group` (`id_group`),
  KEY `birthdate` (`birthdate`),
  KEY `posts` (`posts`),
  KEY `last_login` (`last_login`),
  KEY `lngfile` (`lngfile`(30)),
  KEY `id_post_group` (`id_post_group`),
  KEY `warning` (`warning`),
  KEY `total_time_logged_in` (`total_time_logged_in`),
  KEY `id_theme` (`id_theme`),
  KEY `real_name` (`real_name`),
  KEY `member_name` (`member_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_messages`
--

CREATE TABLE `smf_messages` (
  `id_msg` int(10) unsigned NOT NULL auto_increment,
  `id_topic` mediumint(8) unsigned NOT NULL default '0',
  `id_board` smallint(5) unsigned NOT NULL default '0',
  `poster_time` int(10) unsigned NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `id_msg_modified` int(10) unsigned NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `poster_name` varchar(255) NOT NULL default '',
  `poster_email` varchar(255) NOT NULL default '',
  `poster_ip` varchar(255) NOT NULL default '',
  `smileys_enabled` tinyint(4) NOT NULL default '1',
  `modified_time` int(10) unsigned NOT NULL default '0',
  `modified_name` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  `icon` varchar(16) NOT NULL default 'xx',
  `approved` tinyint(3) NOT NULL default '1',
  PRIMARY KEY  (`id_msg`),
  UNIQUE KEY `topic` (`id_topic`,`id_msg`),
  UNIQUE KEY `id_board` (`id_board`,`id_msg`),
  UNIQUE KEY `id_member` (`id_member`,`id_msg`),
  KEY `approved` (`approved`),
  KEY `ip_index` (`poster_ip`(15),`id_topic`),
  KEY `participation` (`id_member`,`id_topic`),
  KEY `show_posts` (`id_member`,`id_board`),
  KEY `id_topic` (`id_topic`),
  KEY `id_member_msg` (`id_member`,`approved`,`id_msg`),
  KEY `current_topic` (`id_topic`,`id_msg`,`id_member`,`approved`),
  KEY `related_ip` (`id_member`,`poster_ip`,`id_msg`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_message_icons`
--

CREATE TABLE `smf_message_icons` (
  `id_icon` smallint(5) unsigned NOT NULL auto_increment,
  `title` varchar(80) NOT NULL default '',
  `filename` varchar(80) NOT NULL default '',
  `id_board` smallint(5) unsigned NOT NULL default '0',
  `icon_order` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_icon`),
  KEY `id_board` (`id_board`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_moderators`
--

CREATE TABLE `smf_moderators` (
  `id_board` smallint(5) unsigned NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_board`,`id_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_openid_assoc`
--

CREATE TABLE `smf_openid_assoc` (
  `server_url` text NOT NULL,
  `handle` varchar(255) NOT NULL default '',
  `secret` text NOT NULL,
  `issued` int(10) NOT NULL default '0',
  `expires` int(10) NOT NULL default '0',
  `assoc_type` varchar(64) NOT NULL,
  PRIMARY KEY  (`server_url`(125),`handle`(125)),
  KEY `expires` (`expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_package_servers`
--

CREATE TABLE `smf_package_servers` (
  `id_server` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_server`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_permissions`
--

CREATE TABLE `smf_permissions` (
  `id_group` smallint(5) NOT NULL default '0',
  `permission` varchar(30) NOT NULL default '',
  `add_deny` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id_group`,`permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_permission_profiles`
--

CREATE TABLE `smf_permission_profiles` (
  `id_profile` smallint(5) NOT NULL auto_increment,
  `profile_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_profile`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_personal_messages`
--

CREATE TABLE `smf_personal_messages` (
  `id_pm` int(10) unsigned NOT NULL auto_increment,
  `id_pm_head` int(10) unsigned NOT NULL default '0',
  `id_member_from` mediumint(8) unsigned NOT NULL default '0',
  `deleted_by_sender` tinyint(3) unsigned NOT NULL default '0',
  `from_name` varchar(255) NOT NULL default '',
  `msgtime` int(10) unsigned NOT NULL default '0',
  `subject` varchar(255) NOT NULL default '',
  `body` text NOT NULL,
  PRIMARY KEY  (`id_pm`),
  KEY `id_member` (`id_member_from`,`deleted_by_sender`),
  KEY `msgtime` (`msgtime`),
  KEY `id_pm_head` (`id_pm_head`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_pm_recipients`
--

CREATE TABLE `smf_pm_recipients` (
  `id_pm` int(10) unsigned NOT NULL default '0',
  `id_member` mediumint(8) unsigned NOT NULL default '0',
  `labels` varchar(60) NOT NULL default '-1',
  `bcc` tinyint(3) unsigned NOT NULL default '0',
  `is_read` tinyint(3) unsigned NOT NULL default '0',
  `is_new` tinyint(3) unsigned NOT NULL default '0',
  `deleted` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_pm`,`id_member`),
  UNIQUE KEY `id_member` (`id_member`,`deleted`,`id_pm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_pm_rules`
--

CREATE TABLE `smf_pm_rules` (
  `id_rule` int(10) unsigned NOT NULL auto_increment,
  `id_member` int(10) unsigned NOT NULL default '0',
  `rule_name` varchar(60) NOT NULL,
  `criteria` text NOT NULL,
  `actions` text NOT NULL,
  `delete_pm` tinyint(3) unsigned NOT NULL default '0',
  `is_or` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_rule`),
  KEY `id_member` (`id_member`),
  KEY `delete_pm` (`delete_pm`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_polls`
--

CREATE TABLE `smf_polls` (
  `id_poll` mediumint(8) unsigned NOT NULL auto_increment,
  `question` varchar(255) NOT NULL default '',
  `voting_locked` tinyint(1) NOT NULL default '0',
  `max_votes` tinyint(3) unsigned NOT NULL default '1',
  `expire_time` int(10) unsigned NOT NULL default '0',
  `hide_results` tinyint(3) unsigned NOT NULL default '0',
  `change_vote` tinyint(3) unsigned NOT NULL default '0',
  `guest_vote` tinyint(3) unsigned NOT NULL default '0',
  `num_guest_voters` int(10) unsigned NOT NULL default '0',
  `reset_poll` int(10) unsigned NOT NULL default '0',
  `id_member` mediumint(8) NOT NULL default '0',
  `poster_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_poll`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_poll_choices`
--

CREATE TABLE `smf_poll_choices` (
  `id_poll` mediumint(8) unsigned NOT NULL default '0',
  `id_choice` tinyint(3) unsigned NOT NULL default '0',
  `label` varchar(255) NOT NULL default '',
  `votes` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_poll`,`id_choice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_scheduled_tasks`
--

CREATE TABLE `smf_scheduled_tasks` (
  `id_task` smallint(5) NOT NULL auto_increment,
  `next_time` int(10) NOT NULL default '0',
  `time_offset` int(10) NOT NULL default '0',
  `time_regularity` smallint(5) NOT NULL default '0',
  `time_unit` varchar(1) NOT NULL default 'h',
  `disabled` tinyint(3) NOT NULL default '0',
  `task` varchar(24) NOT NULL default '',
  PRIMARY KEY  (`id_task`),
  UNIQUE KEY `task` (`task`),
  KEY `next_time` (`next_time`),
  KEY `disabled` (`disabled`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_sessions`
--

CREATE TABLE `smf_sessions` (
  `session_id` char(32) NOT NULL,
  `last_update` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_settings`
--

CREATE TABLE `smf_settings` (
  `variable` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`variable`(30))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_smileys`
--

CREATE TABLE `smf_smileys` (
  `id_smiley` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(30) NOT NULL default '',
  `filename` varchar(48) NOT NULL default '',
  `description` varchar(80) NOT NULL default '',
  `smiley_row` tinyint(4) unsigned NOT NULL default '0',
  `smiley_order` smallint(5) unsigned NOT NULL default '0',
  `hidden` tinyint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id_smiley`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_spiders`
--

CREATE TABLE `smf_spiders` (
  `id_spider` smallint(5) unsigned NOT NULL auto_increment,
  `spider_name` varchar(255) NOT NULL default '',
  `user_agent` varchar(255) NOT NULL default '',
  `ip_info` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id_spider`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_subscriptions`
--

CREATE TABLE `smf_subscriptions` (
  `id_subscribe` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(60) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `cost` text NOT NULL,
  `length` varchar(6) NOT NULL default '',
  `id_group` smallint(5) NOT NULL default '0',
  `add_groups` varchar(40) NOT NULL default '',
  `active` tinyint(3) NOT NULL default '1',
  `repeatable` tinyint(3) NOT NULL default '0',
  `allow_partial` tinyint(3) NOT NULL default '0',
  `reminder` tinyint(3) NOT NULL default '0',
  `email_complete` text NOT NULL,
  PRIMARY KEY  (`id_subscribe`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_themes`
--

CREATE TABLE `smf_themes` (
  `id_member` mediumint(8) NOT NULL default '0',
  `id_theme` tinyint(4) unsigned NOT NULL default '1',
  `variable` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`id_theme`,`id_member`,`variable`(30)),
  KEY `id_member` (`id_member`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_topics`
--

CREATE TABLE `smf_topics` (
  `id_topic` mediumint(8) unsigned NOT NULL auto_increment,
  `is_sticky` tinyint(4) NOT NULL default '0',
  `id_board` smallint(5) unsigned NOT NULL default '0',
  `id_first_msg` int(10) unsigned NOT NULL default '0',
  `id_last_msg` int(10) unsigned NOT NULL default '0',
  `id_member_started` mediumint(8) unsigned NOT NULL default '0',
  `id_member_updated` mediumint(8) unsigned NOT NULL default '0',
  `id_poll` mediumint(8) unsigned NOT NULL default '0',
  `id_previous_board` smallint(5) NOT NULL default '0',
  `id_previous_topic` mediumint(8) NOT NULL default '0',
  `num_replies` int(10) unsigned NOT NULL default '0',
  `num_views` int(10) unsigned NOT NULL default '0',
  `locked` tinyint(4) NOT NULL default '0',
  `unapproved_posts` smallint(5) NOT NULL default '0',
  `approved` tinyint(3) NOT NULL default '1',
  PRIMARY KEY  (`id_topic`),
  UNIQUE KEY `last_message` (`id_last_msg`,`id_board`),
  UNIQUE KEY `first_message` (`id_first_msg`,`id_board`),
  UNIQUE KEY `poll` (`id_poll`,`id_topic`),
  KEY `is_sticky` (`is_sticky`),
  KEY `approved` (`approved`),
  KEY `id_board` (`id_board`),
  KEY `member_started` (`id_member_started`,`id_board`),
  KEY `last_message_sticky` (`id_board`,`is_sticky`,`id_last_msg`),
  KEY `board_news` (`id_board`,`id_first_msg`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_treas_config`
--

CREATE TABLE `smf_treas_config` (
  `name` varchar(25) NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_treas_donations`
--

CREATE TABLE `smf_treas_donations` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` mediumint(8) NOT NULL default '0',
  `business` varchar(50) NOT NULL default '0',
  `txn_id` varchar(20) NOT NULL default '0',
  `item_name` varchar(60) NOT NULL default '0',
  `item_number` varchar(40) NOT NULL default '0',
  `quantity` varchar(6) NOT NULL default '0',
  `invoice` varchar(40) NOT NULL default '0',
  `custom` varchar(127) NOT NULL default '0',
  `tax` varchar(10) NOT NULL default '0',
  `option_name1` varchar(60) NOT NULL default '0',
  `option_seleczion1` varchar(127) NOT NULL default '0',
  `option_name2` varchar(60) NOT NULL default '0',
  `option_seleczion2` varchar(127) NOT NULL default '0',
  `memo` text NOT NULL,
  `payment_status` varchar(15) NOT NULL default '0',
  `payment_date` int(10) NOT NULL default '1292183361',
  `txn_type` varchar(15) NOT NULL default '0',
  `mc_gross` varchar(10) NOT NULL default '0',
  `mc_fee` varchar(10) NOT NULL default '0',
  `mc_currency` varchar(5) NOT NULL default '0',
  `settle_amount` varchar(12) NOT NULL default '0',
  `exchange_rate` varchar(10) NOT NULL default '0',
  `first_name` varchar(127) NOT NULL default '0',
  `last_name` varchar(127) NOT NULL default '0',
  `address_street` varchar(127) NOT NULL default '0',
  `address_city` varchar(127) NOT NULL default '0',
  `address_state` varchar(127) NOT NULL default '0',
  `address_zip` varchar(20) NOT NULL default '0',
  `address_country` varchar(127) NOT NULL default '0',
  `address_status` varchar(15) NOT NULL default '0',
  `payer_email` varchar(127) NOT NULL default '0',
  `payer_status` varchar(15) NOT NULL default '0',
  `currency_symbol` varchar(7) NOT NULL default '$',
  `group_id` smallint(5) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_treas_events`
--

CREATE TABLE `smf_treas_events` (
  `eid` int(11) NOT NULL auto_increment,
  `date_start` int(10) NOT NULL default '1292183361',
  `date_end` int(10) NOT NULL default '0',
  `title` varchar(25) default '',
  `description` text,
  `target` varchar(10) NOT NULL default '0',
  `actual` varchar(10) NOT NULL default '0',
  PRIMARY KEY  (`eid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_treas_registry`
--

CREATE TABLE `smf_treas_registry` (
  `id` int(11) NOT NULL auto_increment,
  `date` int(10) NOT NULL default '1292183361',
  `num` varchar(16) NOT NULL default '0',
  `name` varchar(128) NOT NULL default '0',
  `descr` varchar(128) NOT NULL default '0',
  `amount` varchar(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_treas_subscribers`
--

CREATE TABLE `smf_treas_subscribers` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` mediumint(8) NOT NULL default '0',
  `group_id` smallint(5) NOT NULL default '0',
  `group_end` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `smf_treas_targets`
--

CREATE TABLE `smf_treas_targets` (
  `name` varchar(25) NOT NULL default '0',
  `subtype` varchar(20) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '0',
  UNIQUE KEY `unique_tgt` (`name`,`subtype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
