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
  `AdminName` varchar(50) DEFAULT NULL,
  `UserName` varchar(50) DEFAULT NULL,
  `ContactNumber` bigint(15) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Password` varchar(50) DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp(),
  `role_id` int(11) NOT NULL DEFAULT 1,
  `ID` int(11) NOT NULL PRIMARY KEY
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`AdminName`, `UserName`, `ContactNumber`, `Email`, `Password`, `CreationTime`, `role_id`, `ID`) VALUES
('Admin', 'admin', 8979555558, 'minihoanganh@gmail.com', '$2y$10$4RX8MWSz8CIpzGlnzkImyO5lMdQXsAc4v4ltMvcPXJCjWGahynox6', '2019-10-11 04:36:52', 1, 10000);

-- --------------------------------------------------------

--
-- Table structure for table `tblnotice`
--

CREATE TABLE `tblnotice` (
  `ID` int(5) NOT NULL PRIMARY KEY,
  `NoticeTitle` mediumtext DEFAULT NULL,
  `ClassId` int(10) DEFAULT NULL,
  `NoticeMsg` mediumtext DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblnotice`
--

INSERT INTO `tblnotice` (`ID`, `NoticeTitle`, `ClassId`, `NoticeMsg`, `CreationTime`) VALUES
(1, 'Marks of Unit Test.', 40000, 'Meet your class teacher for seeing copies of unit test', '2022-01-19 06:35:58'),
(2, 'Test', 40001, 'This is for testing.', '2022-02-02 18:17:03');

-- --------------------------------------------------------

--
-- Table structure for table `tblpage`
--

