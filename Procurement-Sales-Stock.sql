-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 2019 年 11 月 12 日 01:30
-- 伺服器版本： 5.7.28
-- PHP 版本： 7.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `Procurement-Sales-Stock`
--

-- --------------------------------------------------------

--
-- 資料表結構 `department`
--

CREATE TABLE `department` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '部門名稱',
  `disable_date` datetime DEFAULT NULL COMMENT '停用時間',
  `create_date` datetime NOT NULL COMMENT '建立時間	',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間	'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='部門';

-- --------------------------------------------------------

--
-- 資料表結構 `position`
--

CREATE TABLE `position` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '職務名稱',
  `disable_date` datetime DEFAULT NULL COMMENT '停用時間',
  `create_date` datetime NOT NULL COMMENT '建立時間	',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間	'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='職務';

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '商品名稱',
  `disable_date` datetime DEFAULT NULL COMMENT '停用(下架)時間',
  `create_date` datetime NOT NULL COMMENT '建立時間	',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間	'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品主表';

-- --------------------------------------------------------

--
-- 資料表結構 `stock_log`
--

CREATE TABLE `stock_log` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '操作者id',
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品id',
  `quantity` int(11) NOT NULL COMMENT '異動數量',
  `subtotal` int(11) NOT NULL COMMENT '剩餘庫存小計',
  `is_inventory_check` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否為清點庫存',
  `disable_date` datetime DEFAULT NULL COMMENT '停用(作廢)時間	',
  `create_date` datetime NOT NULL COMMENT '建立時間',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間	'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='庫存異動紀錄';

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL,
  `account` text NOT NULL COMMENT '帳號',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `gender` tinyint(1) NOT NULL COMMENT '性別',
  `department_id` int(11) UNSIGNED NOT NULL COMMENT '部門',
  `position_id` int(11) UNSIGNED NOT NULL COMMENT '職位',
  `use_date` datetime NOT NULL COMMENT '啟用時間',
  `disable_date` datetime DEFAULT NULL COMMENT '停用時間',
  `create_date` datetime NOT NULL COMMENT '建立時間',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='使用者';

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `account`, `name`, `gender`, `department_id`, `position_id`, `use_date`, `disable_date`, `create_date`, `edit_date`) VALUES
(1, 'user_a', '業務主管A', 1, 1, 1, '2019-11-11 00:00:00', NULL, '2019-11-11 00:00:00', '2019-11-11 00:00:00'),
(2, 'user_b', '業務主管B', 2, 2, 1, '2019-11-11 00:00:00', NULL, '2019-11-11 00:00:00', '2019-11-11 00:00:00'),
(3, 'user_c', '業務主管C', 1, 3, 1, '2019-11-11 00:00:00', NULL, '2019-11-11 00:00:00', '2019-11-11 00:00:00');

-- --------------------------------------------------------

--
-- 資料表結構 `user_pwd`
--

CREATE TABLE `user_pwd` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL COMMENT '使用者id',
  `password` varchar(255) NOT NULL COMMENT '密碼',
  `use_date` datetime NOT NULL COMMENT '啟用時間',
  `create_date` datetime NOT NULL COMMENT '建立時間',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='使用者密碼紀錄';

--
-- 傾印資料表的資料 `user_pwd`
--

INSERT INTO `user_pwd` (`id`, `user_id`, `password`, `use_date`, `create_date`, `edit_date`) VALUES
(1, 1, 'd50fcbe7957bd2020da911c398f49a85', '2019-11-11 00:00:00', '2019-11-11 00:00:00', '2019-11-11 00:00:00'),
(2, 2, '4f21ad704c5667f4957c0450af3f4bee', '2019-11-11 00:00:00', '2019-11-11 00:00:00', '2019-11-11 00:00:00'),
(3, 3, 'd6730c4f59dbd125e7c434aeaa43e7c8', '2019-11-11 00:00:00', '2019-11-11 00:00:00', '2019-11-11 00:00:00');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `stock_log`
--
ALTER TABLE `stock_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department` (`department_id`);

--
-- 資料表索引 `user_pwd`
--
ALTER TABLE `user_pwd`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `department`
--
ALTER TABLE `department`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `position`
--
ALTER TABLE `position`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `stock_log`
--
ALTER TABLE `stock_log`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_pwd`
--
ALTER TABLE `user_pwd`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
