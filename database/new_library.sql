-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2023 at 11:40 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `new_library`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `Delete_Book` (IN `p_book_id` INT)   BEGIN
   
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION
    BEGIN
        
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'An error occurred during the delete operation';
    END;

   
    START TRANSACTION;

    
    DELETE FROM books WHERE book_id = p_book_id;

 
    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Delete_Borrower` (IN `p_borrower_id` INT)   BEGIN
    -- checheck kang existing
    IF (SELECT COUNT(*) FROM borrowers WHERE borrower_id = p_borrower_id) > 0 THEN
        
        DELETE FROM borrowers WHERE borrower_id = p_borrower_id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Insert_Books` (IN `p_book_id` INT(11), IN `p_book_title` VARCHAR(100), IN `p_book_author` VARCHAR(35), IN `p_book_description` VARCHAR(255), IN `p_isbn` VARCHAR(17))   BEGIN
    
    DECLARE v_id INT;
    DECLARE v_date_created DATETIME;
    DECLARE v_date_updated DATETIME;

    
    SET v_id = NULL;
    SET v_date_created = NOW();
    SET v_date_updated = NOW();

   
    INSERT INTO books (id, book_id, book_title, book_author, book_description, isbn, date_created, date_updated)
    VALUES (v_id, p_book_id, p_book_title, p_book_author, p_book_description, p_isbn, v_date_created, v_date_updated);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Insert_Borrowed_Books` (IN `p_book_id` INT, IN `p_borrower_id` INT, IN `p_date_borrowed` DATE, IN `p_date_return` DATE, IN `p_date_received` DATE)   BEGIN
    DECLARE v_book_title VARCHAR(255);
    DECLARE v_first_name VARCHAR(255);
    DECLARE v_last_name VARCHAR(255);

 
    SELECT book_title INTO v_book_title FROM books WHERE book_id = p_book_id;


    SELECT first_name, last_name INTO v_first_name, v_last_name FROM borrowers WHERE borrower_id = p_borrower_id;

 
    INSERT INTO borrowed_books (book_id, borrower_id, book_title, date_borrowed, date_return, date_received, date_updated)
    VALUES (p_book_id, p_borrower_id, v_book_title, p_date_borrowed, p_date_return, p_date_received, NOW());

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Insert_Borrower` (IN `p_borrower_id` INT(11), IN `p_first_name` VARCHAR(35), IN `p_last_name` VARCHAR(35), IN `p_address` VARCHAR(100), IN `p_contact_number` INT(18), IN `p_email` VARCHAR(75))   BEGIN
    
    DECLARE v_id INT;
    DECLARE v_date_created DATETIME;
    DECLARE v_date_updated DATETIME;

    
    SET v_id = NULL;
    SET v_date_created = NOW();
    SET v_date_updated = NOW();

    
    INSERT INTO borrowers (id, borrower_id, first_name, last_name, address, contact_number, email, date_created, date_updated)
    VALUES (v_id, p_borrower_id, p_first_name, p_last_name, p_address, p_contact_number, p_email, v_date_created, v_date_updated);
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LoginUser` (IN `p_username` VARCHAR(255), IN `p_password` VARCHAR(255))   BEGIN
    DECLARE v_id INT;
    DECLARE v_password VARCHAR(255);

    
    SELECT id, password
    INTO v_id, v_password
    FROM admin_user
    WHERE username = p_username;

   
    IF v_id IS NOT NULL THEN
        
        IF BINARY p_password = v_password THEN
            
            SELECT 'Login successful' AS status, v_id AS id;
        ELSE
            
            SELECT 'Invalid username or password' AS status;
        END IF;
    ELSE
       
        SELECT 'Invalid username or password' AS status;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `LoginUserr` (IN `p_username` VARCHAR(255), IN `p_password` VARCHAR(255))   BEGIN
    DECLARE user_id INT;
    DECLARE stored_password VARCHAR(255);
    DECLARE status VARCHAR(50);

    
    SELECT user_id, password INTO user_id, stored_password
    FROM admin_user
    WHERE username = p_username;

    
    IF user_id IS NOT NULL THEN
      
        IF stored_password = SHA1(p_password) THEN
            SET status = 'Login successful';
        ELSE
            SET status = 'Invalid password';
        END IF;
    ELSE
        SET status = 'User not found';
    END IF;

    SELECT user_id, status;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegisterUser` (IN `p_first_name` VARCHAR(255), IN `p_last_name` VARCHAR(255), IN `p_username` VARCHAR(255), IN `p_password` VARCHAR(255))   BEGIN
    DECLARE existing_count INT;

    -- Check kung nag eexist username
    SELECT COUNT(*) INTO existing_count FROM admin_user WHERE username = p_username;

    IF existing_count > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Username already exists';
    ELSE
        -- to insert new one
        INSERT INTO admin_user (first_name, last_name, username, password, date_created, date_updated)
        VALUES (p_first_name, p_last_name, p_username, p_password, NOW(), NOW());
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegisterUserr` (IN `p_first_name` VARCHAR(255), IN `p_last_name` VARCHAR(255), IN `p_username` VARCHAR(255), IN `p_password` VARCHAR(255))   BEGIN
    DECLARE hashed_password VARCHAR(255);

   
    SET hashed_password = SHA1(p_password);

   
    INSERT INTO admin_user (first_name, last_name, username, password)
    VALUES (p_first_name, p_last_name, p_username, hashed_password);

    SELECT 'Registration successful' AS status;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Update_Books` (IN `p_book_id` INT(11), IN `p_new_book_title` VARCHAR(75), IN `p_new_book_author` VARCHAR(35), IN `p_new_book_description` VARCHAR(255), IN `p_new_isbn` VARCHAR(17))   BEGIN
    UPDATE books
    SET
        book_title = p_new_book_title,
        book_author = p_new_book_author,
        book_description = p_new_book_description,
        isbn = p_new_isbn,
        date_updated = NOW()
    WHERE book_id = p_book_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Update_Borrowed_Book` (IN `p_book_id` INT, IN `p_borrower_id` INT, IN `p_date_borrowed` DATE, IN `p_date_return` DATE, IN `p_date_received` DATE)   BEGIN
    UPDATE borrowed_books
    SET
        borrower_id = p_borrower_id,
        date_borrowed = p_date_borrowed,
        date_return = p_date_return,
        date_received = p_date_received
    WHERE
        book_id = p_book_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `Update_Borrower` (IN `p_borrower_id` INT, IN `p_first_name` VARCHAR(255), IN `p_last_name` VARCHAR(255), IN `p_address` VARCHAR(255), IN `p_contact_number` VARCHAR(20), IN `p_email` VARCHAR(255))   BEGIN
    UPDATE borrowers
    SET
        first_name = p_first_name,
        last_name = p_last_name,
        address = p_address,
        contact_number = p_contact_number,
        email = p_email
    WHERE
        borrower_id = p_borrower_id;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE `admin_user` (
  `id` int(11) NOT NULL,
  `first_name` varchar(35) NOT NULL,
  `last_name` varchar(35) NOT NULL,
  `username` varchar(75) NOT NULL,
  `password` varchar(35) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_user`
