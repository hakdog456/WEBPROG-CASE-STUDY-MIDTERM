-- --------------------------------------------------------
-- Database: `adoptiondb`
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `adoptiondb`;
USE `adoptiondb`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `priveledge` varchar(50) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data
INSERT INTO `users` (`userId`, `name`, `username`, `password`, `priveledge`) VALUES
(1, 'Dustin Gualberto', 'hakdog', 'webprogdabest', 'admin'),
(21, 'mennard Ezekiel', 'mennard', 'gwapoAko', 'user'),
(23, 'Kyran Solomon', 'ky', 'heynow', 'user');

-- --------------------------------------------------------
-- Table structure for table `pets`
-- --------------------------------------------------------
CREATE TABLE `pets` (
  `petID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `breed` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `price` double NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`details`)),
  PRIMARY KEY (`petID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `likedpet`
-- --------------------------------------------------------
CREATE TABLE `likedpet` (
  `likedPetId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `petId` int(11) NOT NULL,
  PRIMARY KEY (`likedPetId`),
  UNIQUE KEY `user_pet_unique` (`userId`,`petId`),
  CONSTRAINT `fk_likedpet_user` FOREIGN KEY (`userId`) REFERENCES `users`(`userId`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_likedpet_pet` FOREIGN KEY (`petId`) REFERENCES `pets`(`petID`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `transactions`
-- --------------------------------------------------------
CREATE TABLE `transactions` (
  `transactionId` int(11) NOT NULL AUTO_INCREMENT,
  `petId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `userPayment` double NOT NULL,
  `dateTimeCreated` datetime NOT NULL DEFAULT current_timestamp(),
  `meetGreetDateTime` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `location` text NOT NULL,
  `evaluation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`evaluation`)),
  PRIMARY KEY (`transactionId`),
  UNIQUE KEY `pet_user_unique` (`petId`,`userId`),
  KEY `userId_to_transaction` (`userId`),
  CONSTRAINT `fk_transaction_pet` FOREIGN KEY (`petId`) REFERENCES `pets`(`petID`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `fk_transaction_user` FOREIGN KEY (`userId`) REFERENCES `users`(`userId`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
