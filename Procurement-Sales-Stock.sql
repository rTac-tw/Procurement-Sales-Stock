-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- 主機： localhost
-- 產生時間： 2019 年 11 月 15 日 03:30
-- 伺服器版本： 5.7.28-log
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
  `permission` text COMMENT '權限',
  `disable_date` datetime DEFAULT NULL COMMENT '停用時間',
  `create_date` datetime NOT NULL COMMENT '建立時間	',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間	'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='部門';

--
-- 傾印資料表的資料 `department`
--

INSERT INTO `department` (`id`, `name`, `permission`, `disable_date`, `create_date`, `edit_date`) VALUES
(1, '會計室', '1,2,3,4', NULL, '2019-11-12 00:00:00', '2019-11-12 00:00:00'),
(2, '第一業務部', '2', NULL, '2019-11-12 00:00:00', '2019-11-12 00:00:00'),
(3, '第二業務部', '3', NULL, '2019-11-12 00:00:00', '2019-11-12 00:00:00'),
(4, '第三業務部', '4', NULL, '2019-11-12 00:00:00', '2019-11-12 00:00:00');

-- --------------------------------------------------------

--
-- 資料表結構 `position`
--

CREATE TABLE `position` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '職務名稱',
  `permission` text COMMENT '權限',
  `disable_date` datetime DEFAULT NULL COMMENT '停用時間',
  `create_date` datetime NOT NULL COMMENT '建立時間	',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間	'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='職務';

--
-- 傾印資料表的資料 `position`
--

INSERT INTO `position` (`id`, `name`, `permission`, `disable_date`, `create_date`, `edit_date`) VALUES
(1, '業務主管', '2', NULL, '2019-11-12 00:00:00', '2019-11-12 00:00:00'),
(2, '業務', NULL, NULL, '2019-11-12 00:00:00', '2019-11-12 00:00:00'),
(3, '會計', '1,2,3', NULL, '2019-11-12 00:00:00', '2019-11-12 00:00:00');

-- --------------------------------------------------------

--
-- 資料表結構 `product`
--

CREATE TABLE `product` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '商品名稱',
  `price` int(10) UNSIGNED NOT NULL COMMENT '價格',
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '庫存數量',
  `disable_date` datetime DEFAULT NULL COMMENT '停用(下架)時間',
  `create_date` datetime NOT NULL COMMENT '建立時間	',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間	'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品主表';

--
-- 傾印資料表的資料 `product`
--

INSERT INTO `product` (`id`, `name`, `price`, `quantity`, `disable_date`, `create_date`, `edit_date`) VALUES
(1, '口香糖', 17, 170, NULL, '2019-11-13 00:00:00', '2019-11-15 02:52:50'),
(2, '泡麵', 50, 189, NULL, '2019-11-13 00:00:00', '2019-11-13 00:00:00'),
(3, '糖果', 2, 80, NULL, '2019-11-13 00:00:00', '2019-11-15 02:25:57'),
(4, '蘇打餅乾', 25, 60, NULL, '2019-11-14 10:21:57', '2019-11-15 02:52:52'),
(5, '巧克力', 10, 50, NULL, '2019-11-15 02:27:17', '2019-11-15 03:30:13');

-- --------------------------------------------------------

--
-- 資料表結構 `stock_log`
--