--

INSERT INTO `admin_user` (`id`, `first_name`, `last_name`, `username`, `password`, `date_created`, `date_updated`) VALUES
(15, 'aw', 'aw', 'ewan@gmail.com', '$2y$10$20GUBJIaCdVnQXy1D99VKONpZlEX', '2023-12-08 11:52:27', '2023-12-08 11:52:27'),
(16, 'najnaj', 'san', 'najnaj@gmail.com', '$2y$10$xi8/X99lswlplffxyw3lMOHYXXjA', '2023-12-09 00:44:48', '2023-12-09 00:44:48'),
(17, 'adsad', '2323', '12345@gmail.com', '$2y$10$pD76lSJIIY2GArOESFDCGuL9dC3C', '2023-12-09 00:45:54', '2023-12-09 00:45:54'),
(18, 'Jan-jan', 'Santiago', 'libraryAdmin@gmail.com', '$2y$10$O6Wcp9a7Bdle30aN/EEPBe9L072Q', '2023-12-10 21:17:13', '2023-12-10 21:17:13'),
(19, 'Jan-jan', 'Santiago', 'janjansantiago@gmail.com', '$2y$10$6gC1SZ//NLPoNoVxBYNeuu4h8zYQ', '2023-12-10 21:21:09', '2023-12-10 21:21:09'),
(20, 'Kristoffer', 'Cabigon', 'cabigon@gmail.com', '$2y$10$WsrPZveMt6QRain5DmjIoO6jNbGb', '2023-12-10 21:25:24', '2023-12-10 21:25:24'),
(21, 'Jan-jan', 'Santiago', 'santiagojanjan@gmail.com', '$2y$10$4QlANEvXW4OQF9BnXRjwW.bH.P5i', '2023-12-10 21:36:53', '2023-12-10 21:36:53'),
(22, 'Mark', 'Tonio', 'tonio@gmail.com', '$2y$10$J7Ex6wTk5ex9bGCJI6Qc1uGUjjQ8', '2023-12-10 21:40:43', '2023-12-10 21:40:43'),
(24, 'Jan-jan', 'Santiago', 'sanjan@gmail.com', '$2y$10$.BEd5f2aVkHLjmllYyjBVuDgUwsB', '2023-12-10 22:33:05', '2023-12-10 22:33:05'),
(29, 'Aeron', 'Aeron', 'Aeron@gmail.com', '$2y$10$sIaB98hNBYl49.9IoRplje/miQaR', '2023-12-10 23:24:49', '2023-12-10 23:24:49'),
(30, 'melvin', 'melvin', 'melvin@gmail.com', '$2y$10$jIDh81QGBUbWydA4y.eYYO0ORJ3w', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, 'Bucio', 'Bucio', 'Bucioo@gmail.com', 'a7ce5b0c7e956b8e3c1a5c254c910d57c56', '2023-12-10 23:36:47', '2023-12-10 23:36:47'),
(33, 'Bucio', 'Bucio', 'melv@gmail.com', '9adcb29710e807607b683f62e555c22dc56', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(34, 'Bucio', 'Bucio', 'melv@gmail.com', '9adcb29710e807607b683f62e555c22dc56', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(35, 'the', 'the', 'the@gmail.com', '9adcb29710e807607b683f62e555c22dc56', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(36, 'Santiago', 'Jan-jan', '12345@gmail.com', '5e9795e3f3ab55e7790a6283507c085db0d', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(37, 'thethe', 'thethe', 'thethe@gmail.com', '8cb2237d0679ca88db6464eac60da963455', '2023-12-10 23:54:23', '2023-12-10 23:54:23'),
(41, 'Jan-jan', 'Santiago', 'jan@gmail.com', '12345', '2023-12-11 00:05:17', '2023-12-11 00:05:17'),
(42, 'The', 'TheWan', 'dawan@gmail.com', 'dawan', '2023-12-11 01:22:11', '2023-12-11 01:22:11'),
(43, 'Jan-jan', 'Santiago', 'thejanjan@gmail.com', '$2y$10$v/5G8lef8m6BFPX4Y4xJuOx9pT27', '2023-12-15 17:50:09', '0000-00-00 00:00:00'),
(44, 'Mark', 'Tonio', 'tonio1234@gmail.com', '$2y$10$d/TMyaDdj/PYle8VFBr.UeNO9RUP', '2023-12-15 17:57:09', '0000-00-00 00:00:00'),
(45, 'Kobe', 'Ray', 'koberay@gmail.com', '$2y$10$L0gLPbuxcrFrkwT..bb26eePx.BW', '2023-12-15 18:04:33', '0000-00-00 00:00:00'),
(46, 'Ekko', 'Ekko', 'ekko@gmail.com', '$2y$10$12Qyym/CXAr1fRXSmQRJ7unnM5j1', '2023-12-15 18:08:06', '0000-00-00 00:00:00'),
(47, 'awit', 'cutie', 'cutie@gmail.com', '$2y$10$tUE/BxlP6wFY9ziuiRPRsObAGsiB', '2023-12-15 18:14:24', '0000-00-00 00:00:00'),
(48, 'aron', 'jay', 'aron@gmail.com', '$2y$10$Ply4W37vHfLKeqqJCj/YiOzgkIwj', '2023-12-15 18:31:54', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `book_title` varchar(100) NOT NULL,
  `book_author` varchar(35) NOT NULL,
  `book_description` varchar(255) NOT NULL,
  `isbn` varchar(17) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `book_id`, `book_title`, `book_author`, `book_description`, `isbn`, `date_created`, `date_updated`) VALUES
(28, 1001, 'Harry Potter', 'J.K. Rowling', 'Harry Potter is a series of novels by British author J. K. Rowling. ', '1111-2222-3333', '2023-12-11 01:55:26', '2023-12-11 01:55:50'),
(29, 1002, 'Lord of the Rings', 'Mark', 'A war', '1112-2221-3331', '2023-12-11 01:56:28', '2023-12-11 01:56:28'),
(30, 1003, 'The Good and Bad', 'Laurence', 'A story of a young man', '1122-2211-3311', '2023-12-11 01:57:09', '2023-12-11 01:57:09');

--
-- Triggers `books`
--
DELIMITER $$
CREATE TRIGGER `books_audit` BEFORE UPDATE ON `books` FOR EACH ROW BEGIN
    
    INSERT INTO books_audit_trail (id, book_id, book_title, book_author, book_description, isbn, date_created, date_updated, action)
    VALUES (OLD.id, OLD.book_id, OLD.book_title, OLD.book_author, OLD.book_description, OLD.isbn, OLD.date_created, OLD.date_updated, 'UPDATE');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `books_audit_trail`
--

CREATE TABLE `books_audit_trail` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `book_title` varchar(75) NOT NULL,
  `book_author` varchar(35) NOT NULL,
  `book_description` varchar(255) NOT NULL,
  `isbn` varchar(17) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `action` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books_audit_trail`
--

INSERT INTO `books_audit_trail` (`id`, `book_id`, `book_title`, `book_author`, `book_description`, `isbn`, `date_created`, `date_updated`, `action`) VALUES
(16, 103, 'aw', 'aw', 'aw', 'aw', '2023-12-08 22:38:37', '2023-12-08 22:38:37', 'UPDATE'),
(16, 103, 'new', 'awnew', 'new', 'new', '2023-12-08 22:38:37', '2023-12-09 01:55:16', 'UPDATE'),
(28, 1001, 'Harry Potter', 'J.K. Rowling', 'Harry Potter is a series of novels by British author J. K. Rowling. The novels follow Harry Potter, an 11-year-old boy who discovers he is the son of famous wizards and will attend Hogwarts School of Witchcraft and Wizardry. Harry learns of an entire soci', '1111-2222-3333', '2023-12-11 01:55:26', '2023-12-11 01:55:26', 'UPDATE');

-- --------------------------------------------------------

--
-- Table structure for table `book_author`
--

CREATE TABLE `book_author` (
  `author_id` int(11) NOT NULL,
  `author_name` varchar(35) NOT NULL,
  `book_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_author`
--

INSERT INTO `book_author` (`author_id`, `author_name`, `book_id`) VALUES
(1, 'J.K. Rowling', 1),
(2, 'Lord of the Rings', 1002),
(3, 'Laurence', 1003);

-- --------------------------------------------------------

--
-- Stand-in structure for view `book_borrowed`
-- (See below for the actual view)
--
CREATE TABLE `book_borrowed` (
`book_id` int(11)
,`borrower_id` int(11)
,`date_borrowed` datetime
,`date_return` datetime
,`date_received` datetime
,`book_title` varchar(100)
,`borrower_name` varchar(71)
);

-- --------------------------------------------------------

--
-- Table structure for table `book_categories`
--

CREATE TABLE `book_categories` (
  `book_id` int(11) NOT NULL,
  `category` varchar(75) NOT NULL,
  `isbn` varchar(17) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borrowed_books`
--

CREATE TABLE `borrowed_books` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `book_title` varchar(75) NOT NULL,
  `date_borrowed` datetime NOT NULL,
  `date_return` datetime NOT NULL,
  `date_received` datetime NOT NULL,
  `date_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowed_books`
--

INSERT INTO `borrowed_books` (`id`, `book_id`, `borrower_id`, `book_title`, `date_borrowed`, `date_return`, `date_received`, `date_updated`) VALUES
(1, 103, 101, 'aw', '2023-12-08 00:00:00', '2023-12-11 00:00:00', '2023-12-16 00:00:00', '2023-12-08 23:43:52'),
(11, 1001, 101, 'Harry Potter', '2023-12-11 00:00:00', '2023-12-12 00:00:00', '0000-00-00 00:00:00', '2023-12-11 01:58:56'),
(12, 1003, 102, 'The Good and Bad', '2023-12-15 00:00:00', '2023-12-16 00:00:00', '0000-00-00 00:00:00', '2023-12-15 16:22:05'),
(14, 1002, 103, 'Lord of the Rings', '2023-12-15 00:00:00', '2023-12-16 00:00:00', '0000-00-00 00:00:00', '2023-12-15 16:22:47');

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `id` int(11) NOT NULL,
  `borrower_id` int(11) NOT NULL,
  `first_name` varchar(35) NOT NULL,
  `last_name` varchar(35) NOT NULL,
  `address` varchar(100) NOT NULL,
  `contact_number` int(17) NOT NULL,
  `email` varchar(75) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `last_activity` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`id`, `borrower_id`, `first_name`, `last_name`, `address`, `contact_number`, `email`, `date_created`, `date_updated`, `last_activity`) VALUES
(24, 101, 'Jan-jan', 'Santiago', 'Bagong Silang', 2147483647, 'jan@gmail.com', '2023-12-09 02:36:26', '2023-12-09 02:36:26', NULL),
(25, 102, 'Kristoffer', 'Cabigon', 'Saranay', 1231231, 'Kristoffer@gmail.com', '2023-12-09 02:41:03', '2023-12-09 02:41:03', NULL),
(26, 103, 'Mark', 'Tonio', 'Malaria', 12312, 'mark@gmail.com', '2023-12-09 02:54:51', '2023-12-09 02:54:51', NULL),
(30, 104, 'Shaine', 'Palomares', 'Kiko', 912341231, 'shaine@gmail.com', '2023-12-15 16:20:24', '2023-12-15 16:20:24', NULL),
(31, 105, 'Aeron', 'Guillermo', 'Amparo', 91235363, 'aeron@gmail.com', '2023-12-15 16:20:55', '2023-12-15 16:20:55', NULL),
(32, 106, 'Melvin', 'Custodio', 'Bagong Silang', 988952113, 'melvin@gmail.com', '2023-12-15 16:21:28', '2023-12-15 16:21:28', NULL);

-- --------------------------------------------------------

--
-- Structure for view `book_borrowed`
--
DROP TABLE IF EXISTS `book_borrowed`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `book_borrowed`  AS SELECT `borrowed_books`.`book_id` AS `book_id`, `borrowed_books`.`borrower_id` AS `borrower_id`, `borrowed_books`.`date_borrowed` AS `date_borrowed`, `borrowed_books`.`date_return` AS `date_return`, `borrowed_books`.`date_received` AS `date_received`, `books`.`book_title` AS `book_title`, concat(`borrowers`.`first_name`,' ',`borrowers`.`last_name`) AS `borrower_name` FROM ((`borrowed_books` join `books` on(`borrowed_books`.`book_id` = `books`.`book_id`)) join `borrowers` on(`borrowed_books`.`borrower_id` = `borrowers`.`borrower_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_book_id` (`book_id`),
  ADD KEY `book_title` (`book_title`);

--
-- Indexes for table `book_author`
--
ALTER TABLE `book_author`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `borrowed_books`
--
ALTER TABLE `borrowed_books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_user`
--
ALTER TABLE `admin_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `book_author`
--
ALTER TABLE `book_author`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `borrowed_books`
--
ALTER TABLE `borrowed_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `delete_inactive_borrowers` ON SCHEDULE EVERY 1 MINUTE STARTS '2023-12-09 02:02:02' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    DELETE FROM borrowers WHERE date_updated < NOW() - INTERVAL 5 MINUTE;
END


-- ON SCHEDULE EVERY 1 DAY
-- DO
-- BEGIN
    -- DELETE FROM borrowers WHERE last_activity < NOW() - INTERVAL 30 DAYS$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
