/*
SQLyog Professional v13.1.1 (64 bit)
MySQL - 8.0.30 : Database - ciptaprocpanel_mmsi
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE `ciptaprocpanel_mmsi`;

/*Table structure for table `acct_deposito` */

DROP TABLE IF EXISTS `acct_deposito`;

CREATE TABLE `acct_deposito` (
  `deposito_id` int NOT NULL AUTO_INCREMENT,
  `account_id` int DEFAULT '0',
  `account_basil_id` int NOT NULL DEFAULT '0',
  `deposito_code` varchar(20) DEFAULT '',
  `deposito_name` varchar(50) DEFAULT '',
  `deposito_number` int DEFAULT '0',
  `deposito_period` int DEFAULT '0',
  `deposito_interest_period` int DEFAULT '0',
  `deposito_interest_rate` decimal(10,2) DEFAULT '0.00',
  `deposito_availability` int DEFAULT '0',
  `deposito_token` varchar(250) DEFAULT '',
  `deposito_point` int DEFAULT '0',
  `data_state` decimal(1,0) DEFAULT '0',
  `created_id` int DEFAULT '0',
  `created_on` datetime DEFAULT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deposito_penalty_percentage` decimal(20,2) DEFAULT NULL,
  `deposito_commission_agent_percentage` decimal(20,2) DEFAULT NULL,
  `deposito_commission_supervisor_percentage` decimal(20,2) DEFAULT NULL,
  `deposito_commission_period` int DEFAULT NULL,
  `deposito_commission_agent_percentage_next` decimal(20,2) DEFAULT NULL,
  `deposito_commission_supervisor_percentage_next` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`deposito_id`),
  KEY `FK_acct_deposito_account_id` (`account_id`),
  KEY `deposito_code` (`deposito_code`),
  KEY `deposito_token` (`deposito_token`),
  KEY `data_state` (`data_state`),
  KEY `created_id` (`created_id`),
  CONSTRAINT `acct_deposito_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `acct_account` (`account_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb3;

/*Data for the table `acct_deposito` */

insert  into `acct_deposito`(`deposito_id`,`account_id`,`account_basil_id`,`deposito_code`,`deposito_name`,`deposito_number`,`deposito_period`,`deposito_interest_period`,`deposito_interest_rate`,`deposito_availability`,`deposito_token`,`deposito_point`,`data_state`,`created_id`,`created_on`,`last_update`,`deposito_penalty_percentage`,`deposito_commission_agent_percentage`,`deposito_commission_supervisor_percentage`,`deposito_commission_period`,`deposito_commission_agent_percentage_next`,`deposito_commission_supervisor_percentage_next`) values 
(24,687,635,'001','Simpanan Berjangka 12 Bulan',0,12,2,10.00,4,'',12,1,37,'2022-10-26 14:55:15','2023-02-22 09:22:13',NULL,NULL,NULL,NULL,NULL,NULL),
(25,565,567,'2000','tes',0,12,2,12.00,21,'',34,1,37,'2022-10-29 09:41:02','2022-10-29 09:41:13',NULL,NULL,NULL,NULL,NULL,NULL),
(26,687,635,'SC01','SIMBAKO Cerdas',0,12,2,6.00,75,'',0,0,116,'2022-12-01 15:14:25','2024-03-23 11:35:13',2.50,1.05,1.00,12,NULL,NULL),
(27,687,635,'SP01','SIMBAKO Prestige',0,12,2,6.00,98,'',0,0,116,'2023-07-10 16:23:53','2024-03-23 11:35:29',1.00,1.00,1.00,12,NULL,NULL),
(28,749,753,'SMS-001-3A','Simpanan Maju Sejahtera 3 bulan A',0,3,2,7.50,98,'',0,0,37,'2024-03-25 12:00:23','2024-03-25 15:11:50',2.50,4.13,0.00,3,4.23,0.00),
(29,749,753,'SMS-001-3B','Simpanan Maju Sejahtera 3 Bulan B',0,3,2,8.50,100,'',0,0,37,'2024-03-25 13:48:54','2024-03-25 15:11:28',2.50,3.88,0.00,3,4.17,0.00),
(30,749,753,'SMS-001-3C','Simpanan Maju Sejahtera 3 Bulan C',0,3,2,9.50,100,'',0,0,37,'2024-03-25 13:51:50','2024-03-25 15:11:16',2.50,3.63,0.00,3,3.62,0.00),
(31,750,754,'SMS-001-6A','Simpanan Maju Sejahtera 6 Bulan A',0,6,2,8.50,100,'',0,0,37,'2024-03-25 13:55:53','2024-03-25 15:11:04',2.50,7.75,0.00,6,1.29,0.00),
(32,750,754,'SMS-001-6B','Simpanan Maju Sejahtera 6 Bulan B',0,6,2,9.50,100,'',0,0,37,'2024-03-25 13:59:21','2024-03-25 15:10:52',2.50,14.50,0.00,6,1.20,0.00),
(33,750,754,'SMS-001-6C','Simpanan Maju Sejahtera 6 Bulan C',0,6,2,10.50,100,'',0,0,37,'2024-03-25 14:02:56','2024-03-25 15:10:26',2.50,3.38,0.00,6,1.68,0.00),
(34,751,755,'SMS-001-9A','Simpanan Maju Sejahtera 9 Bulan C',0,9,2,9.50,100,'',0,0,37,'2024-03-25 15:05:08','2024-03-25 15:10:09',2.50,10.88,0.00,9,0.40,0.00),
(35,751,755,'SMS-001-9B','Simpanan Maju Sejahtera 9 Bulan B',0,9,2,10.50,100,'',0,0,37,'2024-03-25 15:09:54','2024-03-25 15:09:54',2.50,10.13,0.00,9,0.37,0.00),
(36,751,755,'SMS-001-9C','Simpanan Maju Sejahtera 9 Bulan C',0,9,2,11.50,100,'',0,0,37,'2024-03-25 15:29:36','2024-03-25 15:29:36',2.50,9.38,0.00,9,0.34,0.00),
(37,635,635,'SMS-001-12A','Simpanan Maju Sejahtera 12 Bulan A',0,12,2,11.50,100,'',0,0,37,'2024-03-25 15:31:39','2024-03-25 15:31:39',2.50,12.50,0.00,1,0.00,0.00),
(38,635,635,'SMS-001-12B','Simpanan Maju Sejahtera 12 Bulan B',0,12,2,12.50,100,'',0,0,37,'2024-03-25 15:33:24','2024-03-25 15:37:41',2.50,11.50,0.00,1,0.00,0.00),
(39,635,635,'SMS-001-12C','Simpanan Maju Sejahtera 12 Bulan C',0,12,2,13.50,100,'',0,0,37,'2024-03-25 15:35:54','2024-03-25 15:35:55',2.50,10.50,0.00,1,0.00,0.00);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
