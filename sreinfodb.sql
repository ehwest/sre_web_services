-- phpMyAdmin SQL Dump
-- version 2.11.9.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 17, 2014 at 09:10 PM
-- Server version: 5.0.22
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `sre`
--

-- --------------------------------------------------------

--
-- Table structure for table `sreinfodb`
--

CREATE TABLE IF NOT EXISTS `sreinfodb` (
  `ix` int(11) NOT NULL auto_increment,
  `inputparams` longtext,
  `results` longtext,
  `userid` varchar(64) default NULL,
  `istatus` varchar(64) default NULL,
  `tod` int(11) default NULL,
  `uploadedfile` longtext,
  `filename` text,
  `tempfilename` text,
  `modified` int(11) default NULL,
  `outputmatrix` longtext,
  `checkparamslen` int(11) default NULL,
  `t1` int(11) NOT NULL,
  `t2` int(11) NOT NULL,
  `teststartdate` int(11) default NULL,
  `projectname` varchar(64) default NULL,
  `datapoints` int(11) default NULL,
  `maxeffort` double default NULL,
  `totaldefects` int(11) default NULL,
  `diim` varchar(32) default NULL,
  `dinim` varchar(32) default NULL,
  `im_effort_left` varchar(32) default NULL,
  `imdefectsleft` varchar(32) default NULL,
  `nim_effort_left` varchar(32) default NULL,
  `nimdefectsleft` varchar(32) default NULL,
  PRIMARY KEY  (`ix`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii AUTO_INCREMENT=31032 ;

