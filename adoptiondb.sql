-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2025 at 03:46 PM
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

-- ----------------------------------------f----------------

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
  `imageDirectory` varchar(255) NOT NULL,
  `adoptedById` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pets`
--

INSERT INTO `pets` (`petID`, `name`, `type`, `breed`, `age`, `price`, `details`, `imageDirectory`, `adoptedById`) VALUES
(6, 'Bark Twain', 'Dog', 'Beagle', 2, 15000, '{\"gender\":\"Male\",\"color\":\"Brown and White\",\"coat_length\":\"Short\",\"personality\":\"Bark Twain may have been dealt a rough hand—neglected, abandoned, and left tied to a tree at the illegal POGO Boss Mansion—but this gentle giant never let that define him. Despite everything, he still holds onto hope that kindness and love are still in the cards for him.\",\"vaccinated\":true,\"spayed_neutered\":false}', 'images/homeImages/petPics/Bark-Twaine-BEAGLE 1.webp', NULL),
(7, 'Andarna', 'Small Animal', 'Guinea Pig', 1, 2500, '{\"gender\":\"Male\",\"color\":\"Orange and White\",\"coat_length\":\"Short\",\"personality\":\"Andarna is a gentle and calm guinea pig who loves fresh vegetables and quiet environments. Perfect for families with children who want a low-maintenance, affectionate pet.\",\"vaccinated\":true,\"spayed_neutered\":true}', 'images/homeImages/petPics/Andarna-GUINEA_PIG 1.webp', NULL),
(8, 'Cheetos', 'Small Animal', 'Syrian Hamster', 1, 1500, '{\"gender\":\"Male\",\"color\":\"Golden\",\"coat_length\":\"Short\",\"personality\":\"Cheetos is an active and curious hamster who loves exploring tunnels and running on his wheel. He\'s nocturnal and enjoys late-night adventures. Great for someone looking for an entertaining pocket pet.\",\"vaccinated\":false,\"spayed_neutered\":false}', 'images/homeImages/petPics/Cheetos-SYRIAN_HAMSTER 1.webp', NULL),
(9, 'Lunch', 'Dog', 'Bichon Frise', 1, 20000, '{\"gender\":\"Male\",\"color\":\"White\",\"coat_length\":\"Long\",\"personality\":\"Lunch is a playful and affectionate Bichon Frise who loves being the center of attention. He enjoys cuddles, playing fetch, and making new friends. His fluffy white coat requires regular grooming but his loving personality makes it all worthwhile.\",\"vaccinated\":true,\"spayed_neutered\":false}', 'images/homeImages/petPics/Lunch-BICHON_FRISE 1.webp', NULL),
(10, 'Gizmo', 'Dog', 'Papillon', 1, 18000, '{\"gender\":\"Male\",\"color\":\"White and Brown\",\"coat_length\":\"Long\",\"personality\":\"Gizmo is a smart and alert Papillon with distinctive butterfly-like ears. He\'s highly trainable, loves learning new tricks, and is great with older children. His elegant appearance and lively personality make him a wonderful companion.\",\"vaccinated\":true,\"spayed_neutered\":false}', 'images/homeImages/petPics/Gizmo-PAPILLON 1.webp', NULL),
(11, 'Bark Twain', 'Dog', 'Beagle', 2, 15000, '{\"gender\":\"Male\",\"color\":\"Brown and White\",\"coat_length\":\"Short\",\"personality\":\"Bark Twain may have been dealt a rough hand—neglected, abandoned, and left tied to a tree at the illegal POGO Boss Mansion—but this gentle giant never let that define him. Despite everything, he still holds onto hope that kindness and love are still in the cards for him.\",\"vaccinated\":true,\"spayed_neutered\":false}', 'images/homeImages/petPics/Bark-Twaine-BEAGLE 1.webp', NULL),
(12, 'Andarna', 'Small Animal', 'Guinea Pig', 1, 2500, '{\"gender\":\"Male\",\"color\":\"Orange and White\",\"coat_length\":\"Short\",\"personality\":\"Andarna is a gentle and calm guinea pig who loves fresh vegetables and quiet environments. Perfect for families with children who want a low-maintenance, affectionate pet.\",\"vaccinated\":true,\"spayed_neutered\":true}', 'images/homeImages/petPics/Andarna-GUINEA_PIG 1.webp', NULL),
(13, 'Cheetos', 'Small Animal', 'Syrian Hamster', 1, 1500, '{\"gender\":\"Male\",\"color\":\"Golden\",\"coat_length\":\"Short\",\"personality\":\"Cheetos is an active and curious hamster who loves exploring tunnels and running on his wheel. He\'s nocturnal and enjoys late-night adventures. Great for someone looking for an entertaining pocket pet.\",\"vaccinated\":false,\"spayed_neutered\":false}', 'images/homeImages/petPics/Cheetos-SYRIAN_HAMSTER 1.webp', NULL),
(14, 'Lunch', 'Dog', 'Bichon Frise', 1, 20000, '{\"gender\":\"Male\",\"color\":\"White\",\"coat_length\":\"Long\",\"personality\":\"Lunch is a playful and affectionate Bichon Frise who loves being the center of attention. He enjoys cuddles, playing fetch, and making new friends. His fluffy white coat requires regular grooming but his loving personality makes it all worthwhile.\",\"vaccinated\":true,\"spayed_neutered\":false}', 'images/homeImages/petPics/Lunch-BICHON_FRISE 1.webp', NULL),
(15, 'Gizmo', 'Dog', 'Papillon', 1, 18000, '{\"gender\":\"Male\",\"color\":\"White and Brown\",\"coat_length\":\"Long\",\"personality\":\"Gizmo is a smart and alert Papillon with distinctive butterfly-like ears. He\'s highly trainable, loves learning new tricks, and is great with older children. His elegant appearance and lively personality make him a wonderful companion.\",\"vaccinated\":true,\"spayed_neutered\":false}', 'images/homeImages/petPics/Gizmo-PAPILLON 1.webp', NULL);

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

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transactionId`, `petId`, `userId`, `userPayment`, `dateTimeCreated`, `meetGreetDateTime`, `status`, `location`, `evaluation`) VALUES
(1, 14, 27, 0, '2025-12-09 22:18:45', '2025-12-20 08:00:00', 'Application Approved', 'tarlac', '{\"applicantInfo\":{\"firstName\":\"dustin\",\"middleName\":\"briones\",\"lastName\":\"gualberto\",\"suffix\":\"\",\"occupation\":\"\",\"employer\":\"\",\"employerAddress\":\"\",\"email\":\"dustingualberto7@gmail.com\",\"phone\":\"09687488130\",\"address\":\"\"},\"homeEnvironment\":{\"housing\":\"House\",\"rent\":\"No\",\"landlordName\":\"\",\"landlordPhone\":\"\",\"adultsInHousehold\":\"\",\"childrenInHousehold\":\"\",\"otherPets\":\"No\",\"previousPets\":\"No\",\"averageAloneTime\":\"\"},\"petPreferences\":{\"reasons\":[\"Companion for child\"],\"otherReasonDetail\":\"\",\"gift\":\"No\",\"giftRecipientName\":\"\",\"giftRecipientPhone\":\"\",\"financialPrepared\":\"Yes\"},\"agreement\":{\"understand\":true,\"certify\":true,\"signature\":\"dustin\"},\"submittedAt\":\"2025-12-09T14:18:45.214Z\",\"placeholderPetId\":true,\"meetAndGreet\":{\"date\":\"2025-12-20 08:00pm:00\",\"location\":\"tarlac\",\"message\":\"awdadasd\"}}');

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
(27, 'dustin', 'hakdog', '$2y$10$aN5zrbrFUktU9rkG/UIiPeqDWwZ.bS/wfLE2jxEvmK1.oPc2LEsw.', 'admin', 'dustingualberto7@gmail.com'),
(28, 'kyran', 'kyky', '$2y$10$nVjpza/ERG5XdLRmH9VFyOriHOZHnvV4wRQqdSfYZYbmhOJ1qXyx2', 'admin', 'kyran@gmail.com'),
(29, 'mennard', 'nardy', '$2y$10$qHZNKZqJmPW0LBk56MZ6vusxGfcfEn.SelxkkDBnZF4ry1tCWzhcK', 'user', 'nardy@gmail.com'),
(32, 'chingchong', 'cheng', '$2y$10$daKG8dK6KjnMRZk.HLAAJeKz8ZheDIQRwmllGw1xyqWsoIyt01Pu2', 'user', 'cheng@gmail.com'),
(33, 'hope', 'lovejoyhope', '$2y$10$cB0qt3RNZDDYsY3niRE62eReXCdwOTjp.wiS/LO/rJkjbeuf3H6dO', 'user', 'hope@gmail.com');

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
  MODIFY `petID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transactionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
