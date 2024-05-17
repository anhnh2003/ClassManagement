-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2024 at 03:50 PM
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
-- Database: `studentmsdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `AdminName` varchar(120) DEFAULT NULL,
  `UserName` varchar(120) DEFAULT NULL,
  `ContactNumber` bigint(10) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp(),
  `role_id` int(11) NOT NULL DEFAULT 1,
  `ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`AdminName`, `UserName`, `ContactNumber`, `Email`, `Password`, `CreationTime`, `role_id`, `ID`) VALUES
('Admin', 'admin', 8979555558, 'minihoanganh@gmail.com', '$2y$10$4RX8MWSz8CIpzGlnzkImyO5lMdQXsAc4v4ltMvcPXJCjWGahynox6', '2019-10-11 04:36:52', 1, 10000);

-- --------------------------------------------------------

--
-- Table structure for table `tblclass`
--

CREATE TABLE `tblclass` (
  `ID` int(5) NOT NULL,
  `ClassName` varchar(50) DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp(),
  `Room` varchar(20) DEFAULT NULL,
  `teacher_id` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclass`
--

INSERT INTO `tblclass` (`ID`, `ClassName`, `CreationTime`, `Room`, `teacher_id`) VALUES
(1, 'Soft Skills', '2022-01-13 10:42:14', 'D8-108', NULL),
(2, 'Introduction to Programming', '2022-01-13 10:42:35', 'B1-403', NULL),
(3, 'Cryptography', '2022-01-13 10:42:41', 'B1-203', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblnotice`
--

CREATE TABLE `tblnotice` (
  `ID` int(5) NOT NULL,
  `NoticeTitle` mediumtext DEFAULT NULL,
  `ClassId` int(10) DEFAULT NULL,
  `NoticeMsg` mediumtext DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblnotice`
--

INSERT INTO `tblnotice` (`ID`, `NoticeTitle`, `ClassId`, `NoticeMsg`, `CreationTime`) VALUES
(2, 'Marks of Unit Test.', 3, 'Meet your class teacher for seeing copies of unit test', '2022-01-19 06:35:58'),
(3, 'Marks of Unit Test.', 2, 'Meet your class teacher for seeing copies of unit test', '2022-01-19 06:35:58'),
(4, 'Test', 3, 'This is for testing.', '2022-02-02 18:17:03'),
(5, 'Test Notice', 3, 'This is for Testing.', '2022-02-02 19:03:43');

-- --------------------------------------------------------

--
-- Table structure for table `tblpage`
--

CREATE TABLE `tblpage` (
  `ID` int(10) NOT NULL,
  `PageType` varchar(200) DEFAULT NULL,
  `PageTitle` mediumtext DEFAULT NULL,
  `PageDescription` mediumtext DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `ContactNumber` bigint(10) DEFAULT NULL,
  `UpdationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpage`
--

INSERT INTO `tblpage` (`ID`, `PageType`, `PageTitle`, `PageDescription`, `Email`, `ContactNumber`, `UpdationDate`) VALUES
(1, 'aboutus', 'About Us', '<div style=\"text-align: start;\"><font color=\"#7b8898\" face=\"Mercury SSm A, Mercury SSm B, Georgia, Times, Times New Roman, Microsoft YaHei New, Microsoft Yahei, ????, ??, SimSun, STXihei, ????, serif\"><span style=\"font-size: 26px;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</span></font><br></div>', NULL, NULL, NULL),
(2, 'contactus', 'Contact Us', '890,Sector 62, Gyan Sarovar, GAIL Noida(Delhi/NCR)', 'infodata@gmail.com', 7896541236, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblpublicnotice`
--

CREATE TABLE `tblpublicnotice` (
  `ID` int(5) NOT NULL,
  `NoticeTitle` varchar(200) DEFAULT NULL,
  `NoticeMessage` mediumtext DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpublicnotice`
--

INSERT INTO `tblpublicnotice` (`ID`, `NoticeTitle`, `NoticeMessage`, `CreationTime`) VALUES
(1, 'School will re-open', 'Consult your class teacher.', '2022-01-20 09:11:57'),
(2, 'Test Public Notice', 'This is for Testing\r\n', '2022-02-02 19:04:10');

-- --------------------------------------------------------

--
-- Table structure for table `tblresult`
--

CREATE TABLE `tblstudent_test` (
  `student_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `TotalPoint` int(11) NOT NULL,
  `StartTime` datetime DEFAULT NULL,
  `SubmitTime` datetime DEFAULT NULL,
  `IP` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblroles`
--

CREATE TABLE `tblroles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblroles`
--

INSERT INTO `tblroles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Teacher'),
(3, 'Student');

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent`
--

CREATE TABLE `tblstudent` (
  `StudentName` varchar(30) DEFAULT NULL,
  `Email` varchar(30) DEFAULT NULL,
  `Gender` varchar(7) DEFAULT NULL,
  `ContactNumber` bigint(10) DEFAULT NULL,
  `UserName` varchar(20) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp(),
  `role_id` int(11) NOT NULL DEFAULT 3,
  `ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblstudent`
--

INSERT INTO `tblstudent` (`StudentName`, `Email`, `Gender`, `ContactNumber`, `UserName`, `Password`, `CreationTime`, `role_id`, `ID`) VALUES
('Nguyen A', 'nguyena@gmail.com', 'Male', 0343868519, 'nguyena', '202cb962ac59075b964b07152d234b70', '2022-01-13 14:09:04', 3, 30000),
('Tran B', 'tranb@gmail.com', 'Male', NULL, 'tranb', '202cb962ac59075b964b07152d234b70', '2022-01-16 06:23:33', 3, 30001),
('Nguyen C', 'nguyenc@gmali.com', 'Female', NULL, 'nguyenc', '202cb962ac59075b964b07152d234b70', '2022-01-19 15:24:52', 3, 30002),
('Dinh D', 'dinhd@gmail.com', 'Female', NULL, 'dinhd', 'f925916e2754e5e03f75dd58a5733251', '2022-02-02 18:19:45', 3, 30003),
('Pham E', 'phame@gmail.com', 'Male', NULL, 'phame', 'f925916e2754e5e03f75dd58a5733251', '2022-02-02 18:57:22', 3, 30004),
('anh', 'hi@gmail.com', 'Male', 0, 'anh', '$2y$10$raQ/eDk1.lW//hBaBmNILu2.05rw/LUhFarntAS4lkYCpk7rovWE2', '2024-05-15 04:14:35', 3, 30005);
-- --------------------------------------------------------

--
-- Table structure for table `tblteacher`
--

CREATE TABLE `tblteacher` (
  `TeacherName` varchar(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `Email` varchar(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `Gender` varchar(7) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `Username` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `Password` varchar(30) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `ContactNumber` bigint(10) DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp(),
  `role_id` int(11) NOT NULL DEFAULT 2,
  `ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblteacher`
--

INSERT INTO `tblteacher` (`TeacherName`, `Email`, `Gender`, `Username`, `Password`, `ContactNumber`, `CreationTime`, `role_id`, `ID`) VALUES
('Anh', 'hi@gmail.com', 'Male', 'anh', '$2y$10$/wv.E6hhNJQ/JeOlFBDeeei', NULL, '2024-05-15 03:18:58', 2, 20000),
('huy', 'a@gmail.com', 'Female', 'huy', '$2y$10$6O10POMCSX/GUlNULt6WO.k', NULL, '2024-05-16 02:35:52', 2, 20001);

-- --------------------------------------------------------

--
-- Table structure for table `tbltest`
--

CREATE TABLE `tbltest` (
  `ID` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `TestName` varchar(30) DEFAULT NULL,
  `CreateTime` datetime DEFAULT curtime(),
  `StartTime` datetime DEFAULT NULL,
  `EndTime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbltoken`
--

CREATE TABLE `tbltoken` (
  `UserToken` varchar(255) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TokenExpire` datetime NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblclass`
--
ALTER TABLE `tblclass`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblnotice`
--
ALTER TABLE `tblnotice`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblpage`
--
ALTER TABLE `tblpage`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblpublicnotice`
--
ALTER TABLE `tblpublicnotice`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblroles`
--
ALTER TABLE `tblroles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `tblstudent`
--
ALTER TABLE `tblstudent`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblteacher`
--
ALTER TABLE `tblteacher`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbltoken`
--
ALTER TABLE `tbltoken`
  ADD PRIMARY KEY (`UserToken`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10001;

--
-- AUTO_INCREMENT for table `tblclass`
--
ALTER TABLE `tblclass`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tblnotice`
--
ALTER TABLE `tblnotice`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblpage`
--
ALTER TABLE `tblpage`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblpublicnotice`
--
ALTER TABLE `tblpublicnotice`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblroles`
--
ALTER TABLE `tblroles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblstudent`
--
ALTER TABLE `tblstudent`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30006;

--
-- AUTO_INCREMENT for table `tblteacher`
--
ALTER TABLE `tblteacher`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20002;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
