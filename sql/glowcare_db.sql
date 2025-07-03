-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2025 at 10:49 AM
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
-- Database: `glowcare_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `shipping_name` varchar(100) NOT NULL,
  `shipping_email` varchar(100) NOT NULL,
  `shipping_phone` varchar(15) NOT NULL,
  `shipping_address` varchar(200) NOT NULL,
  `shipping_city` varchar(50) NOT NULL,
  `shipping_province` varchar(50) NOT NULL,
  `shipping_zip` varchar(10) NOT NULL,
  `shipping_country` varchar(50) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `card_number` varchar(100) DEFAULT NULL,
  `card_expiry` varchar(10) DEFAULT NULL,
  `card_cvv` varchar(10) DEFAULT NULL,
  `card_name` varchar(100) DEFAULT NULL,
  `items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`items`)),
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `user_id`, `shipping_name`, `shipping_email`, `shipping_phone`, `shipping_address`, `shipping_city`, `shipping_province`, `shipping_zip`, `shipping_country`, `payment_method`, `card_number`, `card_expiry`, `card_cvv`, `card_name`, `items`, `subtotal`, `shipping_cost`, `tax`, `total`, `status`, `order_date`, `updated_at`) VALUES
(1, 'GC202507026912', 'Battad11', 'Julius', 'juliussamar2@gmail.com', '09239736612', '1001 UE, Recto', 'Manila City', 'Manila', '1029', 'Philippines', 'gcash', '', '', '', '', '[{\"product\":{\"id\":3,\"name\":\"Baby Daily Lotion\",\"short_description\":\"Rich, nourishing night cream for deep overnight hydration.\",\"description\":\"Cetaphil\\u2019s Gentle Baby Lotion is designed to soothe and nourish, while moisturizing and protecting your baby\\u2019s skin from dryness.\",\"price\":38.5,\"image\":\"products\\/Cetaphil Baby Daily Lotion.png\",\"rating\":4.6,\"category\":\"moisturizers\",\"features\":[\"Contains Retinol and Peptides\",\"Deep overnight hydration\",\"Anti-aging benefits\",\"Repairs and rejuvenates\",\"Rich, luxurious texture\"],\"ingredients\":\"Aqua, Glycerin, Caprylic\\/Capric Triglyceride, Cetearyl Alcohol, Retinol, Palmitoyl Pentapeptide-4, Ceramide NP, Squalane, Dimethicone, Sodium Hyaluronate, Tocopherol.\",\"usage\":\"Apply a generous amount to clean face and neck every evening. Gently massage until fully absorbed. Use sunscreen during the day when using this product as it contains Retinol.\",\"in_stock\":true},\"quantity\":3,\"total\":115.5}]', 115.50, 0.00, 9.24, 124.74, 'pending', '2025-07-02 09:35:14', '2025-07-02 09:35:14'),
(2, 'GC202507033241', 'JAZMARKLOVEUE', 'Allen Mark', 'daileg.jefferson@ue.edu.ph', '09993692799', 'Samapaloc, Manila', 'Antipolo', 'Rizal', '1870', 'Philippines', 'gcash', '', '', '', '', '[{\"product\":{\"id\":14,\"name\":\"Rich Night Cream\",\"short_description\":\"Maintains skin hydration overnight while gently soothing skin.\",\"description\":\"Thoroughly remove dirt, excess oil and makeup without irritation. Ideal for sensitive skin.\",\"price\":24.99,\"image\":\"products\\/Rich Night Cream.png\",\"rating\":4.5,\"category\":\"cleansers\",\"features\":[\"Soap-free formula\",\"Suitable for all skin types\",\"Removes makeup effectively\",\"pH-balanced\",\"Dermatologist tested\"],\"ingredients\":\"Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.\",\"usage\":\"Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.\",\"in_stock\":true},\"quantity\":1,\"total\":24.99}]', 24.99, 9.99, 2.00, 36.98, 'pending', '2025-07-03 06:43:52', '2025-07-03 06:43:52');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `short_description` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `category` varchar(50) DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `ingredients` text DEFAULT NULL,
  `usage_instructions` text DEFAULT NULL,
  `in_stock` tinyint(1) DEFAULT 1,
  `stock_quantity` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `date_of_birth` date NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `street` varchar(200) NOT NULL,
  `city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `country` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `gender`, `date_of_birth`, `phone_number`, `email`, `street`, `city`, `province`, `zip_code`, `country`, `username`, `password_hash`, `created_at`, `updated_at`) VALUES
(4, 'Gilliane', 'Female', '2005-03-18', '09993692799', 'villon.gillianegail@ue.edu.ph', 'L8 Villa Rhomas Subd.', 'Antipolo', 'Rizal', '1870', 'Philippines', 'annalynlovejacob', '$2y$10$09CniY.wdzmXGshH5P692.U38UOZPbxPjcaWwfGFgdrdaXoJM0EXe', '2025-07-03 07:32:32', '2025-07-03 07:32:32'),
(5, 'Annalyn', 'Female', '2005-09-18', '09993692799', 'gillianegail@ue.edu.ph', 'L8 Villa Rhomas', 'Antipolo', 'Rizal', '1870', 'Philippines', 'FREIN123', '$2y$10$fdt8y0TvXAWnOvcl4R2xhOc8MuUAG5tUUL9Lrez0zJgPHU3RJhPlG', '2025-07-03 07:45:30', '2025-07-03 07:45:30'),
(6, 'Jazmark Jacob', 'Male', '0005-03-18', '09993692799', 'gailannalyn@ue.edu.ph', 'L8 Villa Rhomas', 'Antipolo', 'Rizal', '1870', 'Philippines', 'Annalyn123', '$2y$10$CB.Vje3.HsSqU98lTa676uuOK1FV.Kti8SyF8iZzetlfymRJEb9Tq', '2025-07-03 07:47:22', '2025-07-03 07:47:22'),
(7, 'GlowCare Admin', 'Female', '1992-11-22', '09283615523', 'admin.glowcare@ue.edu.ph', 'Glowcare', 'Manila', 'Manila', '1016', 'Philippines', 'adminglowcare123', '$2y$10$jh6l1e4E8TVl8QxxwTA1Ie8mG71wWDF0ptdPgeYhz4e8PTVYp7haa', '2025-07-03 08:29:11', '2025-07-03 08:39:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('customer','admin','staff') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_order_date` (`order_date`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_in_stock` (`in_stock`),
  ADD KEY `idx_rating` (`rating`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
