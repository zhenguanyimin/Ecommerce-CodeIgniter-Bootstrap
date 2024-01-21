CREATE TABLE IF NOT EXISTS `active_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `enabled` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

INSERT INTO `active_pages` (`id`, `name`, `enabled`) VALUES
(1, 'blog', 1);

CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `time` int(10) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `visit_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `remote_addr` varchar(50) NOT NULL,
  `request_uri` varchar(255) NOT NULL,
  `remote_location` varchar(255) NOT NULL,
  `http_referer` varchar(255) NOT NULL,
  `visit_time` int(10) unsigned NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `cookie_law` (
  `id` int(10) UNSIGNED NOT NULL,
  `link` varchar(255) NOT NULL,
  `theme` varchar(20) NOT NULL,
  `visibility` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `cookie_law_translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `message` varchar(255) NOT NULL,
  `button_text` varchar(50) NOT NULL,
  `learn_more` varchar(50) NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `for_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cookie_law`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `cookie_law_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQUE` (`abbr`,`for_id`) USING BTREE;

ALTER TABLE `cookie_law`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `cookie_law_translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `textual_pages_tanslations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `for_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `textual_pages_tanslations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `textual_pages_tanslations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `activity` varchar(255) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `abbr` varchar(5) NOT NULL,
  `name` varchar(30) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `currencyKey` varchar(5) NOT NULL,
  `flag` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0不开启 1开启',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;


INSERT INTO `languages` (`id`, `abbr`, `name`, `currency`, `currencyKey`, `flag`, `status`) VALUES
(1, 'bg', 'bulgarian', 'лв', 'BGN', 'bg.jpg', '1'),
(2, 'en', 'english', '$', 'USD', 'en.jpg', '1'),
(3, 'gr', 'greece', 'EUR', 'EUR', 'gr.png', '1'),
(4, 'id', 'indonesian', 'RP', 'IDR', 'id.jpg', '1'),
(5, 'fr', 'francais', 'EUR', 'EUR', 'fr.jpg', '1')
(6, 'zh', 'chinese', '¥', 'CNY', 'zh.jpg', '1');


CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `trade_no` varchar(50) COMMENT '交易id',
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'point to public_users ID',
  `products` text NOT NULL,
  `date` int(10) unsigned NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `clean_referrer` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `paypal_status` varchar(10) DEFAULT NULL,
  `alipay_status` varchar(10) DEFAULT NULL,
  `pay_type` tinyint(3) unsigned NOT NULL DEFAULT '20' COMMENT '支付方式(10余额支付 20支付宝支付)',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '付款状态(10未付款 20已付款)',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '付款时间',
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '配送方式(10快递配送)',
  `express_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费金额',
  `express_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '物流公司ID',
  `express_company` varchar(50) NOT NULL DEFAULT '' COMMENT '物流公司',
  `express_no` varchar(50) NOT NULL DEFAULT '' COMMENT '物流单号',
  `delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '发货状态(10未发货 20已发货)',
  `delivery_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发货时间',
  `receipt_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '收货状态(10未收货 20已收货)',
  `receipt_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收货时间',
  `order_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单状态(10进行中 20取消 21待取消 30已完成)',
  `order_source` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单来源(10普通订单 20商户诚信保证金)',
  `total_amount` DOUBLE(16,6) DEFAULT 0,
  `vendor_share` DOUBLE(16,6) DEFAULT 0,
  `commission` DOUBLE(16,6) DEFAULT 0,
  `pay_fee_amount` DOUBLE(16,6) DEFAULT 0,
  `shipping_amount` int(11) DEFAULT '0',
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `viewed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'viewed status is change when change processed status',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `discount_code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `orders_clients` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `phone` varchar(500) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(500) NOT NULL,
  `post_code` varchar(500) NOT NULL,
  `notes` text NOT NULL,
  `for_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `orders_clients`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `orders_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `users_public` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `password` varchar(40) NOT NULL,
  `online_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '在线状态(0不在线 1在线)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户状态(1正常 2已注销)',
  `login_at` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录时间',
  `logout_at` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登出时间',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `users_public`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users_public`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folder` int(10) unsigned DEFAULT NULL COMMENT 'folder with images',
  `image` varchar(255) NOT NULL,
  `time` int(10) unsigned NOT NULL COMMENT 'time created',
  `time_update` int(10) unsigned NOT NULL COMMENT 'time updated',
  `visibility` tinyint(1) NOT NULL DEFAULT '1',
  `shop_categorie` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `grade` tinyint(3) unsigned NOT NULL DEFAULT '6' COMMENT '新旧程度评分(10全新 9九五新 8九成新 7八五新 6八成新 5七五新 4七成新 3六五新 2六成新 1五成新及以下)',
  `defect_desc` varchar(255) NOT NULL DEFAULT '' COMMENT '瑕疵说明',
  `procurement` int(10) unsigned NOT NULL,
  `in_slider` tinyint(1) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `virtual_products` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `brand_id` int(5) DEFAULT NULL,
  `position` int(10) UNSIGNED NOT NULL,
  `vendor_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `grade_desc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grade_id` int(10) unsigned NOT NULL,
  `desc` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `grade_desc` (`id`, `grade_id`, `desc`) VALUES
(1, 10, '全新'),
(2, 9, '九五新'),
(3, 8, '九成新'),
(4, 7, '八五新'),
(5, 6, '八成新'),
(6, 5, '七五新'),
(7, 4, '七成新'),
(8, 3, '六五新'),
(9, 2, '六成新'),
(10, 1, '五成新及以下');

CREATE TABLE IF NOT EXISTS `seo_pages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `seo_pages` (`id`, `name`) VALUES
(1, 'home'),
(2, 'checkout'),
(3, 'contacts'),
(4, 'blog');

CREATE TABLE IF NOT EXISTS `shop_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_for` int(11) NOT NULL,
  `position` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `subscribed` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE `blog_translations` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `for_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `blog_translations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `blog_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `products_translations` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `basic_description` text NOT NULL,
  `price` varchar(20) NOT NULL,
  `old_price` varchar(20) NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `for_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `products_translations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `products_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `seo_pages_translations` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` varchar(200) NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `page_type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `seo_pages_translations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `seo_pages_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `shop_categories_translations` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `for_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `shop_categories_translations`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `shop_categories_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  `notify` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'notifications by email',
  `last_login` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;


INSERT INTO `users` (`id`, `username`, `password`, `email`, `notify`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'your@email.com', 0);

CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `iban` varchar(255) NOT NULL,
  `bank` varchar(255) NOT NULL,
  `bic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `express_info`;
CREATE TABLE `express_info` (
  `express_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '物流公司ID',
  `express_name` varchar(255) NOT NULL DEFAULT '' COMMENT '物流公司名称',
  `kuaidi100_code` varchar(30) NOT NULL DEFAULT '' COMMENT '物流公司编码 (快递100)',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序(数字越小越靠前)',
  `store_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商城ID',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`express_id`),
  KEY `store_id` (`store_id`),
  KEY `kuaidi100_code` (`kuaidi100_code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='物流公司记录表';

CREATE TABLE IF NOT EXISTS `value_store` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thekey` varchar(50) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `key` (`thekey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `value_store` (`id`, `thekey`, `value`) VALUES
(1, 'sitelogo', 'NewLogo.jpg'),
(2, 'navitext', ''),
(3, 'footercopyright', 'Your organization.'),
(4, 'contactspage', 'Hello dear client'),
(5, 'footerContactAddr', ''),
(6, 'footerContactEmail', 'support@shop.dev'),
(7, 'footerContactPhone', ''),
(8, 'googleMaps', '42.671840, 83.279163'),
(9, 'footerAboutUs', ''),
(10, 'footerSocialFacebook', ''),
(11, 'footerSocialTwitter', ''),
(12, 'footerSocialGooglePlus', ''),
(13, 'footerSocialPinterest', ''),
(14, 'footerSocialYoutube', ''),
(16, 'contactsEmailTo', 'contacts@shop.dev'),
(17, 'shippingOrder', '1'),
(18, 'addJs', ''),
(19, 'publicQuantity', '0'),
(20, 'paypal_email', ''),
(21, 'paypal_sandbox', '0'),
(22, 'publicDateAdded', '0'),
(23, 'googleApi', ''),
(24, 'template', 'redlabel'),
(25, 'cashondelivery_visibility', '1'),
(26, 'showBrands', '0'),
(27, 'showInSlider', '0'),
(28, 'codeDiscounts', '1'),
(29, 'virtualProducts', '0'),
(30, 'multiVendor', '0'),
(31, 'outOfStock', '0'),
(32, 'hideBuyButtonsOfOutOfStock', '0'),
(33, 'moreInfoBtn', ''),
(34, 'refreshAfterAddToCart', 0);

CREATE TABLE `brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `confirm_links` (
  `id` int(11) NOT NULL,
  `link` char(32) NOT NULL,
  `for_order` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `confirm_links`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `confirm_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `discount_codes` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL,
  `code` varchar(10) NOT NULL,
  `amount` varchar(20) NOT NULL,
  `valid_from_date` int(10) UNSIGNED NOT NULL,
  `valid_to_date` int(10) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-enabled, 0-disabled',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `url` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `vendor_alipay_account` varchar(50) NOT NULL DEFAULT '' COMMENT '支付宝账号',
  `vendor_real_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `vendor_phone` varchar(50) NOT NULL DEFAULT '' COMMENT '手机号码',
  `vendor_IDCard` varchar(50) NOT NULL DEFAULT '' COMMENT '身份证号码',
  `vendor_weixin` varchar(50) NOT NULL DEFAULT '' COMMENT '微信账号',
  `password` varchar(100) NOT NULL,
  `bond_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '商户保证金缴纳状态(0未缴纳 1已缴纳 2已退还)',
  `vendor_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '商户状态(1正常 2已销户)',
  `online_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '在线状态(0不在线 1在线)',
  `login_at` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录时间',
  `logout_at` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登出时间',  
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique` (`email`),
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `vendors_orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(11) NOT NULL,
  `parent_order_id` int(11) NOT NULL,
  `products` text NOT NULL,
  `date` int(10) UNSIGNED NOT NULL,
  `referrer` varchar(255) NOT NULL,
  `clean_referrer` varchar(255) NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `paypal_status` varchar(10) DEFAULT NULL,
  `alipay_status` varchar(10) DEFAULT NULL,
  `pay_type` tinyint(3) unsigned NOT NULL DEFAULT '20' COMMENT '支付方式(10余额支付 20支付宝支付)',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '付款状态(10未付款 20已付款)',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '付款时间',
  `delivery_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '配送方式(10快递配送)',
  `express_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '运费金额',
  `express_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '物流公司ID',
  `express_company` varchar(50) NOT NULL DEFAULT '' COMMENT '物流公司',
  `express_no` varchar(50) NOT NULL DEFAULT '' COMMENT '物流单号',
  `delivery_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '发货状态(10未发货 20已发货)',
  `delivery_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发货时间',
  `receipt_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '收货状态(10未收货 20已收货)',
  `receipt_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '收货时间',
  `order_status` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单状态(10进行中 20取消 21待取消 30已完成)',
  `order_source` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '订单来源(10普通订单 20商户诚信保证金)',
  `total_amount` DOUBLE(16,6) DEFAULT 0,
  `vendor_share` DOUBLE(16,6) DEFAULT 0,
  `commission` DOUBLE(16,6) DEFAULT 0,
  `pay_fee_amount` DOUBLE(16,6) DEFAULT 0,
  `shipping_amount` int(11) DEFAULT '0',
  `processed` tinyint(1) NOT NULL DEFAULT '0',
  `viewed` tinyint(1) NOT NULL DEFAULT '0',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `discount_code` varchar(20) NOT NULL,
  `vendor_id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `vendors_orders`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `vendors_orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `vendors_orders_clients` (
  `id` int(11) NOT NULL,
  `name` varchar(500) NOT NULL,
  `receiptor_name` varchar(500) NOT NULL,
  `email` varchar(500) NOT NULL,
  `phone` varchar(500) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(500) NOT NULL,
  `post_code` varchar(500) NOT NULL,
  `notes` text NOT NULL,
  `for_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `vendors_orders_clients`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `vendors_orders_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

CREATE TABLE `keys` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`key` VARCHAR(40) NOT NULL,
	`level` INT(2) NOT NULL,
	`ignore_limits` TINYINT(1) NOT NULL DEFAULT '0',
	`date_created` INT(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE area (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  pid int(11) NOT NULL DEFAULT 0 COMMENT '父级',
  name varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  shortname varchar(30) NOT NULL DEFAULT '' COMMENT '简称',
  longitude varchar(30) NOT NULL DEFAULT '' COMMENT '经度',
  latitude varchar(30) NOT NULL DEFAULT '' COMMENT '纬度',
  level smallint(6) NOT NULL DEFAULT 0 COMMENT '级别',
  sort mediumint(9) NOT NULL DEFAULT 0 COMMENT '排序',
  status tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态1有效',
  PRIMARY KEY (id)
)
ENGINE = INNODB,
AUTO_INCREMENT = 460400501,
AVG_ROW_LENGTH = 84,
CHARACTER SET utf8,
COLLATE utf8_general_ci,
COMMENT = '地址表';

ALTER TABLE area ADD INDEX IDX_nc_area (name, shortname);

ALTER TABLE area ADD INDEX level (level, sort, status);

ALTER TABLE area ADD INDEX longitude (longitude, latitude);

ALTER TABLE area ADD INDEX pid (pid);

CREATE TABLE IF NOT EXISTS `recommendation_book` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_for` int(11) NOT NULL,
  `position` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `recommendation_book_translations` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `abbr` varchar(5) NOT NULL,
  `url` varchar(50) NOT NULL DEFAULT '',
  `for_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `recommendation_book` (`id`, `sub_for`, `position`) VALUES
(1, 0, 0),
(2, 0, 1),
(3, 0, 2),
(4, 0, 3),
(5, 0, 4),
(6, 0, 5),
(7, 0, 6);

INSERT INTO `recommendation_book` (`id`, `sub_for`, `position`) VALUES
(8, 1, 7),
(9, 1, 8),
(10, 1, 9),
(11, 1, 10),
(12, 1, 11),
(13, 1, 12),
(14, 1, 13),
(15, 1, 14),
(16, 2, 15),
(17, 2, 16),
(18, 2, 17),
(19, 2, 18),
(20, 2, 19),
(21, 2, 20),
(22, 2, 21),
(23, 2, 22),
(24, 2, 23),
(25, 2, 24);

INSERT INTO `recommendation_book` (`id`, `sub_for`, `position`) VALUES
(26, 3, 25),
(27, 3, 26),
(28, 3, 27),
(29, 3, 28),
(30, 3, 29),
(31, 3, 30),
(32, 4, 31),
(33, 4, 32),
(34, 4, 33),
(35, 4, 34),
(36, 4, 35),
(37, 4, 36),
(38, 4, 37),
(39, 4, 38),
(40, 4, 39),
(41, 4, 40),
(42, 4, 41);

INSERT INTO `recommendation_book` (`id`, `sub_for`, `position`) VALUES
(43, 5, 42),
(44, 5, 43),
(45, 5, 44),
(46, 5, 45),
(47, 5, 46),
(48, 5, 47),
(49, 5, 48),
(50, 5, 49),
(51, 5, 50),
(52, 5, 51);

INSERT INTO `recommendation_book` (`id`, `sub_for`, `position`) VALUES
(53, 6, 52),
(54, 6, 53),
(55, 6, 54),
(56, 6, 55),
(57, 6, 56),
(58, 6, 57),
(59, 6, 58),
(60, 6, 59),
(61, 6, 60),
(62, 6, 61),
(63, 6, 62);

INSERT INTO `recommendation_book` (`id`, `sub_for`, `position`) VALUES
(64, 7, 63),
(65, 7, 64),
(66, 7, 65),
(67, 7, 66),
(68, 7, 67),
(69, 7, 68),
(70, 7, 69),
(71, 7, 70),
(72, 7, 71),
(73, 7, 72),
(74, 7, 73),
(75, 7, 74),
(76, 7, 75),
(77, 7, 76),
(78, 7, 77),
(79, 7, 78),
(80, 7, 79),
(81, 7, 80),
(82, 7, 81),
(83, 7, 82),
(84, 7, 83);

INSERT INTO `recommendation_book_translations` (`id`, `name`, `abbr`, `for_id` ) VALUES
(1, '提高情商必看书单', 'zh', 1),
(2, '职场升职加薪必读', 'zh', 2),
(3, '高效率工作必读', 'zh', 3),
(4, '优秀管理者书单', 'zh', 4),
(5, '提升格局好书推荐', 'zh', 5),
(6, '财富自由必读', 'zh', 6),
(7, '人生必读', 'zh', 7);

INSERT INTO `recommendation_book_translations` (`id`, `name`, `abbr`, `for_id` ) VALUES
(8, '非暴力沟通', 'zh', 8),
(9, '情商', 'zh', 9),
(10, '少有人走的路', 'zh', 10),
(11, '洗脑术:怎样有逻辑地说服他人', 'zh', 11),
(12, '自控力', 'zh', 12),
(13, '爱的五种语言', 'zh', 13),
(14, '感谢自己的不完美', 'zh', 14),
(15, '为何家会伤人', 'zh', 15);

INSERT INTO `recommendation_book_translations` (`id`, `name`, `abbr`, `for_id` ) VALUES
(16, '金字塔原理', 'zh', 16),
(17, '学会提问', 'zh', 17),
(18, '请停止无效努力', 'zh', 18),
(19, '如何阅读一本书', 'zh',19),
(20, '问题解决力', 'zh', 20),
(21, '怦然心动的人生整理魔法', 'zh', 21),
(22, '鬼谷子', 'zh', 22),
(23, '让创意更有黏性', 'zh', 23),
(24, '阿里铁军销售心法：顶级销售的21条军规', 'zh', 24),
(25, '认知天性', 'zh', 25);

INSERT INTO `recommendation_book_translations` (`id`, `name`, `abbr`, `for_id` ) VALUES
(26, '高效能人士的七个习惯', 'zh', 26),
(27, '把时间当作朋友', 'zh', 27),
(28, '拖延心理学力', 'zh', 28),
(29, '小强升职记', 'zh',29),
(30, '麦肯锡工作法', 'zh', 30),
(31, '番茄工作法', 'zh', 31),
(32, '原则', 'zh', 32),
(33, '管理的实践', 'zh', 33),
(34, '卓有成效的管理者', 'zh', 34),
(35, '权利与领导', 'zh', 35),
(36, '自我发现与重塑', 'zh', 36),
(37, '经营者养成笔记', 'zh', 37),
(38, '浪潮之巅', 'zh', 38),
(39, '第五项修炼', 'zh', 39),
(40, '基业长青', 'zh', 40),
(41, '定位', 'zh', 41),
(42, '创业维艰', 'zh', 42);

INSERT INTO `recommendation_book_translations` (`id`, `name`, `abbr`, `for_id` ) VALUES
(43, '大问题', 'zh', 43),
(44, '超越感觉', 'zh', 44),
(45, '做出好决定', 'zh', 45),
(46, '系统之美', 'zh',46),
(47, '态度改变和社会影响', 'zh', 47),
(48, '沟通的艺术', 'zh', 48),
(49, '社会性动物', 'zh', 49),
(50, '哲学家们都干了些什么', 'zh', 50),
(51, '活出生命的意义', 'zh', 51),
(52, '曾国藩的正面与侧面', 'zh', 52);

INSERT INTO `recommendation_book_translations` (`id`, `name`, `abbr`, `for_id` ) VALUES
(53, '穷爸爸富爸爸', 'zh', 53),
(54, '小狗钱钱', 'zh', 54),
(55, '邻家的百万富翁', 'zh', 55),
(56, '财富自由之路', 'zh',56),
(57, '聪明的投资者', 'zh', 57),
(58, '穷查理宝典', 'zh', 58),
(59, '彼得林奇的成功投资', 'zh', 59),
(60, '巴比伦最富有的人', 'zh', 60),
(61, '钱的外遇', 'zh', 61),
(62, '有钱人跟你想的不一样', 'zh', 62),
(63, '投资最重要的事', 'zh', 63);

INSERT INTO `recommendation_book_translations` (`id`, `name`, `abbr`, `for_id` ) VALUES
(64, '红楼梦', 'zh', 64),
(65, '卡拉马佐夫兄弟', 'zh', 65),
(66, '史记', 'zh', 66),
(67, '天生有罪', 'zh',67),
(68, '雕刻时光', 'zh', 68),
(69, '莎士比亚全集', 'zh', 69),
(70, '福尔摩斯探案全集', 'zh', 70),
(71, '哥德尔、艾舍尔、巴赫', 'zh', 71),
(72, '三体', 'zh', 72),
(73, '少有人走的路', 'zh', 73),
(74, '艺术的故事', 'zh', 74),
(75, '寻路中国', 'zh', 75),
(76, '金锁记', 'zh', 76),
(77, '雅舍谈吃', 'zh', 77),
(78, '国史大纲', 'zh',78),
(79, '活着', 'zh', 79),
(80, 'ZOO', 'zh', 80),
(81, '全球通史', 'zh', 81),
(82, '批判性思维', 'zh', 82),
(83, '逻辑学导论', 'zh', 83),
(84, '白夜行', 'zh', 84);

CREATE TABLE IF NOT EXISTS `bestseller_list` (
  `id` int(11) NOT NULL,
  `list_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `bestseller_book` (
  `id` int(11) NOT NULL,
  `book_name` varchar(50) NOT NULL,
  `url` varchar(50) NOT NULL DEFAULT '',
  `for_id` int(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `bestseller_list` (`id`, `list_name` ) VALUES
(1, '飙升榜'),
(2, '新书榜'),
(3, '热搜榜'),
(4, '小说榜'),
(5, '总榜'),
(6, '神作榜');

INSERT INTO `bestseller_book` (`id`, `book_name`, `for_id` ) VALUES
(1, '哈利波特完整系列 J.K.罗琳', 1),
(2, '诺贝尔文学奖大师代表作品 Digital Lab', 1),
(3, '你就是光 乐传曲', 1),
(4, '一读就懂的道德经 王晨阳', 1),
(5, '时势：周期波动下的国家、社会和个人 小Lin', 1),
(6, '趣说汉朝407年 杨阳洋 ', 1),
(7, '奇迹之书 邓萨尼勋爵', 1),
(8, '每天学点佛学智慧：不生气 卢莉·墨墨', 1),
(9, '斯坦福大学写作课 华莱士·斯泰格那', 1),
(10, '日本激荡三十年 御厨贵', 1);

INSERT INTO `bestseller_book` (`id`, `book_name`, `for_id` ) VALUES
(11, '我不是废柴', 2),
(12, '法律的悖论', 2),
(13, '莫言长篇代表作', 2),
(14, '三大队', 2),
(15, '我在美国当精神科医生', 2),
(16, '半小时漫画中国地理 ', 2),
(17, '慢读《庄子》', 2),
(18, '此时世界有多少人正在想你', 2),
(19, '豆子芝麻茶', 2),
(20, '我的母亲做保洁', 2);

INSERT INTO `bestseller_book` (`id`, `book_name`, `for_id` ) VALUES
(21, '繁花', 3),
(22, '大江大河', 3),
(23, '哈利波特完整系列', 3),
(24, '超越好奇', 3),
(25, '玩耍是最认真的学习', 3),
(26, '瓦尔登湖 ', 3),
(27, '我在北京送快递', 3),
(28, '中国古代文化常识', 3),
(29, '易经系传别讲', 3),
(30, '记一忘三二', 3);

INSERT INTO `bestseller_book` (`id`, `book_name`, `for_id` ) VALUES
(31, '一句顶一万句', 4),
(32, '一地鸡毛', 4),
(33, '斗罗大陆', 4),
(34, '三体', 4),
(35, '深蓝的故事', 4),
(36, '布鲁克林有棵树', 4),
(37, '正好是你', 4),
(38, '小巷人家', 4),
(39, '杀死一只知更鸟', 4),
(40, '云边有个小卖部', 4);

INSERT INTO `bestseller_book` (`id`, `book_name`, `for_id` ) VALUES
(41, '三体（全集）', 5),
(42, '长安的荔枝', 5),
(43, '明朝那些事儿（全集）', 5),
(44, '活着', 5),
(45, '平凡的世界（全三册）', 5),
(46, '追风筝的人', 5),
(47, '南京大屠杀', 5),
(48, '杀死一只知更鸟（同名电影原著）', 5),
(49, '白夜行', 5),
(50, '白鹿原', 5),
(51, '我们仨', 5),
(52, '小王子', 5),
(53, '一个叫欧维的男人决定去死', 5),
(54, '置身事内：中国政府与经济发展', 5),
(55, '围城', 5),
(56, '绝叫', 5),
(57, '邓小平时代', 5),
(58, '蛤蟆先生去看心理医生', 5),
(59, '献给阿尔吉侬的花束', 5),
(60, '认知觉醒：开启自我改变的原动力', 5);

INSERT INTO `bestseller_book` (`id`, `book_name`, `for_id` ) VALUES
(61, '幸得诸君慰平生', 6),
(62, '心灵激荡：老俞对谈录', 6),
(63, '钢铁是怎样炼成的', 6),
(64, '兄弟俩', 6),
(65, '如果奔跑是我的宿命', 6),
(66, '如何抑止女性写作', 6),
(67, '每天都想陪伴你', 6),
(68, '羊道·深山夏牧场', 6),
(69, '头发这么少 去个理发店 还不给打折（银发川柳1）', 6),
(70, '非人哉.6', 6);

CREATE TABLE IF NOT EXISTS `platform_balances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `total_amount` DOUBLE(20,6) DEFAULT 0 COMMENT '平台销售总额',
  `platform_amount` DOUBLE(20,6) DEFAULT 0 COMMENT '平台分成总金额',
  `pay_fee_amount` DOUBLE(20,6) DEFAULT 0 COMMENT '支付渠道佣金总额',
  `vendors_amount` DOUBLE(20,6) DEFAULT 0 COMMENT '卖家分成总金额',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `platform_balances` (`id`, `total_amount`, `platform_amount`, `pay_fee_amount`, `vendors_amount`) VALUES
(1, 0.0, 0.0, 0.0, 0.0);

CREATE TABLE IF NOT EXISTS `vendors_balances` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vendor_id` int(10) NOT NULL ,
  `total_amount` DOUBLE(20,6) DEFAULT 0 COMMENT '卖家总金额',
  `balances` DOUBLE(20,6) DEFAULT 0 COMMENT '卖家余额',
  `withdraw_amount` DOUBLE(20,6) DEFAULT 0 COMMENT '卖家已提现金额',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `users_payment_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL ,
  `order_id` int(11) NOT NULL,
  `channel` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '交易渠道(1 支付宝)',
  `buyer_id` varchar(50) COMMENT '用户付款账号',
  `seller_id` varchar(50) COMMENT '平台收款账号',
  `notify_time` varchar(50) COMMENT '通知的发送时间',
  `notify_type` varchar(50) COMMENT '通知类型',
  `notify_id` varchar(128) COMMENT '通知校验 ID',
  `app_id` varchar(50) COMMENT '收款应用id',
  `out_trade_no` varchar(64) COMMENT '平台订单id',
  `out_biz_no` varchar(64) COMMENT '商家业务号。商家业务ID，通常是退款通知中返回的退款申请流水号',
  `trade_no` varchar(50) COMMENT '交易id',
  `trade_status` varchar(50) NOT NULL DEFAULT 'WAIT_BUYER_PAY' COMMENT '交易状态（WAIT_BUYER_PAY 交易创建，TRADE_SUCCESS 支付成功，TRADE_FINISHED 交易完成，TRADE_CLOSED 交易关闭）',
  `amount` DOUBLE(20,2) DEFAULT 0 COMMENT '订单金额。本次交易支付订单金额，单位为人民币（元），精确到小数点后 2 位',
  `receipt_amount` DOUBLE(20,2) DEFAULT 0 COMMENT '实收金额。商家在交易中实际收到的款项，单位为人民币（元），精确到小数点后 2 位',
  `buyer_pay_amount` DOUBLE(20,2) DEFAULT 0 COMMENT '用户在交易中支付的金额，单位为人民币（元），精确到小数点后 2 位',
  `refund_fee` DOUBLE(20,2) DEFAULT 0 COMMENT '总退款金额。退款通知中，返回总退款金额，单位为人民币（元），精确到小数点后 2 位',
  `subject` varchar(256) COMMENT '订单标题/商品标题/交易标题/订单关键字等，是请求时对应参数，会在通知中原样传回',
  `gmt_create` varchar(50) COMMENT '交易创建时间。格式为 yyyy-MM-dd HH:mm:ss',
  `gmt_payment` varchar(50) COMMENT '交易付款时间。格式为 yyyy-MM-dd HH:mm:ss',
  `gmt_refund` varchar(50) COMMENT '交易退款时间。格式为 yyyy-MM-dd HH:mm:ss.S',
  `gmt_close` varchar(50) COMMENT '交易结束时间。格式为 yyyy-MM-dd HH:mm:ss',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` varchar(50) COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `vendors_payment_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL ,
  `order_id` int(11) NOT NULL,
  `channel` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '交易渠道(1 支付宝)',
  `buyer_id` varchar(50) COMMENT '用户付款账号',
  `seller_id` varchar(50) COMMENT '平台收款账号',
  `notify_time` varchar(50) COMMENT '通知的发送时间',
  `notify_type` varchar(50) COMMENT '通知类型',
  `notify_id` varchar(128) COMMENT '通知校验 ID',
  `app_id` varchar(50) COMMENT '收款应用id',
  `out_trade_no` varchar(64) COMMENT '平台订单id',
  `out_biz_no` varchar(64) COMMENT '商家业务号。商家业务ID，通常是退款通知中返回的退款申请流水号',
  `trade_no` varchar(50) COMMENT '交易id',
  `trade_status` varchar(50) COMMENT '交易状态（WAIT_BUYER_PAY 交易创建，TRADE_SUCCESS 支付成功，TRADE_FINISHED 交易完成，TRADE_CLOSED 交易关闭）',
  `amount` DOUBLE(20,2) DEFAULT 0 COMMENT '订单金额。本次交易支付订单金额，单位为人民币（元），精确到小数点后 2 位',
  `receipt_amount` DOUBLE(20,2) DEFAULT 0 COMMENT '实收金额。商家在交易中实际收到的款项，单位为人民币（元），精确到小数点后 2 位',
  `buyer_pay_amount` DOUBLE(20,2) DEFAULT 0 COMMENT '用户在交易中支付的金额，单位为人民币（元），精确到小数点后 2 位',
  `refund_fee` DOUBLE(20,2) DEFAULT 0 COMMENT '总退款金额。退款通知中，返回总退款金额，单位为人民币（元），精确到小数点后 2 位',
  `subject` varchar(256) COMMENT '订单标题/商品标题/交易标题/订单关键字等，是请求时对应参数，会在通知中原样传回',
  `gmt_create` varchar(50) COMMENT '交易创建时间。格式为 yyyy-MM-dd HH:mm:ss',
  `gmt_payment` varchar(50) COMMENT '交易付款时间。格式为 yyyy-MM-dd HH:mm:ss',
  `gmt_refund` varchar(50) COMMENT '交易退款时间。格式为 yyyy-MM-dd HH:mm:ss.S',
  `gmt_close` varchar(50) COMMENT '交易结束时间。格式为 yyyy-MM-dd HH:mm:ss',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` varchar(50) COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;