-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1
-- 生成日期： 2026-04-14 12:15:24
-- 服务器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--

CREATE DATABASE IF NOT EXISTS cematrixdb;
USE cematrixdb;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `cart` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `promo_code` varchar(20) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `order_item`
--

CREATE TABLE `order_item` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(4,2) NOT NULL,
  `description` text DEFAULT NULL,
  `release_date` datetime NOT NULL,
  `stock` int(4) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT 'box.png',
  `category` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `product`
--

INSERT INTO `product` (`product_id`, `name`, `price`, `description`, `release_date`, `stock`, `image`, `category`) VALUES
(1, 'Mega Brave', 59.00, 'Detailed description for Mega Brave...', '2026-04-15 10:00:00', 100, '1.png', 'card'),
(2, 'Ninja Spinner', 59.00, 'Ninja Spinner is invincible...', '2026-04-20 14:30:00', 100, '2.png', 'card'),
(3, 'Munikis Zero', 59.00, 'Munikis Zero Limited Edition...', '2026-05-01 09:00:00', 100, '3.png', 'card'),
(4, 'Mega Symphonia', 59.00, 'Mega Symphonia Sound of Music...', '2026-05-10 12:00:00', 100, '4.png', 'card'),
(5, 'Black Bolt', 59.00, 'Black Bolt strikes...', '2026-05-15 18:00:00', 100, '5.png', 'card');

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `role` enum('Admin','Member') DEFAULT 'Member',
  `photo` varchar(255) DEFAULT 'default.png',
  `remember_token` varchar(40) DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `is_blocked` tinyint(1) DEFAULT 0,
  `block_reason` varchar(255) DEFAULT NULL,
  `blocked_at` datetime DEFAULT NULL,
  `reset_code` varchar(10) DEFAULT NULL,
  `reset_code_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`user_id`, `email`, `password`, `name`, `role`, `photo`, `remember_token`, `login_attempts`, `locked_until`, `is_blocked`, `block_reason`, `blocked_at`, `reset_code`, `reset_code_expire`) VALUES
(1, 'admin@popmart.com', '0192023a7bbd73250516f069df18b500', 'Admin Boss', 'Admin', 'default.png', NULL, 0, NULL, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `voucher`
--

CREATE TABLE `voucher` (
  `promo_code` varchar(20) NOT NULL,
  `discount_price` decimal(10,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `voucher_handle`
--

CREATE TABLE `voucher_handle` (
  `user_id` int(11) NOT NULL,
  `promo_code` varchar(20) NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `wishlist`
--

CREATE TABLE `wishlist` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `fk_cart_user` (`user_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- 表的索引 `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_order_user` (`user_id`);

--
-- 表的索引 `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `fk_item_product` (`product_id`);

--
-- 表的索引 `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- 表的索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- 表的索引 `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`promo_code`);

--
-- 表的索引 `voucher_handle`
--
ALTER TABLE `voucher_handle`
  ADD PRIMARY KEY (`user_id`,`promo_code`),
  ADD KEY `promo_code` (`promo_code`);

--
-- 表的索引 `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- 使用表AUTO_INCREMENT `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 限制导出的表
--

--
-- 限制表 `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- 限制表 `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- 限制表 `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `fk_item_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- 限制表 `voucher_handle`
--
ALTER TABLE `voucher_handle`
  ADD CONSTRAINT `voucher_handle_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `voucher_handle_ibfk_2` FOREIGN KEY (`promo_code`) REFERENCES `voucher` (`promo_code`);

--
-- 限制表 `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
