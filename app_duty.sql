-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- 主机: w.rdc.sae.sina.com.cn:3307
-- 生成日期: 2015 年 05 月 27 日 14:50
-- 服务器版本: 5.5.23
-- PHP 版本: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `app_duty`
--

-- --------------------------------------------------------

--
-- 表的结构 `duty`
--

CREATE TABLE IF NOT EXISTS `duty` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department` varchar(50) DEFAULT NULL COMMENT '部门',
  `name` varchar(50) DEFAULT NULL COMMENT '姓名',
  `number` varchar(50) DEFAULT NULL COMMENT '工号',
  `deadline` varchar(50) DEFAULT NULL COMMENT '打卡时间',
  `month` varchar(10) DEFAULT NULL COMMENT '月份',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `duty`
--


-- --------------------------------------------------------

--
-- 表的结构 `duty_user`
--

CREATE TABLE IF NOT EXISTS `duty_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `duty_user`
--

