/*
SQLyog Community v13.3.0 (64 bit)
MySQL - 8.4.5 : Database - TIBAART
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`TIBAART` /*!40100 DEFAULT CHARACTER SET utf8mb3 */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `TIBAART`;

/*Table structure for table `ADMIN` */

DROP TABLE IF EXISTS `ADMIN`;

CREATE TABLE `ADMIN` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL COMMENT '管理員名稱',
  `account` varchar(16) NOT NULL COMMENT '帳號',
  `password` varchar(255) NOT NULL COMMENT '密碼',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_UNIQUE` (`account`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COMMENT='後台管理員';

/*Data for the table `ADMIN` */

insert  into `ADMIN`(`id`,`name`,`account`,`password`) values 
(1,'test','123','456');

/*Table structure for table `ARTIST` */

DROP TABLE IF EXISTS `ARTIST`;

CREATE TABLE `ARTIST` (
  `id` int NOT NULL AUTO_INCREMENT,
  `account` varchar(16) NOT NULL COMMENT '帳號',
  `password` varchar(255) NOT NULL COMMENT '密碼',
  `email` varchar(45) NOT NULL COMMENT '信箱',
  `name` varchar(45) NOT NULL COMMENT '使用者名稱',
  `img` varchar(500) DEFAULT NULL COMMENT '藝術家頭像',
  `phone` varchar(15) NOT NULL COMMENT '連絡電話',
  `gender` enum('M','F','Other') NOT NULL DEFAULT 'Other',
  `birthday` date DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '修改日期',
  `bank_account` varchar(45) NOT NULL COMMENT '銀行帳戶',
  `per` tinyint(1) NOT NULL DEFAULT '1' COMMENT '權限',
  `intro` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_UNIQUE` (`account`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb3 COMMENT='藝術家會員';

/*Data for the table `ARTIST` */

insert  into `ARTIST`(`id`,`account`,`password`,`email`,`name`,`img`,`phone`,`gender`,`birthday`,`updated`,`bank_account`,`per`,`intro`) values 
(1,'TIBAART1','PASSWORD','TIBAART1@GMAIL.COM','Elia Nova','/assets/img/expoArea-artist.png','0912345678','F',NULL,'2025-06-07 14:18:43','12345678910',1,'出生於冰島雷克雅維克，自幼沉浸於極光與火山地貌的視覺震撼中。20歲時赴挪威卑爾根藝術學院深造，後旅居日本京都五年，受禪意美學影響深遠。'),
(23,'456','$2y$10$abEb8KJnalnkHyltkU1EYuRaJVovFJ93rLZ9fKr14TtNoQ5LC2q2K','tsai@mail.com','昇紘',NULL,'0911222333','Other','2025-03-10','2025-06-14 10:35:08','8512254626684654',1,'AI創作者');

/*Table structure for table `ARTWORK` */

DROP TABLE IF EXISTS `ARTWORK`;

CREATE TABLE `ARTWORK` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL COMMENT '作品名稱',
  `name_en` varchar(45) NOT NULL,
  `stuff` varchar(45) NOT NULL COMMENT '媒材',
  `size` varchar(45) NOT NULL COMMENT '尺寸',
  `img` varchar(500) NOT NULL COMMENT '作品圖',
  `note` varchar(300) NOT NULL COMMENT '作品備註',
  `artist_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ARTWORK_ARTIST_idx` (`artist_id`),
  CONSTRAINT `FK_ARTWORK_ARTIST` FOREIGN KEY (`artist_id`) REFERENCES `ARTIST` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COMMENT='作品';

/*Data for the table `ARTWORK` */

insert  into `ARTWORK`(`id`,`name`,`name_en`,`stuff`,`size`,`img`,`note`,`artist_id`) values 
(1,'無聲的風暴','The Quiet Storm','油畫','76 cm * 104 cm','/assets/img/expoArea-artwork4.png',' 這幅畫是凱婭在京都期間創作的代表作，融合日本枯山水的哲學與冰島風暴的視覺印象。畫面雖無明顯的動態，卻透過濃淡交錯的灰藍筆觸營造出「風未起而心已亂」的靜謐張力。她稱之為「情緒的慢性地震」。',1),
(2,'裂縫之下','Beneath the Fracture','油畫','76 cm * 104 cm','/assets/img/expoArea-artwork1.png',' 本畫靈感來自她親眼目睹的冰川裂縫，畫面以大面積裂痕與流動線條組成，象徵人內在的情緒斷層。她用此畫探討壓抑情感之下的潛流，並相信裂縫不是崩壞，而是讓光進入的通道。',1),
(3,'灰燼中的記憶','Ashes of Memory','油畫','76 cm * 104 cm','/assets/img/expoArea-artwork3.png',' 此作源自她年幼時家族遷移與失落的記憶碎片。畫面試圖表達「被歲月燒蝕的過去，如何以碎片殘火的形式，閃現在靜默的當下」。 她將記憶比喻為餘燼——未熄滅，卻再也無法復燃；只留痕跡，卻無法重構完整情感。',1),
(12,'adad','asdasd','asff','dasda','C:\\xampp\\htdocs\\TIBAART\\api/upload/expos/img_685062e7e8c12.jpg','sdasd',23),
(13,'adad','asdasd','asdad','asdasd','C:\\xampp\\htdocs\\TIBAART\\api\\upload\\exposimg_68506402bc6f9.jpg','asdad',23);

/*Table structure for table `DONATE` */

DROP TABLE IF EXISTS `DONATE`;

CREATE TABLE `DONATE` (
  `id` int NOT NULL AUTO_INCREMENT,
  `donate_no` varchar(20) NOT NULL COMMENT '贊助編號',
  `d_name` varchar(45) NOT NULL COMMENT '贊助人姓名',
  `d_email` varchar(45) NOT NULL COMMENT '贊助人信箱',
  `amount` int NOT NULL COMMENT '贊助金額',
  `share` int NOT NULL DEFAULT '10' COMMENT '抽成',
  `status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending' COMMENT '交易狀態',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新時間',
  `member_id` int DEFAULT NULL,
  `artist_id` int NOT NULL,
  `expo_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `donate_no_UNIQUE` (`donate_no`),
  KEY `FK_DONATE_MEMBER_idx` (`member_id`),
  KEY `FK_DONATE_ARTIST_idx` (`artist_id`),
  KEY `FK_DONATE_EXPO_idx` (`expo_id`),
  CONSTRAINT `FK_DONATE_ARTIST` FOREIGN KEY (`artist_id`) REFERENCES `ARTIST` (`id`),
  CONSTRAINT `FK_DONATE_EXPO` FOREIGN KEY (`expo_id`) REFERENCES `EXPO` (`id`),
  CONSTRAINT `FK_DONATE_MEMBER` FOREIGN KEY (`member_id`) REFERENCES `MEMBER` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='贊助';

/*Data for the table `DONATE` */

/*Table structure for table `EXPO` */

DROP TABLE IF EXISTS `EXPO`;

CREATE TABLE `EXPO` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL COMMENT '展覽名稱',
  `name_en` varchar(45) NOT NULL,
  `cover` varchar(500) NOT NULL COMMENT '展覽封面',
  `start` date NOT NULL COMMENT '開始日期',
  `end` date NOT NULL COMMENT '結束日期',
  `note` varchar(500) NOT NULL COMMENT '展覽描述',
  `status` enum('expoOff','expoOn','expoExit') NOT NULL DEFAULT 'expoOff' COMMENT '狀態',
  `artist_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_EXPO_ARTIST_idx` (`artist_id`),
  CONSTRAINT `FK_EXPO_ARTIST` FOREIGN KEY (`artist_id`) REFERENCES `ARTIST` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COMMENT='展覽';

/*Data for the table `EXPO` */

insert  into `EXPO`(`id`,`name`,`name_en`,`cover`,`start`,`end`,`note`,`status`,`artist_id`) values 
(1,'靜界焰光','The Luminous Silence','/assets/img/expo8.jpg','2025-06-07','2025-07-04','「當情感無以為繼，色彩替我們說話。」 凱婭·露希亞的創作長年在寂靜與爆發之間來回拉扯，她以自然隱喻人心，讓畫布成為風暴中心的靜止點。 本次展覽精選其近十年間五件代表作，以色彩為線索，從冷冽冰縫、綠林密靜，到炙熱的內燃記憶，呈現一場無語卻極具張力的感官旅程。觀者不需理解，僅需感受\n','expoOff',1),
(2,'夢境潰流','Deluge of Dreams','/assets/img/expo21.jpg','2025-06-07','2025-07-04','在這場名為《夢境潰流》的展覽中，艾莉亞·諾瓦試圖將無意識的風景具象化。透過她深層次的超現實油畫語彙，觀者將進入一場被記憶碎片、夢境殘影與象徵性符號構築而成的旅程。展覽主軸圍繞「心靈的裂隙與重構」，作品中常見模糊人物、扭曲空間與反覆出現的象徵物——如漂浮的階梯、消融的鐘錶、無臉的自我、漂浮的水母等——均反映藝術家對現實與潛意識邊界的叩問。\n','expoOff',1);

/*Table structure for table `EXPO_ARTWORK` */

DROP TABLE IF EXISTS `EXPO_ARTWORK`;

CREATE TABLE `EXPO_ARTWORK` (
  `expo_id` int DEFAULT NULL,
  `artwork_id` int DEFAULT NULL,
  KEY `FK_EXPO_ARTWORK_EXPO_idx` (`expo_id`),
  KEY `FK_EXPO_ARTWORK_ARTWORK_idx` (`artwork_id`),
  CONSTRAINT `FK_EXPO_ARTWORK_ARTWORK` FOREIGN KEY (`artwork_id`) REFERENCES `ARTWORK` (`id`),
  CONSTRAINT `FK_EXPO_ARTWORK_EXPO` FOREIGN KEY (`expo_id`) REFERENCES `EXPO` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

/*Data for the table `EXPO_ARTWORK` */

insert  into `EXPO_ARTWORK`(`expo_id`,`artwork_id`) values 
(1,1),
(1,2),
(1,3);

/*Table structure for table `MEMBER` */

DROP TABLE IF EXISTS `MEMBER`;

CREATE TABLE `MEMBER` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(45) NOT NULL COMMENT '信箱(帳號)',
  `password` varchar(255) DEFAULT NULL COMMENT '密碼',
  `name` varchar(45) DEFAULT NULL COMMENT '使用者名稱',
  `img` varchar(500) DEFAULT NULL COMMENT '會員頭像',
  `phone` varchar(15) DEFAULT NULL COMMENT '連絡電話',
  `gender` enum('M','F','Other') NOT NULL DEFAULT 'Other' COMMENT '性別',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `register` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '註冊時間',
  `updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '修改時間',
  `per` tinyint(1) NOT NULL DEFAULT '1' COMMENT '權限',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COMMENT='一般會員';

/*Data for the table `MEMBER` */

insert  into `MEMBER`(`id`,`email`,`password`,`name`,`img`,`phone`,`gender`,`birthday`,`register`,`updated`,`per`) values 
(1,'789@mail.com','987','testman','TIBAART/AvatarUpload/img_6846f637291b6.jpg','0911444888','M','2000-01-02','2025-06-10 09:43:22','2025-06-10 09:43:22',1);

/*Table structure for table `ORDER_DETAIL` */

DROP TABLE IF EXISTS `ORDER_DETAIL`;

CREATE TABLE `ORDER_DETAIL` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_name` varchar(45) NOT NULL COMMENT '下單時商品名稱',
  `quantity` int NOT NULL COMMENT '數量',
  `subtotal` decimal(10,2) NOT NULL COMMENT '下單時金額',
  `unit_price` decimal(8,2) NOT NULL COMMENT '單價',
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_ORDER_DETAIL_ORDERS_idx` (`order_id`) /*!80000 INVISIBLE */,
  KEY `FK_ORDER_DETAIL_PRODUCT_idx` (`product_id`),
  CONSTRAINT `FK_ORDER_DETAIL_ORDERS` FOREIGN KEY (`order_id`) REFERENCES `ORDERS` (`id`),
  CONSTRAINT `FK_ORDER_DETAIL_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `PRODUCT` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COMMENT='訂單明細';

/*Data for the table `ORDER_DETAIL` */

insert  into `ORDER_DETAIL`(`id`,`product_name`,`quantity`,`subtotal`,`unit_price`,`order_id`,`product_id`) values 
(1,'緯藝馬克杯',1,500.00,500.00,1,1);

/*Table structure for table `ORDERS` */

DROP TABLE IF EXISTS `ORDERS`;

CREATE TABLE `ORDERS` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL COMMENT '訂單編號',
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '下單時間',
  `address` varchar(45) NOT NULL COMMENT '收件人地址',
  `contact_name` varchar(45) DEFAULT NULL COMMENT '收件人姓名',
  `contact_phone` varchar(45) DEFAULT NULL COMMENT '收件人電話',
  `carrier` varchar(10) DEFAULT NULL COMMENT '載具號碼',
  `status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending' COMMENT '狀態',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '修改訂單時間',
  `shipping_fee` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '運費',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '總金額',
  `payment_method` varchar(50) DEFAULT NULL COMMENT '付款方式',
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid' COMMENT '付款狀態',
  `member_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number_UNIQUE` (`order_number`),
  KEY `FK_ORDERS_MEMBER_idx` (`member_id`),
  CONSTRAINT `FK_ORDERS_MEMBER` FOREIGN KEY (`member_id`) REFERENCES `MEMBER` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COMMENT='訂單';

/*Data for the table `ORDERS` */

insert  into `ORDERS`(`id`,`order_number`,`order_date`,`address`,`contact_name`,`contact_phone`,`carrier`,`status`,`updated_at`,`shipping_fee`,`total_amount`,`payment_method`,`payment_status`,`member_id`) values 
(1,'ORD202506091612','2025-06-09 16:30:19','asfghn','aaa','1161616161','','pending','2025-06-09 16:30:19',60.00,560.00,NULL,'unpaid',NULL);

/*Table structure for table `PRODUCT` */

DROP TABLE IF EXISTS `PRODUCT`;

CREATE TABLE `PRODUCT` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL COMMENT '商品名稱',
  `img` varchar(500) NOT NULL COMMENT '商品圖',
  `type` enum('tiba','artist') NOT NULL COMMENT '分類',
  `des` varchar(200) NOT NULL COMMENT '商品描述',
  `price` int NOT NULL COMMENT '價格',
  `stock` int NOT NULL COMMENT '庫存',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新時間',
  `status` enum('上架中','已下架','已售完') NOT NULL COMMENT '狀態',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3 COMMENT='商品';

/*Data for the table `PRODUCT` */

insert  into `PRODUCT`(`id`,`name`,`img`,`type`,`des`,`price`,`stock`,`updated`,`status`) values 
(1,'緯藝馬克杯','/assets/img/itemMug.jpg','tiba','材質：磁器\n規格：325ml\n簡約設計馬克杯，溫潤手感，陪你度過每一個溫暖時刻。',500,999,'2025-06-07 10:30:36','上架中'),
(2,'緯藝帆布袋','/assets/img/itemBag.jpg','tiba','材質：厚磅純棉帆布\n尺寸：約38x42cm\n質感自然，堅固耐用，陪你輕鬆出門每一天。',580,999,'2025-06-07 10:30:36','上架中'),
(3,'緯藝杯墊','/assets/img/itemCoaster.jpg','tiba','材質：軟木＋吸水陶瓷\n尺寸：直徑10cm\n吸水又防滑，為你的桌面增添一抹藝術感。',490,999,'2025-06-07 10:30:36','上架中'),
(4,'緯藝書籤','/assets/img/itemBookmark.jpg','tiba','材質：金屬＋彩印工藝\r 尺寸：約2.5x12cm\r 精緻輕巧，讓閱讀時光多一份藝術陪伴。',200,999,'2025-06-07 10:30:36','上架中'),
(5,'藝術家油畫','/assets/img/itemPainting.jpg','artist','媒材：油彩畫布\n尺寸：約40x50cm\n原創藝術作品，筆觸細膩，為空間注入靈魂與色彩。',3500,999,'2025-06-07 10:30:36','上架中'),
(6,'藝術家雕塑','/assets/img/itemSculpture.jpg','artist','媒材：樹脂＋金屬\n尺寸：約25x35x20cm\n現代感雕塑藝術，獨具造型與情感張力。',18000,999,'2025-06-07 10:30:36','上架中'),
(7,'緯藝明信片組','/assets/img/itemPostcard.jpg','tiba','材質：進口卡紙\n規格：一組6張，每張10x15cm\n搭配藝術插畫，傳遞祝福也傳遞美感。',250,999,'2025-06-07 10:30:36','上架中'),
(8,'緯藝筆記本','/assets/img/itemNotebook.jpg','tiba','材質：進口卡紙\n規格：一組6張，每張10x15cm\n搭配藝術插畫，傳遞祝福也傳遞美感。',320,999,'2025-06-07 10:30:36','上架中'),
(9,'藝術家攝影集','/assets/img/itemPhoto.jpg','artist','媒材：藝術微噴＋無酸相紙\n尺寸：30x40cm（含白邊）\n原創攝影作品，凝視瞬間的永恆詩意。',4200,999,'2025-06-07 10:30:36','上架中'),
(10,'藝術家陶藝','/assets/img/itemCeramic.jpg','artist','媒材：高溫陶＋釉彩\n尺寸：約15x15x20cm\n質樸與靈感並存，點綴生活的藝術小物。',5200,999,'2025-06-07 10:30:36','上架中'),
(11,'藝術家紙燈罩','/assets/img/itemPaperArt.jpg','artist','材質：進口紙＋手工摺製\n尺寸：約25x25x30cm\n柔和光影中的藝術設計，為空間注入溫暖氣息。',1600,999,'2025-06-07 10:30:36','上架中'),
(12,'藝術家版畫','/assets/img/itemPrint.jpg','artist','媒材：數位版畫＋簽名＋限量編號\n尺寸：30x30cm\n經典作品再製，收藏級限量藝術印刷。',2800,999,'2025-06-07 10:30:36','上架中');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