CREATE TABLE `tblpage` (
  `ID` int(10) NOT NULL PRIMARY KEY,
  `PageType` varchar(100) DEFAULT NULL,
  `PageTitle` mediumtext DEFAULT NULL,
  `PageDescription` mediumtext DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `ContactNumber` bigint(10) DEFAULT NULL,
  `UpdationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpage`
--

INSERT INTO `tblpage` (`ID`, `PageType`, `PageTitle`, `PageDescription`, `Email`, `ContactNumber`, `UpdationDate`) VALUES
(1, 'aboutus', 'About Us', '<div style="text-align: start;"><font color="#7b8898" face="Mercury SSm A, Mercury SSm B, Georgia, Times, Times New Roman, Microsoft YaHei New, Microsoft Yahei, ????, ??, SimSun, STXihei, ????, serif" size="6"><b style="">Project 13 - Secure Web Programming Course</b></font></div><div style="text-align: start;"><font color="#7b8898" face="Mercury SSm A, Mercury SSm B, Georgia, Times, Times New Roman, Microsoft YaHei New, Microsoft Yahei, ????, ??, SimSun, STXihei, ????, serif" size="5">Members:</font></div><div style="text-align: start;"><ul><li><font color="#7b8898" face="Mercury SSm A, Mercury SSm B, Georgia, Times, Times New Roman, Microsoft YaHei New, Microsoft Yahei, ????, ??, SimSun, STXihei, ????, serif" size="5">Nguyen Quoc Huy</font></li><li><font color="#7b8898" face="Mercury SSm A, Mercury SSm B, Georgia, Times, Times New Roman, Microsoft YaHei New, Microsoft Yahei, ????, ??, SimSun, STXihei, ????, serif" size="5">Duong Hong Nam</font></li><li><font color="#7b8898" face="Mercury SSm A, Mercury SSm B, Georgia, Times, Times New Roman, Microsoft YaHei New, Microsoft Yahei, ????, ??, SimSun, STXihei, ????, serif" size="5">Nguyen Hoang Anh</font></li><li><font color="#7b8898" face="Mercury SSm A, Mercury SSm B, Georgia, Times, Times New Roman, Microsoft YaHei New, Microsoft Yahei, ????, ??, SimSun, STXihei, ????, serif" size="5">Le Duc Dung</font></li></ul></div>', NULL, NULL, NULL),
(2, 'contactus', 'Contact Us', '890,Sector 62, Gyan Sarovar, GAIL Noida(Delhi/NCR)', 'infodata@gmail.com', 7896541236, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblpublicnotice`
--

CREATE TABLE `tblpublicnotice` (
  `ID` int(5) NOT NULL PRIMARY KEY,
  `NoticeTitle` varchar(100) DEFAULT NULL,
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
-- Table structure for table `tblroles`
--

CREATE TABLE `tblroles` (
  `role_id` int(11) NOT NULL PRIMARY KEY,
  `role_name` varchar(50) NOT NULL
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
  `ID` int(10) NOT NULL PRIMARY KEY,
  `StudentName` varchar(50) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Gender` varchar(7) DEFAULT NULL,
  `ContactNumber` bigint(15) DEFAULT NULL,
  `UserName` varchar(50) DEFAULT NULL,
  `Password` varchar(50) DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp(),
  `role_id` int(11) NOT NULL DEFAULT 3,
  `StuID` varchar(10) DEFAULT NULL,
  `SaltPassword` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblstudent`
--

INSERT INTO `tblstudent` (`ID`, `StudentName`, `Email`, `Gender`, `ContactNumber`, `UserName`, `Password`, `CreationTime`, `role_id`, `StuID`, `SaltPassword`) VALUES
(30000, 'Nguyen A', 'nguyena@gmail.com', 'Male', NULL, 'nguyena', '$2y$10$4RX8MWSz8CIpzGlnzkImyO5lMdQXsAc4v4ltMvcPXJCjWGahynox6', '2022-01-13 14:09:04', 3, '20210001', NULL),
(30001, 'Tran B', 'tranb@gmail.com', 'Male', NULL, 'tranb', '$2y$10$4RX8MWSz8CIpzGlnzkImyO5lMdQXsAc4v4ltMvcPXJCjWGahynox6', '2022-01-16 06:23:33', 3, '20220001', NULL),
(30002, 'Nguyen C', 'nguyenc@gmali.com', 'Female', NULL, 'nguyenc', '$2y$10$4RX8MWSz8CIpzGlnzkImyO5lMdQXsAc4v4ltMvcPXJCjWGahynox6', '2022-01-19 15:24:52', 3, '20230001', NULL),
(30003, 'Nguyen Quoc Huy', 'nguyenquochuy712@gmail.com', 'Male', 343868519, 'nguyenhuy', '$2y$10$4RX8MWSz8CIpzGlnzkImyO5lMdQXsAc4v4ltMvcPXJCjWGahynox6', '2024-05-15 04:14:35', 3, '20210427', NULL);
-- --------------------------------------------------------

--
-- Table structure for table `tblteacher`
--

CREATE TABLE `tblteacher` (
  `ID` int(11) NOT NULL PRIMARY KEY,
  `TeacherName` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `Email` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `Gender` varchar(7) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `Username` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `Password` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `ContactNumber` bigint(15) DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp(),
  `role_id` int(11) NOT NULL DEFAULT 2,
  `TeaID` varchar(10) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblteacher`
--

INSERT INTO `tblteacher` (`ID`, `TeacherName`, `Email`, `Gender`, `Username`, `Password`, `ContactNumber`, `CreationTime`, `role_id`, `TeaID`) VALUES
(20000, 'Nguyen Anh', 'a@gmail.com', 'Male', 'anh', '$2y$10$4RX8MWSz8CIpzGlnzkImyO5lMdQXsAc4v4ltMvcPXJCjWGahynox6', NULL, '2024-05-15 03:18:58', 2, '20000'),
(20001, 'Nguyen Be', 'b@gmail.com', 'Male', 'be', '$2y$10$4RX8MWSz8CIpzGlnzkImyO5lMdQXsAc4v4ltMvcPXJCjWGahynox6', NULL, '2024-05-16 02:35:52', 2, '20001');

-- --------------------------------------------------------

--
-- Table structure for table `tblclass`
--

CREATE TABLE `tblclass` (
  `ID` int(11) NOT NULL PRIMARY KEY,
  `ClassName` varchar(100) DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp(),
  `Room` varchar(20) DEFAULT NULL,
  `JoinCode` varchar(10) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  FOREIGN KEY (`teacher_id`) REFERENCES tblteacher(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblclass`
--

INSERT INTO `tblclass` (`ID`, `ClassName`, `CreationTime`, `Room`, `JoinCode`, `teacher_id`) VALUES
(40000, 'Soft Skills', '2022-01-13 10:42:14', 'D8-108', '72nci1', 20000),
(40001, 'Introduction to Programming', '2022-01-13 10:42:35', 'B1-403', '9dn2dc', 20000),
(40002, 'Cryptography', '2022-01-13 10:42:41', 'B1-203', 'abc81h', 20001);

-- --------------------------------------------------------

--
-- Table structure for table `tbltest`
--

CREATE TABLE `tbltest` (
  `ID` int(11) NOT NULL PRIMARY KEY,
  `class_id` int(11) DEFAULT NULL,
  `TestName` varchar(100) DEFAULT NULL,
  `CreationTime` timestamp NULL DEFAULT current_timestamp(),
  `StartTime` timestamp NULL DEFAULT NULL,
  `EndTime` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`class_id`) REFERENCES tblclass(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbltoken`
--

CREATE TABLE `tbltoken` (
  `UserToken` varchar(255) NOT NULL PRIMARY KEY,
  `UserID` int(11) NOT NULL,
  `TokenExpire` datetime NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent_test`
--

CREATE TABLE `tblstudent_test` (
  `student_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `TotalPoint` int(11) DEFAULT NULL,
  `StartTime` timestamp NULL DEFAULT NULL,
  `SubmitTime` timestamp NULL DEFAULT NULL,
  `IP` varchar(20) DEFAULT NULL,
  FOREIGN KEY (`student_id`) REFERENCES tblstudent(`ID`),
  FOREIGN KEY (`test_id`) REFERENCES tbltest(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblstudent_class`
--

CREATE TABLE `tblstudent_class` (
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  FOREIGN KEY (`student_id`) REFERENCES tblstudent(`ID`),
  FOREIGN KEY (`class_id`) REFERENCES tblclass(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Indexes for table `tbltest`
--
ALTER TABLE `tbltest`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60000;

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40003;

--
-- AUTO_INCREMENT for table `tblnotice`
--
ALTER TABLE `tblnotice`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30004;

--
-- AUTO_INCREMENT for table `tblteacher`
--
ALTER TABLE `tblteacher`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20002;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
