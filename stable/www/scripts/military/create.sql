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
#
# Table structure for table `MagicMilitary`
#

DROP TABLE IF EXISTS MagicMilitary;
CREATE TABLE MagicMilitary (
  tmpmID int(11) NOT NULL auto_increment,
  pID int(11) NOT NULL default '0',
  fpID int(11) NOT NULL default '0',
  mID int(11) NOT NULL default '0',
  num int(11) NOT NULL default '0',
  ticks int(11) NOT NULL default '0',
  PRIMARY KEY  (tmpmID,pID)
) TYPE=MyISAM;


#
# Table structure for table `Military`
#

DROP TABLE IF EXISTS Military;
CREATE TABLE Military (
  pID int(11) NOT NULL default '0',
  mID int(11) NOT NULL default '0',
  num int(11) NOT NULL default '0',
  PRIMARY KEY  (pID,mID)
) TYPE=MyISAM;

#
# Table structure for table `MilitaryT`
#

DROP TABLE IF EXISTS MilitaryT;
CREATE TABLE MilitaryT (
  mID int(11) NOT NULL auto_increment,
  className varchar(100) NOT NULL default '',
  PRIMARY KEY  (mID)
) TYPE=MyISAM;

#
# Inserting data for table `MilitaryT`
#

INSERT INTO MilitaryT (className) VALUES ('HumanRecruitsMilitary');
INSERT INTO MilitaryT (className) VALUES ('HumanAttackersMilitary');
INSERT INTO MilitaryT (className) VALUES ('HumanDefendersMilitary');
INSERT INTO MilitaryT (className) VALUES ('HumanElitesMilitary');
INSERT INTO MilitaryT (className) VALUES ('HumanThievesMilitary');
INSERT INTO MilitaryT (className) VALUES ('HumanMagiciansMilitary');
INSERT INTO MilitaryT (className) VALUES ('OrcRecruitsMilitary');
INSERT INTO MilitaryT (className) VALUES ('OrcAttackersMilitary');
INSERT INTO MilitaryT (className) VALUES ('OrcDefendersMilitary');
INSERT INTO MilitaryT (className) VALUES ('OrcElitesMilitary');
INSERT INTO MilitaryT (className) VALUES ('OrcThievesMilitary');
INSERT INTO MilitaryT (className) VALUES ('OrcMagiciansMilitary');
INSERT INTO MilitaryT (className) VALUES ('ElfRecruitsMilitary');
INSERT INTO MilitaryT (className) VALUES ('ElfAttackersMilitary');
INSERT INTO MilitaryT (className) VALUES ('ElfDefendersMilitary');
INSERT INTO MilitaryT (className) VALUES ('ElfElitesMilitary');
INSERT INTO MilitaryT (className) VALUES ('ElfThievesMilitary');
INSERT INTO MilitaryT (className) VALUES ('ElfMagiciansMilitary');
INSERT INTO MilitaryT (className) VALUES ('DwarfRecruitsMilitary');
INSERT INTO MilitaryT (className) VALUES ('DwarfAttackersMilitary');
INSERT INTO MilitaryT (className) VALUES ('DwarfDefendersMilitary');
INSERT INTO MilitaryT (className) VALUES ('DwarfElitesMilitary');
INSERT INTO MilitaryT (className) VALUES ('DwarfThievesMilitary');
INSERT INTO MilitaryT (className) VALUES ('DwarfMagiciansMilitary');


# --------------------------------------------------------
#
# Table structure for table `TmpInCommandMilitary`
#

DROP TABLE IF EXISTS TmpInCommandMilitary;
CREATE TABLE TmpInCommandMilitary (
  tmpmID int(11) NOT NULL auto_increment,
  pID int(11) NOT NULL default '0',
  fpID int(11) NOT NULL default '0',
  mID int(11) NOT NULL default '0',
  num int(11) NOT NULL default '0',
  totick int(11) NOT NULL default '0',
  fromtick int(11) NOT NULL default '0',
  staytick int(11) NOT NULL default '0',
  PRIMARY KEY  (tmpmID,pID)
) TYPE=MyISAM;
