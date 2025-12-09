-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 11:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `adoptiondb`
--

-- --------------------------------------------------------

--
-- Table structure for table `likedpet`
--

CREATE TABLE `likedpet` (
  `likedPetId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `petId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `petID` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `breed` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `price` double NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`details`)),
  `imageDirectory` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transactionId` int(11) NOT NULL,
  `petId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `userPayment` double NOT NULL,
  `dateTimeCreated` datetime NOT NULL DEFAULT current_timestamp(),
  `meetGreetDateTime` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `location` text NOT NULL,
  `evaluation` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`evaluation`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `privilege` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `name`, `username`, `password`, `privilege`, `email`) VALUES
(27, 'dustin', 'hakdog', '$2y$10$aN5zrbrFUktU9rkG/UIiPeqDWwZ.bS/wfLE2jxEvmK1.oPc2LEsw.', 'user', 'dustingualberto7@gmail.com'),
(28, 'kyran', 'kyky', '$2y$10$nVjpza/ERG5XdLRmH9VFyOriHOZHnvV4wRQqdSfYZYbmhOJ1qXyx2', 'user', 'kyran@gmail.com'),
(29, 'mennard', 'nardy', '$2y$10$qHZNKZqJmPW0LBk56MZ6vusxGfcfEn.SelxkkDBnZF4ry1tCWzhcK', 'user', 'nardy@gmail.com'),
(32, 'chingchong', 'cheng', '$2y$10$daKG8dK6KjnMRZk.HLAAJeKz8ZheDIQRwmllGw1xyqWsoIyt01Pu2', 'user', 'cheng@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `likedpet`
--
ALTER TABLE `likedpet`
  ADD PRIMARY KEY (`likedPetId`),
  ADD UNIQUE KEY `user_pet_unique` (`userId`,`petId`),
  ADD KEY `fk_likedpet_pet` (`petId`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`petID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transactionId`),
  ADD UNIQUE KEY `pet_user_unique` (`petId`,`userId`),
  ADD KEY `userId_to_transaction` (`userId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `likedpet`
--
ALTER TABLE `likedpet`
  MODIFY `likedPetId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `petID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transactionId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `likedpet`
--
ALTER TABLE `likedpet`
  ADD CONSTRAINT `fk_likedpet_pet` FOREIGN KEY (`petId`) REFERENCES `pets` (`petID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_likedpet_user` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transaction_pet` FOREIGN KEY (`petId`) REFERENCES `pets` (`petID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaction_user` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
