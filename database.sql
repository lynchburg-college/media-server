
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `configSetting` varchar(14) NOT NULL,
  `configValue` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `formats`
--

CREATE TABLE IF NOT EXISTS `formats` (
  `convertOrder` int(11) NOT NULL,
  `formatName` varchar(12) NOT NULL,
  `fileType` varchar(5) NOT NULL,
  `videoCodec` varchar(12) NOT NULL,
  `audioCodec` varchar(12) NOT NULL,
  `commandLine` varchar(1024) NOT NULL,
  UNIQUE KEY `convertOrder` (`convertOrder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE IF NOT EXISTS `keywords` (
  `mediaID` int(11) NOT NULL,
  `keyword` varchar(32) NOT NULL,
  PRIMARY KEY (`mediaID`),
  KEY `keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `mediaName` varchar(128) NOT NULL,
  `uploaded` datetime NOT NULL,
  `uploadedHost` varchar(18) DEFAULT NULL,
  `uploadedUser` varchar(64) DEFAULT NULL,
  `converted` date DEFAULT NULL,
  `public` int(11) NOT NULL DEFAULT '1',
  `viewed` int(11) NOT NULL DEFAULT '0',
  `title` varchar(128) DEFAULT NULL,
  `description` text,
  UNIQUE KEY `mediaName` (`mediaName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `medialist`
--

CREATE TABLE IF NOT EXISTS `medialist` (
  `itemID` int(11) NOT NULL AUTO_INCREMENT,
  `parentID` varchar(64) COLLATE latin1_general_ci NOT NULL,
  `display` varchar(64) COLLATE latin1_general_ci NOT NULL,
  `mediaName` varchar(32) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`itemID`),
  KEY `parentID` (`parentID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=470 ;

-- --------------------------------------------------------

--
-- Table structure for table `medialog`
--

CREATE TABLE IF NOT EXISTS `medialog` (
  `logTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mediaName` varchar(128) NOT NULL,
  `mediaAction` varchar(32) NOT NULL,
  `mediaPosition` int(11) NOT NULL,
  `user` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `remote_host` varchar(18) NOT NULL,
  KEY `logTimestamp` (`logTimestamp`),
  KEY `mediaAction` (`mediaAction`),
  KEY `mediaName` (`mediaName`),
  KEY `remote_host` (`remote_host`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

