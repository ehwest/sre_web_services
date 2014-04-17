-- phpMyAdmin SQL Dump
-- version 2.11.9.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 17, 2014 at 09:11 PM
-- Server version: 5.0.22
-- PHP Version: 5.1.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `sre`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ix` int(11) NOT NULL auto_increment,
  `userid` varchar(512) NOT NULL,
  `licensekey` varchar(512) NOT NULL,
  `modified` int(11) NOT NULL,
  PRIMARY KEY  (`ix`)
) ENGINE=MyISAM DEFAULT CHARSET=ascii AUTO_INCREMENT=11 ;

