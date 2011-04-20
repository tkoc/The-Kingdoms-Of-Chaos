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
CREATE TABLE `ActionLog` (
  `id` int(11) NOT NULL auto_increment,
  `Action` int(11) default '0',
  `DoneBy` int(11) default '-1',
  `DoneAgainst` int(11) default '-1',
  `Operation` int(11) default '-1',
  `Success` enum('true','false') character set latin1 default 'false',
  `Tick` int(11) default '-1',
  `description` text,
  `timestamp` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ActiveThieveryOps` (
  `ID` int(11) NOT NULL auto_increment,
  `pID` int(11) default NULL,
  `ticks` int(11) default NULL,
  `thieveryOperationID` int(11) default NULL,
  `doneByID` int(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `AdminLogin` (
  `loginID` int(11) NOT NULL auto_increment,
  `ip` varchar(12) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `loggedon` enum('N','Y') character set latin1 NOT NULL default 'N',
  `userID` int(11) NOT NULL default '0',
  `admincID` varchar(16) default NULL,
  PRIMARY KEY  (`loginID`),
  KEY `userID` (`userID`),
  KEY `admincID` (`admincID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `adminNews` (
  `adminNewsID` int(11) NOT NULL auto_increment,
  `timestamp` datetime default NULL,
  `news` text,
  `userID` int(11) NOT NULL default '0',
  `header` varchar(255) default NULL,
  PRIMARY KEY  (`adminNewsID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Army` (
  `armyID` int(11) NOT NULL auto_increment,
  `pID` int(11) NOT NULL default '0',
  `AttackID` int(11) NOT NULL default '0',
  `mID` int(11) NOT NULL default '0',
  `num` int(11) NOT NULL default '0',
  `ticks` int(11) NOT NULL default '0',
  PRIMARY KEY  (`armyID`,`pID`,`AttackID`,`mID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Attack` (
  `attackID` int(11) NOT NULL auto_increment,
  `pID` int(11) NOT NULL default '0',
  `attackType` int(11) NOT NULL default '0',
  `totick` int(11) NOT NULL default '0',
  `staytick` int(11) NOT NULL default '0',
  `backtick` int(11) NOT NULL default '0',
  `acres` int(11) NOT NULL default '0',
  `gold` int(11) NOT NULL default '0',
  `metal` int(11) NOT NULL default '0',
  `food` int(11) NOT NULL default '0',
  `targetID` int(11) NOT NULL default '0',
  `extraIncome` int(11) NOT NULL default '0',
  PRIMARY KEY  (`attackID`,`attackType`),
  KEY `targetID` (`targetID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `AttackT` (
  `attackType` int(11) NOT NULL auto_increment,
  `className` varchar(100) default NULL,
  PRIMARY KEY  (`attackType`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `BonusLog` (
  `bonusLogID` int(11) NOT NULL auto_increment,
  `byUser` int(11) default NULL,
  `toUser` int(11) default NULL,
  `txt` text,
  `type` int(3) default '0',
  PRIMARY KEY  (`bonusLogID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Buildings` (
  `bID` int(11) NOT NULL default '0',
  `num` int(11) NOT NULL default '0',
  `pID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pID`,`bID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `BuildingT` (
  `bID` int(11) NOT NULL auto_increment,
  `className` varchar(100) default NULL,
  PRIMARY KEY  (`bID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Config` (
  `ticks` int(11) default '1',
  `status` enum('Running','Pause','Ended','Beta') character set latin1 default 'Pause',
  `statusLength` int(11) NOT NULL,
  `lastTickTime` datetime default NULL,
  `totalTickTime` int(11) NOT NULL,
  `age` int(11) default '0',
  `ApocalypseLength` int(11) default '500',
  `vactionMax` int(11) default '336',
  `vactionMin` int(11) default '48',
  `maxProvinceInKD` int(11) default '5',
  `serverMode` enum('Normal','Beta') character set latin1 default 'Normal',
  `Season` int(11) default '1',
  `SeasonTick` int(11) default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `Config` (`ticks`, `status`, `statusLength`, `lastTickTime`, `totalTickTime`, `age`, `ApocalypseLength`, `vactionMax`, `vactionMin`, `maxProvinceInKD`, `serverMode`, `Season`, `SeasonTick`) VALUES
(1, 'Ended', 10, '2011-04-03 21:00:39', 3600, 1, 500, 336, 48, 4, 'Normal', 1, 67);

CREATE TABLE `Council` (
  `coID` int(11) NOT NULL auto_increment,
  `className` varchar(30) default NULL,
  PRIMARY KEY  (`coID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `DeadMilitary` (
  `deadMilID` int(11) NOT NULL auto_increment,
  `pID` int(11) NOT NULL default '0',
  `mID` int(11) NOT NULL default '0',
  `mType` int(11) NOT NULL default '0',
  `ticks` int(11) NOT NULL default '0',
  `num` int(11) NOT NULL default '0',
  PRIMARY KEY  (`deadMilID`),
  KEY `pID` (`pID`),
  KEY `mID` (`mID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Explore` (
  `pID` int(11) NOT NULL default '0',
  `exploredLand` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Forum` (
  `id` int(11) NOT NULL auto_increment,
  `kiID` int(11) NOT NULL,
  `pID` int(11) NOT NULL,
  `poster` varchar(150) NOT NULL,
  `parent` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `dateSubmitted` int(11) NOT NULL,
  `dateEditted` int(11) NOT NULL,
  `views` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ForumMain` (
  `ForumID` int(11) NOT NULL auto_increment,
  `ForumName` varchar(255) default NULL,
  `MaxThreads` int(11) default '100',
  `Access` int(3) default '0',
  `ForumDescription` varchar(255) default NULL,
  `canPost` int(3) default '0',
  `kiID` int(11) default '-1',
  PRIMARY KEY  (`ForumID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ForumPost` (
  `PostID` int(11) NOT NULL auto_increment,
  `text` text,
  `PostEdit` enum('true','false') character set latin1 default 'false',
  `PostTime` datetime default NULL,
  `PostUserID` int(11) default NULL,
  `PostThreadID` int(11) default NULL,
  `PostForumID` int(11) default NULL,
  `PostEditTime` datetime default NULL,
  `PostNick` varchar(90) default NULL,
  `PostForceShow` enum('true','false') character set latin1 default 'false',
  PRIMARY KEY  (`PostID`),
  KEY `ThreadID` (`PostThreadID`),
  KEY `ForumID` (`PostForumID`),
  KEY `UserID` (`PostUserID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ForumThread` (
  `ThreadID` int(11) NOT NULL auto_increment,
  `ThreadName` varchar(255) default NULL,
  `ThreadTime` datetime default NULL,
  `ThreadTop` enum('true','false') character set latin1 default 'false',
  `ThreadClosed` enum('true','false') character set latin1 default NULL,
  `ThreadUserID` int(11) default NULL,
  `ThreadForumID` int(11) default NULL,
  `Views` int(11) default '0',
  `ThreadNick` varchar(90) default NULL,
  `ThreadReadAccess` int(11) default '0',
  PRIMARY KEY  (`ThreadID`),
  KEY `ForumID` (`ThreadForumID`),
  KEY `UserID` (`ThreadUserID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Kingdom` (
  `kiID` int(11) NOT NULL auto_increment,
  `name` varchar(40) default NULL,
  `king` int(11) NOT NULL default '0',
  `password` varchar(20) default NULL,
  `banner` varchar(100) default NULL,
  `signature` varchar(100) default NULL,
  `numProvinces` int(11) NOT NULL default '0',
  `relationWar` int(11) default '0',
  `relationAlly` int(11) default '0',
  `relationMerge` enum('true','false') character set latin1 default 'false',
  `relationWarTick` int(11) default '0',
  PRIMARY KEY  (`kiID`),
  KEY `king` (`king`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Log` (
  `ID` int(11) NOT NULL auto_increment,
  `Age` int(11) default '-1',
  `Provinces` int(11) default '0',
  `UsersActive` int(11) default '0',
  `UsersTotal` int(11) default '0',
  `Logins` int(11) default '0',
  `TickTime` datetime default NULL,
  `Tick` int(11) default '0',
  `ServerStatus` enum('Running','Pause','Ended','Beta') character set latin1 default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `MagicMilitary` (
  `tmpmID` int(11) NOT NULL auto_increment,
  `pID` int(11) NOT NULL default '0',
  `fpID` int(11) NOT NULL default '0',
  `mID` int(11) NOT NULL default '0',
  `num` int(11) NOT NULL default '0',
  `ticks` int(11) NOT NULL default '0',
  PRIMARY KEY  (`tmpmID`,`pID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Message` (
  `mID` int(11) NOT NULL auto_increment,
  `toID` int(11) NOT NULL default '0',
  `fromID` int(11) NOT NULL default '0',
  `sent` datetime NOT NULL default '0000-00-00 00:00:00',
  `message` mediumtext,
  `isRead` int(1) default '0',
  `fromDeleted` int(1) default '0',
  `toDeleted` int(1) default '0',
  `ticks` int(11) NOT NULL default '0',
  PRIMARY KEY  (`mID`),
  KEY `toID` (`toID`),
  KEY `fromID` (`fromID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Military` (
  `pID` int(11) NOT NULL default '0',
  `mID` int(11) NOT NULL default '0',
  `num` int(11) NOT NULL default '0',
  PRIMARY KEY  (`pID`,`mID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `MilitaryT` (
  `mID` int(11) NOT NULL auto_increment,
  `className` varchar(100) default NULL,
  PRIMARY KEY  (`mID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `News` (
  `neID` int(11) NOT NULL auto_increment,
  `info` mediumtext,
  `kiID` int(11) NOT NULL default '0',
  `seen` enum('Y','N') character set latin1 NOT NULL default 'N',
  `timeS` int(11) NOT NULL default '0',
  `symbol` int(3) default '0',
  PRIMARY KEY  (`neID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `NewsProvince` (
  `neID` int(11) NOT NULL auto_increment,
  `info` mediumtext,
  `pID` int(11) NOT NULL default '0',
  `seen` enum('Y','N') character set latin1 NOT NULL default 'N',
  `timeS` int(11) NOT NULL default '0',
  PRIMARY KEY  (`neID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ProgressBuild` (
  `proID` int(11) NOT NULL auto_increment,
  `bID` int(11) NOT NULL default '0',
  `pID` int(11) NOT NULL default '0',
  `ticks` int(11) NOT NULL default '0',
  `noToBuild` int(11) NOT NULL default '0',
  PRIMARY KEY  (`proID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ProgressExpl` (
  `prID` int(11) NOT NULL auto_increment,
  `pID` int(11) NOT NULL default '0',
  `tick` int(11) NOT NULL default '0',
  `num_acers` int(11) NOT NULL default '0',
  PRIMARY KEY  (`prID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ProgressMil` (
  `prID` int(11) NOT NULL auto_increment,
  `pID` int(11) NOT NULL default '0',
  `mID` int(11) NOT NULL default '0',
  `ticks` int(11) NOT NULL default '0',
  `num` int(11) NOT NULL default '0',
  PRIMARY KEY  (`prID`),
  KEY `pID` (`pID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Province` (
  `pID` int(11) NOT NULL auto_increment,
  `provinceName` varchar(40) default NULL,
  `rulerName` varchar(40) default NULL,
  `gender` enum('M','F') character set latin1 default 'M',
  `mana` int(4) default '100',
  `influence` int(4) default '100',
  `acres` int(11) default '300',
  `peasants` int(11) default '1400',
  `gold` int(11) default '280000',
  `food` int(11) default '40000',
  `metal` int(11) default '5000',
  `spID` int(11) NOT NULL default '0',
  `kiID` int(11) NOT NULL default '0',
  `networth` int(11) default NULL,
  `incomeChange` int(11) default '0',
  `incomeTotal` int(11) default '0',
  `peasantChange` int(11) default '0',
  `peasantTotal` int(11) default '0',
  `foodChange` int(11) default '0',
  `foodTotal` int(11) default '0',
  `metalChange` int(11) default '0',
  `metalTotal` int(11) default '0',
  `militaryPopulation` int(11) default '300',
  `buildingPeasantPopulation` int(11) default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` enum('Killed','Deleted','Inactive','Alive') character set latin1 default 'Alive',
  `voteFor` int(11) default NULL,
  `morale` int(4) default '100',
  `attacksMade` int(11) default '0',
  `attacksSuffered` int(11) default '0',
  `attackWins` int(11) default '0',
  `attackNum` int(11) NOT NULL default '0',
  `buildingJobs` int(11) default '0',
  `aliveTicks` int(11) default '-1',
  `council` int(11) default '0',
  `reputation` int(11) default '1000',
  `attacksSufferedLost` int(11) default '0',
  `protection` int(11) default '50',
  `magicRep` int(11) default '0',
  `militaryRep` int(11) default '0',
  `vacationTicks` int(11) default NULL,
  `vacationmode` enum('true','false') character set latin1 default 'false',
  `goldExpenses` int(11) default '0',
  `metalExpenses` int(11) default '0',
  `foodExpenses` int(11) default '0',
  `showThieveryRank` enum('true','false') character set latin1 default 'false',
  `lastAccess` int(11) default NULL,
  PRIMARY KEY  (`pID`),
  UNIQUE KEY `provinceName` (`provinceName`),
  KEY `kiID` (`kiID`),
  KEY `spID` (`spID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Race` (
  `rID` int(11) NOT NULL auto_increment,
  `className` varchar(100) default NULL,
  PRIMARY KEY  (`rID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Results` (
  `age` int(11) NOT NULL default '0',
  `pID` int(11) NOT NULL default '0',
  `userID` int(11) NOT NULL default '0',
  `userName` varchar(255) default NULL,
  `provinceName` varchar(255) default NULL,
  `rulerName` varchar(255) default NULL,
  `kingdomName` varchar(255) default NULL,
  `kiID` int(11) NOT NULL default '0',
  `networth` int(11) NOT NULL default '0',
  `acres` int(11) NOT NULL default '0',
  `thieveryRank` varchar(255) default NULL,
  `thieveryPoints` int(11) NOT NULL default '0',
  `magicRank` varchar(255) default NULL,
  `magicPoints` int(11) NOT NULL default '0',
  PRIMARY KEY  (`age`,`pID`),
  KEY `age` (`age`),
  KEY `kiID` (`kiID`),
  KEY `acres` (`acres`),
  KEY `networth` (`networth`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Science` (
  `scID` int(11) NOT NULL auto_increment,
  `pID` int(11) NOT NULL default '0',
  `sccID` int(11) NOT NULL default '0',
  `ticks` int(11) NOT NULL default '0',
  PRIMARY KEY  (`scID`),
  KEY `pID` (`pID`),
  KEY `sccID` (`sccID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ScienceCat` (
  `sccID` int(11) NOT NULL auto_increment,
  `className` varchar(30) default NULL,
  PRIMARY KEY  (`sccID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `Spells` (
  `spellID` int(11) NOT NULL auto_increment,
  `casterID` int(11) NOT NULL default '0',
  `targetID` int(11) NOT NULL default '0',
  `sID` int(11) NOT NULL default '0',
  `ticks` int(11) NOT NULL default '0',
  `wizards` int(11) NOT NULL default '0',
  `type` int(11) NOT NULL default '1',
  `strength` double NOT NULL default '0',
  PRIMARY KEY  (`spellID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `SpellT` (
  `sID` int(11) NOT NULL auto_increment,
  `className` varchar(100) default NULL,
  PRIMARY KEY  (`sID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `ThieveryOps` (
  `thoID` int(11) NOT NULL auto_increment,
  `ClassName` varchar(30) default NULL,
  PRIMARY KEY  (`thoID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `TmpInCommandMilitary` (
  `tmpmID` int(11) NOT NULL auto_increment,
  `pID` int(11) NOT NULL default '0',
  `fpID` int(11) NOT NULL default '0',
  `mID` int(11) NOT NULL default '0',
  `num` int(11) NOT NULL default '0',
  `totick` int(11) NOT NULL default '0',
  `fromtick` int(11) NOT NULL default '0',
  `staytick` int(11) NOT NULL default '0',
  PRIMARY KEY  (`tmpmID`,`pID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `TrigEffect` (
  `teID` int(11) NOT NULL auto_increment,
  `effID` int(11) NOT NULL default '0',
  `kiID` int(11) NOT NULL default '0',
  `pID` int(11) NOT NULL default '0',
  `strength` int(11) NOT NULL default '1',
  `duration` int(11) NOT NULL default '0',
  PRIMARY KEY  (`teID`),
  KEY `effID` (`effID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `TrigEffectType` (
  `effID` int(11) NOT NULL auto_increment,
  `className` varchar(30) default NULL,
  PRIMARY KEY  (`effID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE `User` (
  `userID` int(11) NOT NULL auto_increment,
  `username` varchar(16) default NULL,
  `pID` int(11) NOT NULL default '0',
  `history` text,
  `access` int(11) default '3',
  `lastPlayedAge` int(11) default '-1',
  PRIMARY KEY  (`userID`),
  KEY `pID` (`pID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