CREATE TABLE `stock_log` (
  `id` bigint(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '操作者id',
  `stock_type` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '異動類型(進銷)',
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品id',
  `price` int(10) UNSIGNED NOT NULL COMMENT '登打當下價格',
  `quantity` int(11) NOT NULL COMMENT '異動數量',
  `disable_date` datetime DEFAULT NULL COMMENT '停用(作廢)時間	',
  `create_date` datetime NOT NULL COMMENT '建立時間',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間	'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='庫存異動紀錄';

--
-- 傾印資料表的資料 `stock_log`
--

INSERT INTO `stock_log` (`id`, `user_id`, `stock_type`, `product_id`, `price`, `quantity`, `disable_date`, `create_date`, `edit_date`) VALUES
(1, 5, 0, 1, 17, 100, NULL, '2019-11-13 00:00:00', '2019-11-13 00:00:00'),
(2, 5, 1, 1, 17, 20, NULL, '2019-11-13 00:00:00', '2019-11-13 00:00:01'),
(3, 9, 0, 2, 50, 50, NULL, '2019-11-13 00:00:00', '2019-11-13 00:00:02'),
(4, 8, 1, 2, 50, 21, NULL, '2019-11-13 00:00:00', '2019-11-13 00:00:03'),
(5, 6, 1, 2, 50, 20, NULL, '2019-11-13 00:00:00', '2019-11-13 00:00:04'),
(6, 9, 0, 2, 50, 200, NULL, '2019-11-13 00:00:00', '2019-11-13 00:00:05'),
(7, 9, 1, 2, 50, 20, NULL, '2019-11-13 00:00:00', '2019-11-13 00:00:06'),
(8, 4, 1, 1, 17, 20, NULL, '2019-11-14 05:59:46', '2019-11-14 05:59:46'),
(9, 4, 1, 1, 17, 10, NULL, '2019-11-14 06:03:01', '2019-11-14 06:03:01'),
(10, 4, 0, 1, 17, 100, NULL, '2019-11-14 06:17:13', '2019-11-14 06:17:13'),
(11, 4, 1, 1, 17, 5, NULL, '2019-11-14 06:35:41', '2019-11-14 06:35:41'),
(12, 4, 1, 1, 17, 5, NULL, '2019-11-14 06:36:38', '2019-11-14 06:36:38'),
(13, 4, 1, 1, 17, 5, NULL, '2019-11-14 06:39:07', '2019-11-14 06:39:07'),
(14, 4, 0, 1, 17, 15, NULL, '2019-11-14 06:40:54', '2019-11-14 06:40:54'),
(15, 1, 0, 3, 2, 80, NULL, '2019-11-14 06:42:38', '2019-11-14 06:42:38'),
(16, 4, 0, 1, 17, 20, NULL, '2019-11-14 09:49:27', '2019-11-14 09:49:27'),
(17, 7, 0, 4, 25, 100, NULL, '2019-11-15 01:40:39', '2019-11-15 01:40:39'),
(18, 7, 1, 4, 25, 18, NULL, '2019-11-15 01:40:56', '2019-11-15 01:40:56'),
(19, 7, 1, 4, 25, 22, NULL, '2019-11-15 01:41:09', '2019-11-15 01:41:09'),
(20, 10, 0, 5, 10, 100, NULL, '2019-11-15 03:28:10', '2019-11-15 03:28:10'),
(21, 1, 1, 5, 10, 50, NULL, '2019-11-15 03:30:13', '2019-11-15 03:30:13');

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(11) UNSIGNED NOT NULL,
  `account` varchar(255) NOT NULL COMMENT '帳號',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `gender` tinyint(1) NOT NULL COMMENT '性別',
  `department_id` int(11) UNSIGNED NOT NULL COMMENT '部門',
  `position_id` int(11) UNSIGNED NOT NULL COMMENT '職務',
  `use_date` datetime DEFAULT NULL COMMENT '啟用時間',
  `disable_date` datetime DEFAULT NULL COMMENT '停用時間',
  `create_date` datetime NOT NULL COMMENT '建立時間',
  `edit_date` datetime NOT NULL COMMENT '最後修改時間'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='使用者';

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `account`, `name`, `gender`, `department_id`, `position_id`, `use_date`, `disable_date`, `create_date`, `edit_date`) VALUES
(1, 'user_a', '業務主管A', 1, 2, 1, '2019-11-12 10:18:15', NULL, '2019-11-11 00:00:00', '2019-11-15 03:24:35'),
(2, 'user_b', '業務主管B', 2, 3, 1, '2019-11-11 00:00:00', NULL, '2019-11-11 00:00:00', '2019-11-15 03:09:12'),
(3, 'user_c', '業務主管C', 1, 4, 1, '2019-11-11 00:00:00', NULL, '2019-11-11 00:00:00', '2019-11-11 00:00:00'),
(4, 'user', '會計', 0, 1, 3, '2019-11-15 00:00:00', NULL, '2019-11-12 10:42:28', '2019-11-15 03:24:31'),
(5, 'user_1', '業務甲', 0, 2, 2, '2019-11-15 00:00:00', NULL, '2019-11-13 02:04:00', '2019-11-15 03:23:55'),
(6, 'user_2', '業務乙', 0, 2, 2, '2019-11-15 00:00:00', NULL, '2019-11-13 02:04:36', '2019-11-13 02:04:36'),
(7, 'user_3', '業務丙', 0, 3, 2, '2019-11-15 00:00:00', NULL, '2019-11-13 06:22:49', '2019-11-15 02:45:20'),
(8, 'user_4', '業務丁', 0, 3, 2, '2019-11-15 00:00:00', NULL, '2019-11-13 06:23:15', '2019-11-13 06:23:15'),
(9, 'user_5', '業務戊', 0, 4, 2, '2019-11-15 00:00:00', NULL, '2019-11-13 06:24:05', '2019-11-13 06:24:05'),
(10, 'user_6', '業務己', 0, 4, 2, '2019-11-15 03:27:13', NULL, '2019-11-15 03:27:13', '2019-11-15 03:27:40');

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
(1, 1, 'd50fcbe7957bd2020da911c398f49a85', '2019-11-12 10:41:45', '2019-11-11 00:00:00', '2019-11-12 10:41:45'),
(2, 2, '4f21ad704c5667f4957c0450af3f4bee', '2019-11-11 00:00:00', '2019-11-11 00:00:00', '2019-11-11 00:00:00'),
(3, 3, 'd6730c4f59dbd125e7c434aeaa43e7c8', '2019-11-11 00:00:00', '2019-11-11 00:00:00', '2019-11-11 00:00:00'),
(4, 1, 'cb9dae505476a895cd885ac9b7e0eab3', '2019-11-12 10:41:24', '2019-11-12 10:12:27', '2019-11-12 10:41:24'),
(5, 4, 'ee11cbb19052e40b07aac0ca060c23ee', '2019-11-12 10:42:28', '2019-11-12 10:42:28', '2019-11-12 10:42:28'),
(6, 5, '3f49044c1469c6990a665f46ec6c0a41', '2019-11-13 02:04:00', '2019-11-13 02:04:00', '2019-11-13 02:04:00'),
(7, 6, '15e1576abc700ddfd9438e6ad1c86100', '2019-11-13 02:04:36', '2019-11-13 02:04:36', '2019-11-13 02:04:36'),
(8, 7, 'a6f601b7c855d45c6b5b182ab32a67c0', '2019-11-13 06:22:49', '2019-11-13 06:22:49', '2019-11-13 06:22:49'),
(9, 8, 'bb640eb8250ff322567a401240dd6a2e', '2019-11-13 06:23:15', '2019-11-13 06:23:15', '2019-11-13 06:23:15'),
(10, 9, 'fa890200036e527ebb5cba50e1c0450f', '2019-11-13 06:24:05', '2019-11-13 06:24:05', '2019-11-13 06:24:05'),
(11, 10, '1ab162d05561afc0289e1533693b197c', '2019-11-15 03:27:13', '2019-11-15 03:27:13', '2019-11-15 03:27:13');

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
  ADD UNIQUE KEY `account` (`account`) USING BTREE,
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `position`
--
ALTER TABLE `position`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `stock_log`
--
ALTER TABLE `stock_log`
  MODIFY `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_pwd`
--
ALTER TABLE `user_pwd`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
