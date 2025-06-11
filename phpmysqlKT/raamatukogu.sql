-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Loomise aeg: Juuni 11, 2025 kell 02:16 PL
-- Serveri versioon: 10.4.32-MariaDB
-- PHP versioon: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Andmebaas: `raamatukogu`
--

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `autorid`
--

CREATE TABLE `autorid` (
  `id` int(11) NOT NULL,
  `eesnimi` varchar(50) NOT NULL,
  `perekonnanimi` varchar(50) NOT NULL,
  `synniaeg` date DEFAULT NULL,
  `riik` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Andmete tõmmistamine tabelile `autorid`
--

INSERT INTO `autorid` (`id`, `eesnimi`, `perekonnanimi`, `synniaeg`, `riik`) VALUES
(1, 'Jaan', 'Kross', '1920-02-19', 'Eesti'),
(2, 'A. H.', 'Tammsaare', '1878-01-30', 'Eesti'),
(3, 'Stephen', 'King', '1947-09-21', 'USA'),
(4, 'J. K.', 'Rowling', '1965-07-31', 'Suurbritannia'),
(5, 'Andrus', 'Kivirähk', '1970-08-17', 'Eesti'),
(6, 'imre', 'tard', '2025-06-12', 'estonia');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `broneeringud`
--

CREATE TABLE `broneeringud` (
  `id` int(11) NOT NULL,
  `kasutaja_id` int(11) NOT NULL,
  `raamat_id` int(11) NOT NULL,
  `broneeringu_kuupaev` date NOT NULL,
  `voimalik_laenutuse_kuupaev` date NOT NULL,
  `staatus` enum('ootel','tühistatud','täidetud') DEFAULT 'ootel',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `kasutajad`
--

CREATE TABLE `kasutajad` (
  `id` int(11) NOT NULL,
  `eesnimi` varchar(50) NOT NULL,
  `perekonnanimi` varchar(50) NOT NULL,
  `isikukood` varchar(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `parool` varchar(255) NOT NULL,
  `roll` enum('admin','külastaja') DEFAULT 'külastaja',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Andmete tõmmistamine tabelile `kasutajad`
--

INSERT INTO `kasutajad` (`id`, `eesnimi`, `perekonnanimi`, `isikukood`, `email`, `parool`, `roll`, `created_at`) VALUES
(1, 'imre', 'tard', '11111111111', 'imre.tard@gmail.com', '$2y$10$tyMfE4lucankHXFXxmCISux1hQYwFBUBq1zd97esOEwZRWJ1tkW5m', 'külastaja', '2025-06-11 10:34:17'),
(2, 'Admin', 'Admin', '12345678901', 'admin@raamatukogu.ee', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-06-11 11:21:46'),
(3, 'Tavakasutaja', 'Kasutaja', '23456789012', 'kasutaja@raamatukogu.ee', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'külastaja', '2025-06-11 11:21:46'),
(4, 'Mari', 'Maasikas', '34567890123', 'mari@raamatukogu.ee', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'külastaja', '2025-06-11 11:21:46'),
(5, 'admin', 'admin', '22222222222', 'admin@admin.ee', '22222222', 'admin', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `laenutused`
--

CREATE TABLE `laenutused` (
  `id` int(11) NOT NULL,
  `kasutaja_id` int(11) NOT NULL,
  `raamat_id` int(11) NOT NULL,
  `algus_kuupaev` date NOT NULL,
  `lopp_kuupaev` date NOT NULL,
  `tagastatud` tinyint(1) DEFAULT 0,
  `tagastamise_kuupaev` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Andmete tõmmistamine tabelile `laenutused`
--

INSERT INTO `laenutused` (`id`, `kasutaja_id`, `raamat_id`, `algus_kuupaev`, `lopp_kuupaev`, `tagastatud`, `tagastamise_kuupaev`, `created_at`) VALUES
(1, 2, 1, '2025-06-06', '2025-06-20', 0, NULL, '2025-06-11 11:21:46'),
(2, 3, 4, '2025-06-01', '2025-06-15', 0, NULL, '2025-06-11 11:21:46'),
(3, 2, 3, '2025-05-22', '2025-06-05', 1, '2025-06-11', '2025-06-11 11:21:46'),
(4, 3, 2, '2025-05-27', '2025-06-10', 1, NULL, '2025-06-11 11:21:46'),
(5, 1, 4, '2025-06-11', '2025-06-25', 0, NULL, '2025-06-11 11:25:53');

-- --------------------------------------------------------

--
-- Tabeli struktuur tabelile `raamatud`
--

CREATE TABLE `raamatud` (
  `id` int(11) NOT NULL,
  `pealkiri` varchar(255) NOT NULL,
  `autor_id` int(11) NOT NULL,
  `isbn` varchar(17) NOT NULL,
  `aasta` int(11) DEFAULT NULL,
  `kirjeldus` text DEFAULT NULL,
  `eksemplarid` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Andmete tõmmistamine tabelile `raamatud`
--

INSERT INTO `raamatud` (`id`, `pealkiri`, `autor_id`, `isbn`, `aasta`, `kirjeldus`, `eksemplarid`) VALUES
(1, 'Kolme katku vahel', 1, '978-9985-65-408-4', 2004, 'Jaan Krossi ajalooline romaan', 5),
(2, 'Tõde ja õigus I', 2, '978-9985-65-409-1', 1926, 'Tammsaare viieosaline romaanisari', 5),
(3, 'It', 3, '978-0-670-81302-5', 1986, 'Õudusromaan klounist Pennywisest', 2),
(4, 'Harry Potter ja tarkade kivi', 4, '978-1-4088-5565-4', 1997, 'Esimene raamat Harry Potteri sarjast', 4),
(5, 'Rehepapp', 5, '978-9985-65-410-7', 2000, 'Kivirähki tuntud romaan eesti rahvajuttudel', 3),
(8, 'eeeeee', 6, '978-9985-65-409-9', 2007, 'e eee e', 4);

--
-- Indeksid tõmmistatud tabelitele
--

--
-- Indeksid tabelile `autorid`
--
ALTER TABLE `autorid`
  ADD PRIMARY KEY (`id`);

--
-- Indeksid tabelile `broneeringud`
--
ALTER TABLE `broneeringud`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kasutaja_id` (`kasutaja_id`),
  ADD KEY `raamat_id` (`raamat_id`);

--
-- Indeksid tabelile `kasutajad`
--
ALTER TABLE `kasutajad`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isikukood` (`isikukood`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeksid tabelile `laenutused`
--
ALTER TABLE `laenutused`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kasutaja_id` (`kasutaja_id`),
  ADD KEY `raamat_id` (`raamat_id`);

--
-- Indeksid tabelile `raamatud`
--
ALTER TABLE `raamatud`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `autor_id` (`autor_id`);

--
-- AUTO_INCREMENT tõmmistatud tabelitele
--

--
-- AUTO_INCREMENT tabelile `autorid`
--
ALTER TABLE `autorid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT tabelile `broneeringud`
--
ALTER TABLE `broneeringud`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT tabelile `kasutajad`
--
ALTER TABLE `kasutajad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT tabelile `laenutused`
--
ALTER TABLE `laenutused`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT tabelile `raamatud`
--
ALTER TABLE `raamatud`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tõmmistatud tabelite piirangud
--

--
-- Piirangud tabelile `broneeringud`
--
ALTER TABLE `broneeringud`
  ADD CONSTRAINT `broneeringud_ibfk_1` FOREIGN KEY (`kasutaja_id`) REFERENCES `kasutajad` (`id`),
  ADD CONSTRAINT `broneeringud_ibfk_2` FOREIGN KEY (`raamat_id`) REFERENCES `raamatud` (`id`);

--
-- Piirangud tabelile `laenutused`
--
ALTER TABLE `laenutused`
  ADD CONSTRAINT `laenutused_ibfk_1` FOREIGN KEY (`kasutaja_id`) REFERENCES `kasutajad` (`id`),
  ADD CONSTRAINT `laenutused_ibfk_2` FOREIGN KEY (`raamat_id`) REFERENCES `raamatud` (`id`);

--
-- Piirangud tabelile `raamatud`
--
ALTER TABLE `raamatud`
  ADD CONSTRAINT `raamatud_ibfk_1` FOREIGN KEY (`autor_id`) REFERENCES `autorid` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
