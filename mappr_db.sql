-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 16, 2013 at 09:59 AM
-- Server version: 5.5.25
-- PHP Version: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `mappr_v2`
--

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `units` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `issueOrderNum` int(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectId` (`projectId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=245 ;

-- --------------------------------------------------------

--
-- Table structure for table `issuesIssueTags`
--

CREATE TABLE `issuesIssueTags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tagId` int(11) NOT NULL,
  `issueId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tagId` (`tagId`),
  KEY `issueId` (`issueId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3894 ;

-- --------------------------------------------------------

--
-- Table structure for table `issueTags`
--

CREATE TABLE `issueTags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=220 ;

-- --------------------------------------------------------

--
-- Table structure for table `nodes`
--

CREATE TABLE `nodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectId` int(11) NOT NULL,
  `issueInd` int(4) NOT NULL,
  `name` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `votes` int(3) NOT NULL DEFAULT '1',
  `userId` int(11) NOT NULL,
  `units` varchar(512) NOT NULL,
  `issueType` tinyint(2) NOT NULL,
  `isRevisit` tinyint(1) NOT NULL,
  `notes` text NOT NULL,
  `updateTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectId` (`projectId`),
  KEY `projectId_2` (`projectId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=469 ;

-- --------------------------------------------------------

--
-- Table structure for table `nodesNodeTags`
--

CREATE TABLE `nodesNodeTags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tagId` int(11) NOT NULL,
  `nodeId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10672 ;

-- --------------------------------------------------------

--
-- Table structure for table `nodeTags`
--

CREATE TABLE `nodeTags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=239 ;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `url` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `question` text NOT NULL,
  `numberOfIssuesPerParticipant` int(4) NOT NULL,
  `numberOfFromIssuesPerParticipant` int(4) NOT NULL,
  `projectState` tinyint(1) NOT NULL,
  `video_embed` text NOT NULL,
  `link_mapping_info` text NOT NULL,
  `video_embed_link_mapping` text NOT NULL,
  `admin_email` varchar(128) NOT NULL,
  `registration_message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `projectsIssueTags`
--

CREATE TABLE `projectsIssueTags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tagId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tagId` (`tagId`),
  KEY `projectId` (`projectId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=651 ;

-- --------------------------------------------------------

--
-- Table structure for table `projectStates`
--

CREATE TABLE `projectStates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `link` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `userDeletedToNodes`
--

CREATE TABLE `userDeletedToNodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `nodeId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectId` (`projectId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `userLinks`
--

CREATE TABLE `userLinks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `issueFromId` int(11) NOT NULL,
  `issueToId` int(11) NOT NULL,
  `comment` text NOT NULL,
  `sign` tinyint(1) NOT NULL DEFAULT '-9',
  `modified` datetime NOT NULL,
  `num_observers` int(2) NOT NULL DEFAULT '1',
  `tot_observers` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `projectId` (`projectId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7807 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(64) NOT NULL,
  `last_name` varchar(64) NOT NULL,
  `user_email` varchar(128) NOT NULL,
  `user_pass` varchar(60) NOT NULL,
  `user_register_pass` varchar(60) NOT NULL,
  `user_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_last_login` datetime NOT NULL,
  `isAdministrator` tinyint(1) NOT NULL,
  `isConfirmed` tinyint(1) NOT NULL DEFAULT '1',
  `category` int(11) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=120 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES(103, 'First', 'User', 'new@vibrantdatalabs.org', '$P$BGrh4i6vauoa4UiueAtgFbA7kugIz11', 'dCjyUiOK', '2012-12-22 12:03:31', '2012-12-22 12:03:31', '2012-12-22 12:03:31', 1, 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `usersChosenFromNodes`
--

CREATE TABLE `usersChosenFromNodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `nodeId` int(11) NOT NULL,
  `isDone` tinyint(1) NOT NULL,
  `isBegun` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectId` (`projectId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=602 ;

-- --------------------------------------------------------

--
-- Table structure for table `usersFromNodes`
--

CREATE TABLE `usersFromNodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `nodeId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectId` (`projectId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=304 ;

-- --------------------------------------------------------

--
-- Table structure for table `usersProjects`
--

CREATE TABLE `usersProjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `projectStatus` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectId` (`projectId`),
  KEY `usersprojects_ibfk_2` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=110 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `issues`
--
ALTER TABLE `issues`
  ADD CONSTRAINT `issues_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `issuesIssueTags`
--
ALTER TABLE `issuesIssueTags`
  ADD CONSTRAINT `issuesissuetags_ibfk_1` FOREIGN KEY (`issueId`) REFERENCES `issues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nodes`
--
ALTER TABLE `nodes`
  ADD CONSTRAINT `nodes_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `projectsIssueTags`
--
ALTER TABLE `projectsIssueTags`
  ADD CONSTRAINT `projectsissuetags_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userLinks`
--
ALTER TABLE `userLinks`
  ADD CONSTRAINT `userlinks_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usersFromNodes`
--
ALTER TABLE `usersFromNodes`
  ADD CONSTRAINT `usersfromnodes_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `usersProjects`
--
ALTER TABLE `usersProjects`
  ADD CONSTRAINT `usersprojects_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usersprojects_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
