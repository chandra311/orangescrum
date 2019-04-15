-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 18 Mar 2019 pada 11.17
-- Versi server: 10.1.37-MariaDB
-- Versi PHP: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `orangescrum`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `addons`
--

CREATE TABLE `addons` (
  `id` tinyint(2) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `isactive` tinyint(2) NOT NULL DEFAULT '0',
  `dt_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `archives`
--

CREATE TABLE `archives` (
  `id` int(250) NOT NULL,
  `easycase_id` int(250) NOT NULL,
  `case_file_id` int(250) NOT NULL,
  `user_id` int(250) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '1-archive,2-move,3-delete',
  `company_id` int(11) NOT NULL,
  `dt_created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `case_actions`
--

CREATE TABLE `case_actions` (
  `id` int(11) NOT NULL,
  `easycase_id` int(11) NOT NULL COMMENT 'Foreign key of "easycases"',
  `user_id` int(11) NOT NULL COMMENT 'Foreign key of "users"',
  `action` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1-Close, 2-Start',
  `dt_created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `case_activities`
--

CREATE TABLE `case_activities` (
  `id` int(11) NOT NULL,
  `easycase_id` int(11) NOT NULL COMMENT 'Foreign key of "easycases"',
  `comment_id` int(11) NOT NULL COMMENT 'Foreign key of "comments"',
  `case_no` smallint(6) NOT NULL,
  `project_id` int(11) NOT NULL COMMENT 'Foreign key of "projects"',
  `user_id` int(11) NOT NULL COMMENT 'Foreign key of "users"',
  `type` tinyint(4) NOT NULL COMMENT '1-New 2-Opened, 3-Closed, 4-Start, 5-Resolve, 7-Comments, 8-Deleted,10->Modified',
  `isactive` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0-Inactive, 1-Active',
  `dt_created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `case_files`
--

CREATE TABLE `case_files` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `easycase_id` int(11) NOT NULL COMMENT 'Foreign key of "easycases"',
  `comment_id` int(11) NOT NULL COMMENT 'Foreign key of "case_comments"',
  `file` varchar(222) CHARACTER SET latin1 NOT NULL,
  `thumb` varchar(222) CHARACTER SET latin1 NOT NULL,
  `file_size` decimal(7,1) NOT NULL,
  `count` smallint(6) NOT NULL,
  `downloadurl` text,
  `isactive` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0-Inactive, 1-Active'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `case_files`
--

INSERT INTO `case_files` (`id`, `user_id`, `project_id`, `company_id`, `easycase_id`, `comment_id`, `file`, `thumb`, `file_size`, `count`, `downloadurl`, `isactive`) VALUES
(1, 1, 1, 1, 4, 0, 'WF-C20590-Full-Option.jpg', '', '608.1', 0, NULL, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `case_file_drives`
--

CREATE TABLE `case_file_drives` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `easycase_id` int(11) NOT NULL COMMENT 'Foreign key of "easycases"',
  `file_info` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `case_filters`
--

CREATE TABLE `case_filters` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL COMMENT 'Foreign Key of "Users"',
  `order` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `case_recents`
--

CREATE TABLE `case_recents` (
  `id` int(11) NOT NULL,
  `easycase_id` int(11) NOT NULL COMMENT 'Foreign key of "easycases"',
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Foreign key of "users"',
  `project_id` int(11) NOT NULL COMMENT 'Foreign key of "projects"',
  `dt_created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `case_recents`
--

INSERT INTO `case_recents` (`id`, `easycase_id`, `company_id`, `user_id`, `project_id`, `dt_created`) VALUES
(1, 1, 1, 1, 1, '2019-03-18 10:12:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `case_settings`
--

CREATE TABLE `case_settings` (
  `id` int(250) NOT NULL,
  `project_id` int(250) NOT NULL,
  `project_uniqid` varchar(250) NOT NULL,
  `type_id` int(250) NOT NULL,
  `assign_to` int(250) NOT NULL,
  `priority` tinyint(4) NOT NULL,
  `due_date` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `case_templates`
--

CREATE TABLE `case_templates` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0-Inactive, 1-Active',
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `case_user_emails`
--

CREATE TABLE `case_user_emails` (
  `id` int(11) NOT NULL,
  `easycase_id` int(11) NOT NULL COMMENT 'Foreign key of "easycases"',
  `user_id` int(11) NOT NULL COMMENT 'Foreign key of "users"',
  `ismail` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0-Stop Mailing, 1-Email Me'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `case_user_emails`
--

INSERT INTO `case_user_emails` (`id`, `easycase_id`, `user_id`, `ismail`) VALUES
(1, 1, 1, 1),
(2, 4, 1, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `case_user_views`
--

CREATE TABLE `case_user_views` (
  `id` int(11) NOT NULL,
  `easycase_id` int(11) NOT NULL COMMENT 'Foreign key of "easycases"',
  `user_id` int(11) NOT NULL COMMENT 'Foreign key of "users"',
  `project_id` int(11) NOT NULL COMMENT 'Foreign key of "projects"',
  `istype` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-New, 2-Reply, 3-Closed, 4-Start, 5-Edit',
  `isviewed` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0-Not Viewed, 1-Viewed',
  `dt_created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `uniq_id` text NOT NULL,
  `name` varchar(250) NOT NULL,
  `seo_url` varchar(250) NOT NULL,
  `subscription_id` int(11) NOT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `website` varchar(100) NOT NULL,
  `contact_phone` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `is_beta` tinyint(4) NOT NULL DEFAULT '0' COMMENT '1->Beta company , 0-> Default',
  `is_active` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:Active , 2: Cancelled ',
  `is_deactivated` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1-> Auto Deactivated , 2-> Deactivated by admin,3->Disable By admin,0-> Default ',
  `twitted` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `companies`
--

INSERT INTO `companies` (`id`, `uniq_id`, `name`, `seo_url`, `subscription_id`, `logo`, `website`, `contact_phone`, `created`, `modified`, `is_beta`, `is_active`, `is_deactivated`, `twitted`) VALUES
(1, 'e346b602bf6a69132a64d80d62cec13d', 'EIN Presales', '', 1, 'default-invoice-logo.png', '', 'NA', '2019-03-18 08:43:43', '2019-03-18 08:43:43', 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `company_users`
--

CREATE TABLE `company_users` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `company_uniq_id` varchar(250) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` int(11) NOT NULL COMMENT '1-owner,2-Admin,3-member',
  `is_active` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0-Inactive, 1-Active,2- Not confirmed , 3- Deleted user',
  `est_billing_amt` float(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Keep the estimated billing amount for the period',
  `act_date` datetime DEFAULT NULL,
  `billing_start_date` datetime DEFAULT NULL,
  `billing_end_date` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `company_users`
--

INSERT INTO `company_users` (`id`, `company_id`, `company_uniq_id`, `user_id`, `user_type`, `is_active`, `est_billing_amt`, `act_date`, `billing_start_date`, `billing_end_date`, `created`, `modified`) VALUES
(1, 1, 'e346b602bf6a69132a64d80d62cec13d', 1, 1, 1, 0.00, NULL, NULL, NULL, '2019-03-18 08:43:43', '2019-03-18 08:43:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `custom_filters`
--

CREATE TABLE `custom_filters` (
  `id` mediumint(9) NOT NULL,
  `project_uniq_id` varchar(64) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `filter_name` varchar(100) NOT NULL,
  `filter_date` text,
  `filter_duedate` datetime DEFAULT NULL,
  `filter_type_id` text NOT NULL,
  `filter_status` text NOT NULL,
  `filter_member_id` text NOT NULL,
  `filter_priority` text NOT NULL,
  `filter_assignto` text NOT NULL,
  `filter_search` text NOT NULL,
  `dt_created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dailyupdate_notifications`
--

CREATE TABLE `dailyupdate_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `dly_update` tinyint(2) NOT NULL,
  `notification_time` varchar(100) NOT NULL,
  `proj_name` varchar(200) NOT NULL,
  `mail_sent` datetime DEFAULT NULL,
  `modified` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `daily_updates`
--

CREATE TABLE `daily_updates` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `post_by` int(11) NOT NULL COMMENT 'user id : who create or update alert settings.',
  `user_id` text NOT NULL COMMENT 'user_ids are separated by comma(,)',
  `timezone_id` int(11) NOT NULL,
  `notification_time` time NOT NULL,
  `days` int(11) NOT NULL DEFAULT '5' COMMENT 'default:5',
  `cron_email_date` date DEFAULT NULL COMMENT 'When email is sent , this field is going to update'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `default_project_templates`
--

CREATE TABLE `default_project_templates` (
  `id` int(11) NOT NULL,
  `user_id` int(250) NOT NULL,
  `company_id` int(250) NOT NULL,
  `module_name` varchar(250) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `default_project_template_cases`
--

CREATE TABLE `default_project_template_cases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(250) NOT NULL,
  `template_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `default_templates`
--

CREATE TABLE `default_templates` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `default_templates`
--

INSERT INTO `default_templates` (`id`, `name`, `description`, `created`, `modified`) VALUES
(1, 'Meeting Minute', '<b>Attendees:</b>  John, Michael<br/>\n				<b>Date and Time:</b> July 11th 11 am PST<br/>\n				<b>Purpose:</b><br/>\n				\n				<br/>\n				<b>Agenda:</b> \n				<o>\n					<li>Discuss Layout </li>\n					<li>Discuss on Design</li>\n				</ol>\n				<br/>\n				<b>Discussion:</b><br/>', '2014-01-24 12:58:24', '2014-01-24 12:58:24'),
(2, 'Status update', '<p><strong>Today\'s accomplishment:</strong></p>\n				<p><strong>&nbsp; &nbsp; &nbsp; Task no: 120</strong></p>\n				<ul>\n				<li>Lorem Ipsum is simply dummy text of the printing and typesetting industry</li>\n				<li>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout</li>\n				<li>Contrary to popular belief, Lorem Ipsum is not simply random text</li>\n				</ul>\n				<p>&nbsp; &nbsp; &nbsp;<strong>Task no: 125</strong></p>\n				<ul>\n				<li>Lorem Ipsum is simply dummy text of the printing and typesetting industry</li>\n				<li>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout</li>\n				<li>Contrary to popular belief, Lorem Ipsum is not simply random text</li>\n				</ul>\n				<p><br /> <strong>List of files changed:</strong></p>\n				<ol>\n				<li>index.html</li>\n				<li>style.css</li>\n				<li>contact-us.html</li>\n				</ol>\n				<p>Is code checked in Repository: <strong>Y/N</strong><br /> Is code available in Stager: <strong>Y/N</strong> </p>\n				<p><strong>Blocker/Testing Issues:</strong></p>\n				<p><strong>Milestone Update: &lt; Specify Milestone name here &gt;</strong></p>\n				<p>&nbsp; &nbsp;1. Total tasks:</p>\n				<p>&nbsp; &nbsp;2. # of Work in Progress tasks:</p>\n				<p>&nbsp; &nbsp;3. # of Resolve tasks:</p>\n				<p>&nbsp; &nbsp;4. # of tasks not started:</p>\n				<p><br /> <strong>Next Day\'s Plan:</strong></p>', '2014-01-24 12:58:24', '2014-01-24 12:58:24'),
(3, 'Change Request', '<p><strong>Change Requested:</strong></p>\n				<p><strong>&nbsp; &nbsp; &nbsp; Task no: 120</strong></p>\n				<p><strong>&nbsp; &nbsp; &nbsp; Task no: 125</strong></p>\n				<p><strong>Today\'s accomplishment:</strong></p>\n				<p><strong>&nbsp; &nbsp; &nbsp; Task no: 120</strong></p>\n				<ul>\n				<li>Lorem Ipsum is simply dummy text of the printing and typesetting industry</li>\n				<li>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout</li>\n				<li>Contrary to popular belief, Lorem Ipsum is not simply random text</li>\n				</ul>\n				<p>&nbsp; &nbsp; &nbsp;<strong>Task no: 125</strong></p>\n				<ul>\n				<li>Lorem Ipsum is simply dummy text of the printing and typesetting industry</li>\n				<li>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout</li>\n				<li>Contrary to popular belief, Lorem Ipsum is not simply random text</li>\n				</ul>\n				<p><br /> <strong>List of files changed:</strong></p>\n				<ol>\n				<li>index.html</li>\n				<li>style.css</li>\n				<li>contact-us.html</li>\n				</ol>\n				<p>Is code checked in Repository: <strong>Y/N</strong><br /> Is code available in Stager: <strong>Y/N</strong> </p>\n				<p><strong>Blocker/Testing Issues:</strong></p>\n				<p><strong>Milestone Update: &lt; Specify Milestone name here &gt;</strong></p>\n				<p>&nbsp; &nbsp;1. Total tasks:</p>\n				<p>&nbsp; &nbsp;2. # of Work in Progress tasks:</p>\n				<p>&nbsp; &nbsp;3. # of Resolve tasks:</p>\n				<p>&nbsp; &nbsp;4. # of tasks not started:</p>\n				<p><br /> <strong>Next Day\'s Plan:</strong></p>', '2014-01-24 12:58:24', '2014-01-24 12:58:24'),
(4, 'Bug', '<b>Browser version:</b>\n				<br/>\n				<b>OS version:</b>\n				<br/><br/>\n				<b>Url:</b>\n				<br/><br/>\n				<b>What is the test case:</b><br/>\n				<b>What is the expected result:</b><br/>\n				<b>What is the actual result:</b><br/><br/>\n				\n				<b>Is it happening all the time or intermittently:</b><br/>\n				<br/>\n				<b>Attach screenshots:</b>', '2014-01-24 12:58:24', '2014-01-24 12:58:24');

-- --------------------------------------------------------

--
-- Struktur dari tabel `easycases`
--

CREATE TABLE `easycases` (
  `id` mediumint(11) NOT NULL,
  `uniq_id` varchar(64) NOT NULL,
  `case_no` int(11) NOT NULL,
  `case_count` int(11) NOT NULL,
  `project_id` int(11) NOT NULL COMMENT 'Foreign key of "projects"',
  `user_id` int(11) NOT NULL COMMENT 'Foreign key of "users"',
  `updated_by` int(11) NOT NULL,
  `type_id` tinyint(4) NOT NULL COMMENT 'Foreign key of "case_types"',
  `priority` varchar(4) DEFAULT NULL,
  `title` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `estimated_hours` decimal(6,1) NOT NULL,
  `hours` decimal(6,1) NOT NULL,
  `completed_task` int(11) NOT NULL DEFAULT '0',
  `assign_to` int(11) NOT NULL COMMENT 'Foreign Key of "users"',
  `due_date` date DEFAULT NULL,
  `istype` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-Post, 2-Comment',
  `format` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-Files&Details, 2-Details, 3-Files',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-Open, 2-Closed',
  `legend` tinyint(2) NOT NULL COMMENT '1-New 2-Opened, 3-Closed, 4-Start, 5-Resolve, 6- Modified',
  `isactive` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0-Inactive,1-Active',
  `dt_created` datetime NOT NULL,
  `actual_dt_created` datetime NOT NULL,
  `reply_type` int(11) NOT NULL DEFAULT '0' COMMENT '1-> Case Type changes, 2-> Assign to , 3 -> Due Date,4 -> Priority ',
  `is_chrome_extension` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: No, 1: Yes',
  `from_email` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `easycases`
--

INSERT INTO `easycases` (`id`, `uniq_id`, `case_no`, `case_count`, `project_id`, `user_id`, `updated_by`, `type_id`, `priority`, `title`, `message`, `estimated_hours`, `hours`, `completed_task`, `assign_to`, `due_date`, `istype`, `format`, `status`, `legend`, `isactive`, `dt_created`, `actual_dt_created`, `reply_type`, `is_chrome_extension`, `from_email`) VALUES
(1, '81f17053725be27edc36b29cf6f35ee1', 1, 2, 1, 1, 1, 2, '1', 'test', '', '0.0', '0.0', 0, 1, NULL, 1, 2, 1, 5, 1, '2019-03-18 10:00:50', '2019-03-18 08:45:00', 0, 0, 0),
(2, '2a8b19304a075184b51f93316fa275f5', 1, 0, 1, 1, 0, 2, '1', NULL, '', '0.0', '0.0', 0, 0, NULL, 2, 2, 1, 4, 1, '2019-03-18 10:00:00', '2019-03-18 10:00:00', 0, 0, 0),
(3, '0ae88d1ff600b246864e82a12c9f3d14', 1, 0, 1, 1, 0, 2, '1', '', 'Done', '0.0', '2.0', 100, 1, NULL, 2, 2, 1, 5, 1, '2019-03-18 10:00:50', '2019-03-18 10:00:50', 0, 0, 0),
(4, 'bbc31f85077a51d4a6ac9fc768256672', 2, 1, 1, 1, 1, 14, '1', 'Meeting with customer', '', '3.0', '1.0', 0, 1, '2019-03-19', 1, 1, 1, 4, 1, '2019-03-18 10:08:39', '2019-03-18 10:08:31', 0, 0, 0),
(5, '4ce73f789679efa7089276ee3f6b8616', 2, 0, 1, 1, 0, 14, '1', NULL, '', '0.0', '0.0', 0, 0, NULL, 2, 2, 1, 4, 1, '2019-03-18 10:08:39', '2019-03-18 10:08:39', 0, 0, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `easycase_milestones`
--

CREATE TABLE `easycase_milestones` (
  `id` int(11) NOT NULL,
  `easycase_id` int(11) NOT NULL,
  `milestone_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `id_seq` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `email_reminders`
--

CREATE TABLE `email_reminders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email_type` int(11) NOT NULL COMMENT '1:Aaccount Activation, 2:login, 3:Project Creation, 4:User Add, 5:Task Add',
  `cron_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_activities`
--

CREATE TABLE `log_activities` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `log_type_id` int(11) DEFAULT NULL,
  `json_value` text COLLATE utf8_unicode_ci,
  `created` datetime DEFAULT NULL,
  `ip` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data untuk tabel `log_activities`
--

INSERT INTO `log_activities` (`id`, `company_id`, `user_id`, `log_type_id`, `json_value`, `created`, `ip`) VALUES
(1, 1, 1, 1, '{\"company_name\":\"EIN Presales\",\"name\":\"presales.admein\",\"user_type\":\"Free\",\"created\":\"2019-03-18 08:43:43\"}', '2019-03-18 08:43:43', '::1'),
(2, 1, 1, 25, '{\"email\":\"cenna.river@gmail.com\",\"created\":\"2019-03-18 08:44:15\"}', '2019-03-18 08:44:15', '::1'),
(3, 1, 1, 3, '{\"email\":\"cenna.river@gmail.com\",\"name\":\"\",\"created\":\"2019-03-18 09:58:16\"}', '2019-03-18 09:58:16', '::1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_types`
--

CREATE TABLE `log_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data untuk tabel `log_types`
--

INSERT INTO `log_types` (`id`, `name`, `created`) VALUES
(1, 'Account Created', '2013-07-24 17:20:55'),
(3, 'User Deleted', '2013-07-24 17:20:55'),
(4, 'Plan Upgraded', '2013-07-24 17:24:48'),
(5, 'Braintree Profile Created', '2013-07-24 17:24:48'),
(6, 'Credit Card Updated', '2013-07-24 17:24:48'),
(7, 'Subscription Created', '2013-07-24 17:24:48'),
(8, 'Subscription Updated', '2013-07-24 17:24:48'),
(9, 'Invoice Generated', '2013-07-24 17:24:48'),
(10, 'Invoice Failed', '2013-07-24 17:24:48'),
(11, 'Subscription Expired', '2013-07-24 17:24:48'),
(12, 'Subscription canceled', '2013-07-24 17:24:48'),
(13, 'Subscription trial ended', '2013-07-24 17:24:48'),
(14, 'Subscription went active', '2013-07-24 17:24:48'),
(17, 'Invoice Email Sent', '2013-07-24 17:24:48'),
(18, 'Invoice Email Faild ', '2013-07-24 17:24:48'),
(19, 'Cancel subscription notification mail sent ', '2013-07-24 17:24:48'),
(20, 'Instant payment after cancel subscription', '2013-07-24 17:24:48'),
(21, 'Expiry date notification mail sent', '2013-07-24 17:24:48'),
(22, 'Instant payment invoice mail sent ', '2013-07-24 17:24:48'),
(23, 'Instant payment invoice mail faild ', '2013-07-24 17:24:48'),
(24, 'Account confirmed', '2013-09-05 18:41:14'),
(25, 'User invited', '2013-09-06 10:28:25'),
(26, 'User invitation confirmed', '2013-09-06 11:57:00'),
(27, 'User disabled', '2013-09-06 12:17:00'),
(28, 'User enabled', '2013-09-06 12:17:12'),
(29, 'Cancel subscription notification mail faild', '2013-09-06 16:08:33'),
(30, 'Credit Card expired', '2013-09-14 11:38:31'),
(31, 'Credit Card Reminder mail sent', '2013-09-14 11:38:26'),
(32, 'Subscription Payment Failed', '0000-00-00 00:00:00'),
(33, 'Account Deactivated', '0000-00-00 00:00:00'),
(34, 'Account Disable By Admin', '0000-00-00 00:00:00'),
(35, 'Plan downgraded', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mail_tbls`
--

CREATE TABLE `mail_tbls` (
  `id` int(11) NOT NULL,
  `mail` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `milestones`
--

CREATE TABLE `milestones` (
  `id` int(250) NOT NULL,
  `uniq_id` varchar(250) NOT NULL,
  `project_id` int(250) NOT NULL,
  `company_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(250) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `isactive` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0-Inactive, 1-Active',
  `id_seq` tinyint(2) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `os_session_logs`
--

CREATE TABLE `os_session_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_agent` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `os_session_logs`
--

INSERT INTO `os_session_logs` (`id`, `user_id`, `user_agent`) VALUES
(0, 1, '{\"1d4160c118d5527a3d9d2d1af5ec64334117045585c7c8bd7d6425\":\"04ebd3a47e5a87b2eb2fb9f994032e82\"}');

-- --------------------------------------------------------

--
-- Struktur dari tabel `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `uniq_id` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Foreign key of "users"',
  `company_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_name` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(50) NOT NULL,
  `project_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-Internal, 2-External',
  `default_assign` int(255) NOT NULL,
  `isactive` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1- Active, 2- Inactive',
  `dt_created` datetime NOT NULL,
  `dt_updated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `projects`
--

INSERT INTO `projects` (`id`, `uniq_id`, `user_id`, `company_id`, `name`, `short_name`, `description`, `logo`, `project_type`, `default_assign`, `isactive`, `dt_created`, `dt_updated`) VALUES
(1, '7af1b3a86663c18c45932de15f1fd1d6', 1, 1, 'Bank BCA Finance', '001PJ', '', '', 1, 1, 1, '2019-03-18 08:44:05', '2019-03-18 09:59:43');

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_technologies`
--

CREATE TABLE `project_technologies` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL COMMENT 'Foreign key of "projects"',
  `technology_id` smallint(6) NOT NULL COMMENT 'Foreign key of "technologies"'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_templates`
--

CREATE TABLE `project_templates` (
  `id` int(11) NOT NULL,
  `user_id` int(250) NOT NULL,
  `company_id` int(250) NOT NULL,
  `module_name` varchar(250) NOT NULL,
  `is_default` tinyint(4) NOT NULL COMMENT '0-default,1-not',
  `is_active` tinyint(2) NOT NULL COMMENT '1-active,0-inactive',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_template_cases`
--

CREATE TABLE `project_template_cases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(250) NOT NULL,
  `template_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `sort` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `project_users`
--

CREATE TABLE `project_users` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL COMMENT 'Foreign key of "projects"',
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Foreign key of "users"',
  `istype` tinyint(2) NOT NULL DEFAULT '2' COMMENT '1-Admin, 2-Moderator, 3-Viewer',
  `default_email` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0-No, 1-Yes',
  `dt_visited` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `project_users`
--

INSERT INTO `project_users` (`id`, `project_id`, `company_id`, `user_id`, `istype`, `default_email`, `dt_visited`) VALUES
(1, 1, 1, 1, 1, 1, '2019-03-18 10:12:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `save_reports`
--

CREATE TABLE `save_reports` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rpt_type` tinyint(4) NOT NULL COMMENT '1:Task 2:Hour 3:Bug 4:Project',
  `frm_dt` date NOT NULL,
  `to_dt` date NOT NULL,
  `created` datetime NOT NULL,
  `ip` varchar(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `save_reports`
--

INSERT INTO `save_reports` (`id`, `user_id`, `rpt_type`, `frm_dt`, `to_dt`, `created`, `ip`) VALUES
(1, 1, 0, '2019-03-11', '2019-03-18', '2019-03-18 10:03:05', '::1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `plan` int(11) NOT NULL COMMENT '1-Demo, 2-PRO, 3-LITE, 4-PREMIUM',
  `storage` varchar(250) NOT NULL COMMENT 'Mb',
  `project_limit` varchar(100) NOT NULL,
  `user_limit` varchar(100) NOT NULL,
  `milestone_limit` varchar(50) NOT NULL,
  `free_trail_days` smallint(6) NOT NULL,
  `price` smallint(6) NOT NULL,
  `month` smallint(6) NOT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0-Inactive,1-Active',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `plan`, `storage`, `project_limit`, `user_limit`, `milestone_limit`, `free_trail_days`, `price`, `month`, `is_active`, `created`, `modified`) VALUES
(1, 1, 'Unlimited', 'Unlimited', 'Unlimited', 'Unlimited', 0, 0, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `template_module_cases`
--

CREATE TABLE `template_module_cases` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(250) NOT NULL,
  `template_module_id` int(250) NOT NULL COMMENT 'foreign key of template_module',
  `project_id` int(11) NOT NULL COMMENT 'foreign key of Project table.',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `timezones`
--

CREATE TABLE `timezones` (
  `id` int(11) NOT NULL DEFAULT '0',
  `gmt_offset` double DEFAULT '0',
  `dst_offset` double DEFAULT NULL,
  `code` varchar(4) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `timezones`
--

INSERT INTO `timezones` (`id`, `gmt_offset`, `dst_offset`, `code`) VALUES
(1, -12, 0, NULL),
(2, -11, 0, NULL),
(3, -10, 0, 'H'),
(4, -9, 1, 'AK'),
(5, -8, 1, 'P'),
(6, -7, 0, 'M'),
(7, -7, 1, NULL),
(8, -7, 1, 'M'),
(9, -6, 0, NULL),
(10, -6, 1, 'C'),
(11, -6, 1, NULL),
(12, -6, 0, 'C'),
(13, -5, 0, NULL),
(14, -5, 1, 'E'),
(15, -5, 0, 'E'),
(16, -4, 1, 'A'),
(17, -4, 0, NULL),
(18, -4, 1, NULL),
(19, -3.5, 1, 'N'),
(20, -3, 1, NULL),
(21, -3, 0, NULL),
(22, -3, 1, NULL),
(23, -2, 1, NULL),
(24, -1, 1, NULL),
(25, -1, 0, NULL),
(26, 0, 0, NULL),
(27, 0, 1, NULL),
(28, 1, 1, NULL),
(29, 1, 1, NULL),
(30, 1, 1, NULL),
(31, 1, 1, NULL),
(32, 1, 0, NULL),
(33, 2, 1, NULL),
(34, 2, 1, NULL),
(35, 2, 1, NULL),
(36, 2, 0, NULL),
(37, 2, 1, NULL),
(38, 2, 0, NULL),
(39, 3, 1, NULL),
(40, 3, 0, NULL),
(41, 3, 1, NULL),
(42, 3, 0, NULL),
(43, 3.5, 1, NULL),
(44, 4, 0, NULL),
(45, 4, 1, NULL),
(46, 4.5, 0, NULL),
(47, 5, 1, NULL),
(48, 5, 0, NULL),
(49, 5.5, 0, NULL),
(50, 5.75, 0, NULL),
(51, 6, 1, NULL),
(52, 6, 0, NULL),
(53, 6, 0, NULL),
(54, 6.5, 0, NULL),
(55, 7, 0, NULL),
(56, 7, 1, NULL),
(57, 8, 0, NULL),
(58, 8, 1, NULL),
(59, 8, 0, NULL),
(60, 8, 0, NULL),
(61, 8, 0, NULL),
(62, 9, 0, NULL),
(63, 9, 0, NULL),
(64, 9, 1, NULL),
(65, 9.5, 1, NULL),
(66, 9.5, 0, NULL),
(67, 10, 0, NULL),
(68, 10, 1, NULL),
(69, 10, 0, NULL),
(70, 10, 1, NULL),
(71, 10, 1, NULL),
(72, 11, 0, NULL),
(73, 12, 1, NULL),
(74, 12, 0, NULL),
(75, 13, 0, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `timezone_names`
--

CREATE TABLE `timezone_names` (
  `id` int(11) NOT NULL,
  `gmt` varchar(15) NOT NULL,
  `zone` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `timezone_names`
--

INSERT INTO `timezone_names` (`id`, `gmt`, `zone`) VALUES
(1, '(GMT-12:00)', 'International Date Line West'),
(2, '(GMT-11:00)', 'Midway Island Samoa'),
(3, '(GMT-10:00)', 'Hawaii'),
(4, '(GMT-9:00)', 'Alaska'),
(5, '(GMT-08:00)', 'Pacific Time (US & Canada); Tijuana'),
(6, '(GMT-07:00)', 'Arizona'),
(7, '(GMT-07:00)', 'Chihuahua, La Paz, Mazatlan'),
(8, '(GMT-07:00)', 'Mountain Time (US & Canada)'),
(9, '(GMT-06:00)', 'Central America'),
(10, '(GMT-06:00)', 'Central Time (US & Canada)'),
(11, '(GMT-06:00)', 'Guadalajara, Mexico City, Monterrey'),
(12, '(GMT-06:00)', 'Saskatchewan'),
(13, '(GMT-05:00)', 'Bogota, Lime, Quito'),
(14, '(GMT-05:00)', 'Eastern Time (US & Canada)'),
(15, '(GMT-05:00)', 'Indiana (East)'),
(16, '(GMT-05:04)', 'Atlantic Time (Canada)'),
(17, '(GMT-04:00)', 'Caracas, La Paz'),
(18, '(GMT-04:00)', 'Santiago'),
(19, '(GMT-03:30)', 'Newfoundland'),
(20, '(GMT-03:00)', 'Brasilia'),
(21, '(GMT-03:00)', 'Buenos Aires, Georgetown'),
(22, '(GMT-03:00)', 'Greenland'),
(23, '(GMT-02:00)', 'Mid-Atlantic'),
(24, '(GMT-01:00)', 'Azores'),
(25, '(GMT-01:00)', 'Cape Verde Is.'),
(26, '(GMT)', 'Casablanca, Monrovia'),
(27, '(GMT)', 'Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London'),
(28, '(GMT+01:00)', 'Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna'),
(29, '(GMT+01:00)', 'Belgrade, Bratislava, Budapest, Ljubljana, Prague'),
(30, '(GMT+01:00)', 'Brussels, Copenhagen, Madrid, Paris'),
(31, '(GMT+01:00)', 'Sarajevo, Skopje, Warsaw, Zagreb'),
(32, '(GMT+01:00)', 'West Central Africa'),
(33, '(GMT+02:00)', 'Athens, Istanbul, Minsk'),
(34, '(GMT+02:00)', 'Bucharest'),
(35, '(GMT+02:00)', 'Cairo'),
(36, '(GMT+02:00)', 'Harare, Pretoria'),
(37, '(GMT+02:00)', 'Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius'),
(38, '(GMT+02:00)', 'Jerusalem'),
(39, '(GMT+03:00)', 'Baghdad'),
(40, '(GMT+03:00)', 'Kuwait, Riyadh'),
(41, '(GMT+03:00)', 'Moscow, St. Petersburg, Volgograd'),
(42, '(GMT+03:00)', 'Nairobi'),
(43, '(GMT+03:30)', 'Tehran'),
(44, '(GMT+04:00)', 'Abu Dhabi, Muscat'),
(45, '(GMT+04:00)', 'Baku, Tbilisi, Yerevan'),
(46, '(GMT+04:30)', 'Kabul'),
(47, '(GMT+05:00)', 'Ekaterinburg'),
(48, '(GMT+05:00)', 'Islamabad, Karachi, Tashkent'),
(49, '(GMT+05:30)', 'Chennai, Kolkata, Mumbai, New Delhi'),
(50, '(GMT+05.75)', 'Kathmandu'),
(51, '(GMT+06:00)', 'Almaty, Novosibirsk'),
(52, '(GMT+06:00)', 'Astana, Dhaka'),
(53, '(GMT+06:00)', 'Sri Jayawardenepura'),
(54, '(GMT+06:30)', 'Rangoon'),
(55, '(GMT+07:00)', 'Bangkok, Hanoi, Jakarta'),
(56, '(GMT+07:00)', 'Krasnoyarsk'),
(57, '(GMT+08:00)', 'Beijing, Chongging, Hong Kong, Urumgi'),
(58, '(GMT+08:00)', 'Irkutsk, Ulaan Bataar'),
(59, '(GMT+08:00)', 'Kuala Lumpur, Singapore'),
(60, '(GMT+08:00)', 'Perth'),
(61, '(GMT+08:00)', 'Taipei'),
(62, '(GMT+09:00)', 'Osaka, Sapporo, Tokyo'),
(63, '(GMT+09:00)', 'Seoul'),
(64, '(GMT+09:00)', 'Yakutsk'),
(65, '(GMT+09:30)', 'Adelaide'),
(66, '(GMT+09:30)', 'Darwin'),
(67, '(GMT+10:00)', 'Brisbane'),
(68, '(GMT+10:00)', 'Canberra, Melbourne, Sydney'),
(69, '(GMT+10:00)', 'Guam, Port Moresby'),
(70, '(GMT+10:00)', 'Hobart'),
(71, '(GMT+10:00)', 'Vladivostok'),
(72, '(GMT+11:00)', 'Magadan, Solomon Is., New Caledonia'),
(73, '(GMT+12:00)', 'Auckland, Wellington'),
(74, '(GMT+12:00)', 'Figi, Kamchatka, Marshall Is.'),
(75, '(GMT+13:00)', 'Nuku\'alofa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) NOT NULL,
  `btsubscription_id` varchar(10) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `status` varchar(100) NOT NULL,
  `subscription_price` float(4,2) DEFAULT NULL,
  `amt` float(7,2) NOT NULL,
  `discount` float(4,2) DEFAULT NULL,
  `addon` float(4,2) DEFAULT NULL,
  `transaction_type` varchar(100) NOT NULL,
  `invoice_mail_flag` tinyint(2) DEFAULT '0',
  `invoice_id` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `ip` varchar(20) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `types`
--

CREATE TABLE `types` (
  `id` tinyint(4) NOT NULL,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `short_name` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `seq_order` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `types`
--

INSERT INTO `types` (`id`, `company_id`, `short_name`, `name`, `seq_order`) VALUES
(1, 0, 'bug', 'Bug', 2),
(2, 0, 'dev', 'Development', 1),
(3, 0, 'enh', 'Enhancement', 6),
(4, 0, 'rnd', 'Research n Do', 7),
(5, 0, 'qa', 'Quality Assurance', 9),
(6, 0, 'unt', 'Unit Testing', 10),
(7, 0, 'mnt', 'Maintenance', 8),
(8, 0, 'oth', 'Others', 12),
(9, 0, 'rel', 'Release', 11),
(10, 0, 'upd', 'Update', 3),
(11, 0, 'idea', 'Idea', 5),
(12, 0, 'cr', 'Change Request', 4),
(13, 1, '1pa', '1. PRE - ASSESMENT', 0),
(14, 1, '2as', '2. ASSESMENT', 0),
(15, 1, '3poc', '3. POC/UAT', 0),
(16, 1, '4lt', '4. LIVE TESTING', 0),
(17, 1, '5ap', '5. APPROVAL', 0),
(18, 1, '6imp', '6. IMPLEMENTATION', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `type_companies`
--

CREATE TABLE `type_companies` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `type_companies`
--

INSERT INTO `type_companies` (`id`, `company_id`, `type_id`) VALUES
(24, 1, 13),
(23, 1, 14),
(22, 1, 15),
(21, 1, 16),
(20, 1, 17),
(19, 1, 18);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `uniq_id` varchar(64) NOT NULL,
  `btprofile_id` varchar(100) DEFAULT NULL,
  `usersubscription_id` int(10) DEFAULT NULL,
  `credit_cardtoken` varchar(100) DEFAULT NULL,
  `card_number` varchar(255) DEFAULT NULL COMMENT 'Only last 4degit of card',
  `expiry_date` varchar(255) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(64) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `is_beta` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1 For beta user ,0 - Default',
  `last_name` varchar(100) DEFAULT NULL,
  `short_name` varchar(8) DEFAULT NULL,
  `istype` tinyint(2) NOT NULL DEFAULT '3' COMMENT '1-Super Admin, 2-Internal User, 3-External User',
  `photo` varchar(50) DEFAULT NULL,
  `photo_reset` varchar(50) DEFAULT NULL,
  `isactive` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1 - ACTIVE, 2 - INACTIVE . 2-Disabled ,3- Deleted user',
  `timezone_id` smallint(6) DEFAULT '26',
  `isemail` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1-Send Email, 0-Don''t Send',
  `is_agree` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0: No, 1: Yes',
  `usersub_type` tinyint(4) DEFAULT '0' COMMENT '0->Free User, 1->Paid User',
  `est_billing_amount` float(10,2) DEFAULT '0.00',
  `dt_created` datetime NOT NULL,
  `dt_updated` datetime DEFAULT NULL,
  `dt_last_login` datetime DEFAULT NULL,
  `dt_last_logout` datetime DEFAULT NULL,
  `query_string` varchar(100) DEFAULT NULL,
  `update_email` varchar(150) NOT NULL,
  `update_random` varchar(150) NOT NULL,
  `gaccess_token` text,
  `ip` varchar(15) DEFAULT NULL,
  `sig` varchar(100) NOT NULL,
  `desk_notify` tinyint(4) NOT NULL DEFAULT '1',
  `active_dashboard_tab` int(11) NOT NULL DEFAULT '7' COMMENT 'Sum of Binary values which will show tabs accordingly',
  `is_moderator` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0: No grant privilege, 1: Yes',
  `show_default_inner` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `uniq_id`, `btprofile_id`, `usersubscription_id`, `credit_cardtoken`, `card_number`, `expiry_date`, `email`, `password`, `name`, `is_beta`, `last_name`, `short_name`, `istype`, `photo`, `photo_reset`, `isactive`, `timezone_id`, `isemail`, `is_agree`, `usersub_type`, `est_billing_amount`, `dt_created`, `dt_updated`, `dt_last_login`, `dt_last_logout`, `query_string`, `update_email`, `update_random`, `gaccess_token`, `ip`, `sig`, `desk_notify`, `active_dashboard_tab`, `is_moderator`, `show_default_inner`) VALUES
(1, 'ce0d71447ce806b96355c078b626f60c', '', NULL, '', '', '', 'presales.admein@gmail.com', '04ebd3a47e5a87b2eb2fb9f994032e82', 'presales.admein', 0, '', 'PR', 2, NULL, NULL, 1, 55, 1, 1, 0, 0.00, '2019-03-18 08:43:43', '2019-03-18 08:43:43', '2019-03-18 08:43:44', NULL, '', '', '', '', '::1', '', 1, 7, 0, 0),
(2, '8d8377c7f031a604af3200ee0aadfad7', NULL, NULL, NULL, NULL, NULL, 'cenna.river@gmail.com', NULL, '', 0, NULL, NULL, 3, NULL, NULL, 2, 26, 1, 1, 0, 0.00, '2019-03-18 08:44:15', NULL, NULL, NULL, NULL, '', '', NULL, NULL, '', 1, 7, 0, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_infos`
--

CREATE TABLE `user_infos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `access_token` text,
  `is_google_signup` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_invitations`
--

CREATE TABLE `user_invitations` (
  `id` int(11) NOT NULL,
  `invitor_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `user_type` tinyint(2) NOT NULL DEFAULT '3',
  `project_id` text COMMENT 'Comma separated value of project ids',
  `user_id` int(11) NOT NULL,
  `is_active` tinyint(2) NOT NULL DEFAULT '1',
  `qstr` varchar(100) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_logins`
--

CREATE TABLE `user_logins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user_logins`
--

INSERT INTO `user_logins` (`id`, `user_id`, `created`) VALUES
(1, 1, '2014-09-15 09:59:39'),
(2, 1, '2014-09-15 10:07:08'),
(3, 1, '2014-09-15 10:10:52'),
(4, 1, '2014-09-17 10:45:21'),
(5, 1, '2014-09-18 05:41:54'),
(6, 1, '2019-03-18 08:43:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_notifications`
--

CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-Email',
  `value` tinyint(4) NOT NULL COMMENT '0-None, 1-Daily, 2-Weekly, 3-Monthly',
  `due_val` tinyint(4) NOT NULL COMMENT '0-Not,1-send',
  `new_case` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `reply_case` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `case_status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0-No, 1-Yes',
  `weekly_usage_alert` tinyint(2) NOT NULL DEFAULT '1' COMMENT 'Only for owner/admins 1-> Default on 0-> Off '
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user_notifications`
--

INSERT INTO `user_notifications` (`id`, `user_id`, `type`, `value`, `due_val`, `new_case`, `reply_case`, `case_status`, `weekly_usage_alert`) VALUES
(1, 1, 1, 1, 1, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `subscription_id` tinyint(4) NOT NULL,
  `storage` varchar(50) NOT NULL,
  `project_limit` varchar(50) NOT NULL,
  `user_limit` varchar(50) NOT NULL,
  `milestone_limit` varchar(50) NOT NULL,
  `free_trail_days` varchar(15) NOT NULL,
  `price` float(10,2) NOT NULL DEFAULT '0.00',
  `month` smallint(6) NOT NULL,
  `amt_due` float(10,2) NOT NULL DEFAULT '0.00',
  `coupon_code` varchar(100) DEFAULT NULL,
  `btsubscription_id` varchar(100) DEFAULT NULL,
  `btprofile_id` varchar(255) DEFAULT NULL,
  `creditcard_token` varchar(255) DEFAULT '',
  `payment_status` tinyint(2) DEFAULT '0',
  `discount` float(10,2) DEFAULT NULL,
  `is_cancel` tinyint(2) DEFAULT '0' COMMENT '1->Cancelled',
  `is_sub_upgraded_bt` tinyint(2) DEFAULT '0',
  `cancel_mail_flag` tinyint(2) DEFAULT '0',
  `no_of_atmpt_upgrd` tinyint(2) DEFAULT NULL,
  `is_free` tinyint(2) NOT NULL DEFAULT '0',
  `is_updown` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0->Cancelled , 1-> Upgraded ,2-> Downgrad',
  `sub_start_date` datetime DEFAULT NULL,
  `next_billing_date` date DEFAULT NULL,
  `cancel_date` date DEFAULT NULL,
  `created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user_subscriptions`
--

INSERT INTO `user_subscriptions` (`id`, `user_id`, `company_id`, `subscription_id`, `storage`, `project_limit`, `user_limit`, `milestone_limit`, `free_trail_days`, `price`, `month`, `amt_due`, `coupon_code`, `btsubscription_id`, `btprofile_id`, `creditcard_token`, `payment_status`, `discount`, `is_cancel`, `is_sub_upgraded_bt`, `cancel_mail_flag`, `no_of_atmpt_upgrd`, `is_free`, `is_updown`, `sub_start_date`, `next_billing_date`, `cancel_date`, `created`) VALUES
(1, 1, 1, 1, 'Unlimited', 'Unlimited', 'Unlimited', 'Unlimited', '0', 0.00, 0, 0.00, NULL, NULL, NULL, '', 0, NULL, 0, 0, 0, NULL, 1, 0, NULL, NULL, NULL, '2019-03-18 08:43:43');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `archives`
--
ALTER TABLE `archives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `easycase_id` (`easycase_id`),
  ADD KEY `case_file_id` (`case_file_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type` (`type`),
  ADD KEY `company_id` (`company_id`);

--
-- Indeks untuk tabel `case_actions`
--
ALTER TABLE `case_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `easycase_id` (`easycase_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `action` (`action`);

--
-- Indeks untuk tabel `case_activities`
--
ALTER TABLE `case_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `easycase_id` (`easycase_id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `case_no` (`case_no`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type` (`type`),
  ADD KEY `isactive` (`isactive`);

--
-- Indeks untuk tabel `case_files`
--
ALTER TABLE `case_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `easycase_id` (`easycase_id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `isactive` (`isactive`);

--
-- Indeks untuk tabel `case_file_drives`
--
ALTER TABLE `case_file_drives`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `case_filters`
--
ALTER TABLE `case_filters`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `case_recents`
--
ALTER TABLE `case_recents`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `case_settings`
--
ALTER TABLE `case_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `case_templates`
--
ALTER TABLE `case_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indeks untuk tabel `case_user_emails`
--
ALTER TABLE `case_user_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `easycase_id` (`easycase_id`,`user_id`);

--
-- Indeks untuk tabel `case_user_views`
--
ALTER TABLE `case_user_views`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_active` (`is_active`);
ALTER TABLE `companies` ADD FULLTEXT KEY `uniq_id` (`uniq_id`);

--
-- Indeks untuk tabel `company_users`
--
ALTER TABLE `company_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_type` (`user_type`),
  ADD KEY `is_active` (`is_active`);
ALTER TABLE `company_users` ADD FULLTEXT KEY `company_uniq_id` (`company_uniq_id`);

--
-- Indeks untuk tabel `custom_filters`
--
ALTER TABLE `custom_filters`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `dailyupdate_notifications`
--
ALTER TABLE `dailyupdate_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `daily_updates`
--
ALTER TABLE `daily_updates`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `default_project_templates`
--
ALTER TABLE `default_project_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `default_project_template_cases`
--
ALTER TABLE `default_project_template_cases`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `default_templates`
--
ALTER TABLE `default_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `easycases`
--
ALTER TABLE `easycases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uniq_id` (`uniq_id`),
  ADD KEY `case_no` (`case_no`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `isactive` (`isactive`),
  ADD KEY `legend` (`legend`),
  ADD KEY `status` (`status`),
  ADD KEY `format` (`format`),
  ADD KEY `istype` (`istype`),
  ADD KEY `assign_to` (`assign_to`),
  ADD KEY `priority` (`priority`),
  ADD KEY `type_id` (`type_id`);

--
-- Indeks untuk tabel `easycase_milestones`
--
ALTER TABLE `easycase_milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `easycase_id` (`easycase_id`),
  ADD KEY `milestone_id` (`milestone_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `email_reminders`
--
ALTER TABLE `email_reminders`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `log_activities`
--
ALTER TABLE `log_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `log_types`
--
ALTER TABLE `log_types`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mail_tbls`
--
ALTER TABLE `mail_tbls`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `milestones`
--
ALTER TABLE `milestones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_id` (`uniq_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indeks untuk tabel `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uniq_id` (`uniq_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `isactive` (`isactive`),
  ADD KEY `project_type` (`project_type`),
  ADD KEY `company_id` (`company_id`);

--
-- Indeks untuk tabel `project_technologies`
--
ALTER TABLE `project_technologies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `tech_proj_id` (`technology_id`);

--
-- Indeks untuk tabel `project_templates`
--
ALTER TABLE `project_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `project_template_cases`
--
ALTER TABLE `project_template_cases`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `project_users`
--
ALTER TABLE `project_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `istype` (`istype`),
  ADD KEY `company_id` (`company_id`);

--
-- Indeks untuk tabel `save_reports`
--
ALTER TABLE `save_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `template_module_cases`
--
ALTER TABLE `template_module_cases`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `timezones`
--
ALTER TABLE `timezones`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `timezone_names`
--
ALTER TABLE `timezone_names`
  ADD KEY `id` (`id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `type_companies`
--
ALTER TABLE `type_companies`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_id_2` (`uniq_id`),
  ADD KEY `uniq_id` (`uniq_id`),
  ADD KEY `istype` (`istype`),
  ADD KEY `isactive` (`isactive`),
  ADD KEY `isemail` (`isemail`),
  ADD KEY `timezone_id` (`timezone_id`);

--
-- Indeks untuk tabel `user_infos`
--
ALTER TABLE `user_infos`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user_invitations`
--
ALTER TABLE `user_invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `user_logins`
--
ALTER TABLE `user_logins`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `subscription_id` (`subscription_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `addons`
--
ALTER TABLE `addons`
  MODIFY `id` tinyint(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `archives`
--
ALTER TABLE `archives`
  MODIFY `id` int(250) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `case_actions`
--
ALTER TABLE `case_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `case_activities`
--
ALTER TABLE `case_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `case_files`
--
ALTER TABLE `case_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `case_file_drives`
--
ALTER TABLE `case_file_drives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `case_filters`
--
ALTER TABLE `case_filters`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `case_recents`
--
ALTER TABLE `case_recents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `case_settings`
--
ALTER TABLE `case_settings`
  MODIFY `id` int(250) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `case_templates`
--
ALTER TABLE `case_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `case_user_emails`
--
ALTER TABLE `case_user_emails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `company_users`
--
ALTER TABLE `company_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `custom_filters`
--
ALTER TABLE `custom_filters`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `dailyupdate_notifications`
--
ALTER TABLE `dailyupdate_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `daily_updates`
--
ALTER TABLE `daily_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `default_project_templates`
--
ALTER TABLE `default_project_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `default_project_template_cases`
--
ALTER TABLE `default_project_template_cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `default_templates`
--
ALTER TABLE `default_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `easycases`
--
ALTER TABLE `easycases`
  MODIFY `id` mediumint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `easycase_milestones`
--
ALTER TABLE `easycase_milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `email_reminders`
--
ALTER TABLE `email_reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `log_activities`
--
ALTER TABLE `log_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `log_types`
--
ALTER TABLE `log_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `mail_tbls`
--
ALTER TABLE `mail_tbls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `milestones`
--
ALTER TABLE `milestones`
  MODIFY `id` int(250) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `project_technologies`
--
ALTER TABLE `project_technologies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `project_templates`
--
ALTER TABLE `project_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `project_template_cases`
--
ALTER TABLE `project_template_cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `save_reports`
--
ALTER TABLE `save_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `template_module_cases`
--
ALTER TABLE `template_module_cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `types`
--
ALTER TABLE `types`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `type_companies`
--
ALTER TABLE `type_companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `user_infos`
--
ALTER TABLE `user_infos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user_invitations`
--
ALTER TABLE `user_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `user_logins`
--
ALTER TABLE `user_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
