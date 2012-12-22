-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 22, 2012 at 12:14 PM
-- Server version: 5.1.44
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mappr_os`
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `issues`
--


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `issuesIssueTags`
--


-- --------------------------------------------------------

--
-- Table structure for table `issueTags`
--

CREATE TABLE `issueTags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `issueTags`
--


-- --------------------------------------------------------

--
-- Table structure for table `links`
--

CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `linkId` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `projectId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `issueFromId` int(11) NOT NULL,
  `issueToId` int(11) NOT NULL,
  `sign` tinyint(1) NOT NULL,
  `strengthShort` tinyint(1) NOT NULL,
  `strengthLong` tinyint(1) NOT NULL,
  `strength` decimal(2,1) NOT NULL,
  `strengthCertainty` tinyint(1) NOT NULL,
  `strengthComment` text NOT NULL,
  `comments` text NOT NULL,
  `isFlagged` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectId` (`projectId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `links`
--


-- --------------------------------------------------------

--
-- Table structure for table `linksAttributes`
--

CREATE TABLE `linksAttributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `min` tinyint(1) NOT NULL,
  `max` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `linksAttributes`
--


-- --------------------------------------------------------

--
-- Table structure for table `linksAttributesValues`
--

CREATE TABLE `linksAttributesValues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `attributeId` int(11) NOT NULL,
  `attributeValue` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`attributeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `linksAttributesValues`
--


-- --------------------------------------------------------

--
-- Table structure for table `linksProjectsAttributes`
--

CREATE TABLE `linksProjectsAttributes` (
  `projectId` int(11) NOT NULL,
  `linkAttrId` int(11) NOT NULL,
  KEY `projectId` (`projectId`,`linkAttrId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `linksProjectsAttributes`
--


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
  `isGoal` tinyint(1) NOT NULL,
  `notes` text NOT NULL,
  `updateTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectId` (`projectId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `nodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `nodesAttributes`
--

CREATE TABLE `nodesAttributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `min` tinyint(1) NOT NULL,
  `max` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `nodesAttributes`
--


-- --------------------------------------------------------

--
-- Table structure for table `nodesAttributesValues`
--

CREATE TABLE `nodesAttributesValues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `attributeId` int(11) NOT NULL,
  `attributeValue` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`attributeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `nodesAttributesValues`
--


-- --------------------------------------------------------

--
-- Table structure for table `nodesNodeTags`
--

CREATE TABLE `nodesNodeTags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tagId` int(11) NOT NULL,
  `nodeId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `nodesNodeTags`
--


-- --------------------------------------------------------

--
-- Table structure for table `nodesProjectsAttributes`
--

CREATE TABLE `nodesProjectsAttributes` (
  `projectId` int(11) NOT NULL,
  `nodeAttrId` int(11) NOT NULL,
  KEY `projectId` (`projectId`,`nodeAttrId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nodesProjectsAttributes`
--


-- --------------------------------------------------------

--
-- Table structure for table `nodeTags`
--

CREATE TABLE `nodeTags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `nodeTags`
--


-- --------------------------------------------------------

--
-- Table structure for table `possible_participants`
--

CREATE TABLE `possible_participants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `organization` varchar(128) NOT NULL,
  `website` varchar(128) NOT NULL,
  `expertise` text NOT NULL,
  `interest` text NOT NULL,
  `twitter_handle` varchar(128) NOT NULL,
  `did_map` tinyint(1) NOT NULL,
  `links_mapped` int(6) NOT NULL,
  `user_last_login` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `possible_participants`
--


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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `projects`
--


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `projectsIssueTags`
--


-- --------------------------------------------------------

--
-- Table structure for table `projectStates`
--

CREATE TABLE `projectStates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `link` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `projectStates`
--

INSERT INTO `projectStates` VALUES(1, 'Issue Listing', 'issues/listing_instructions');
INSERT INTO `projectStates` VALUES(2, 'Issue List Curation', 'issues/curation');
INSERT INTO `projectStates` VALUES(3, 'Remote Link Mapping', 'links/mapping');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `userDeletedToNodes`
--


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
  `strength` tinyint(1) NOT NULL DEFAULT '-9',
  `certainty` tinyint(1) NOT NULL DEFAULT '-9',
  `modified` datetime NOT NULL,
  `num_observers` int(2) NOT NULL DEFAULT '1',
  `tot_observers` int(2) NOT NULL DEFAULT '1',
  `min_strength` int(1) NOT NULL,
  `max_strength` int(1) NOT NULL,
  `med_strength` int(1) NOT NULL,
  `min_sign` int(1) NOT NULL,
  `max_sign` int(1) NOT NULL,
  `med_sign` int(1) NOT NULL,
  `min_certainty` int(1) NOT NULL,
  `max_certainty` int(1) NOT NULL,
  `med_certainty` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectId` (`projectId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `userLinks`
--


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=104 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` VALUES(103, 'First', 'User', 'new@vibrantdatalabs.org', '$P$BGrh4i6vauoa4UiueAtgFbA7kugIz11', 'dCjyUiOK', '2012-12-22 12:03:31', '2012-12-22 12:03:31', '2012-12-22 12:03:31', 1, 1, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `usersAttributes`
--

CREATE TABLE `usersAttributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `min` tinyint(1) NOT NULL,
  `max` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `usersAttributes`
--


-- --------------------------------------------------------

--
-- Table structure for table `usersAttributesValues`
--

CREATE TABLE `usersAttributesValues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `attributeId` int(11) NOT NULL,
  `attributeValue` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`,`attributeId`),
  KEY `projectId` (`projectId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `usersAttributesValues`
--


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `usersChosenFromNodes`
--


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `usersFromNodes`
--


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `usersProjects`
--


-- --------------------------------------------------------

--
-- Table structure for table `usersProjectsAttributes`
--

CREATE TABLE `usersProjectsAttributes` (
  `projectId` int(11) NOT NULL,
  `userAttrId` int(11) NOT NULL,
  KEY `projectId` (`projectId`,`userAttrId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `usersProjectsAttributes`
--


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
-- Constraints for table `links`
--
ALTER TABLE `links`
  ADD CONSTRAINT `links_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `linksProjectsAttributes`
--
ALTER TABLE `linksProjectsAttributes`
  ADD CONSTRAINT `linksprojectsattributes_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nodes`
--
ALTER TABLE `nodes`
  ADD CONSTRAINT `nodes_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nodesProjectsAttributes`
--
ALTER TABLE `nodesProjectsAttributes`
  ADD CONSTRAINT `nodesprojectsattributes_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `usersAttributesValues`
--
ALTER TABLE `usersAttributesValues`
  ADD CONSTRAINT `usersattributesvalues_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

--
-- Constraints for table `usersProjectsAttributes`
--
ALTER TABLE `usersProjectsAttributes`
  ADD CONSTRAINT `usersprojectsattributes_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
