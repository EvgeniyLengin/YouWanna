
CREATE TABLE `iqsms_sender_list` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PHONE` varchar(13) NOT NULL,
  `TEXT` text NOT NULL,
  `STATUS` tinyint(2) NOT NULL,
  `CREATED` datetime NOT NULL,
  `SCHEDULE` datetime DEFAULT NULL,
  `TYPE` varchar(255) DEFAULT NULL,
  `COMMENT` text,
  `SITE_ID` varchar(2) NOT NULL,
  `SENDER` varchar(20) NOT NULL,
  `PARAMS` text,
  PRIMARY KEY (`ID`),
  KEY `ix_status` (`STATUS`)
);


CREATE TABLE `iqsms_sender_template` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TYPE` varchar(255) NOT NULL,
  `ACTIVE` tinyint(1) DEFAULT NULL,
  `NAME` varchar(255) NOT NULL,
  `PHONE` varchar(255) NOT NULL,
  `PHONE_COPY` varchar(255) DEFAULT NULL,
  `TEXT` text NOT NULL,
  `EVENT` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `ix_type_active` (`TYPE`,`ACTIVE`)
);

CREATE TABLE `iqsms_sender_template_site` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TID` int(11) NOT NULL,
  `SID` varchar(2) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ix_tid_sid` (`TID`,`SID`)
);

