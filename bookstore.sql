-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 05, 2024 lúc 01:37 AM
-- Phiên bản máy phục vụ: 10.4.25-MariaDB
-- Phiên bản PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `bookstore`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitiethoadon`
--

CREATE TABLE `chitiethoadon` (
  `ma_hoa_don` int(11) NOT NULL,
  `ma_sach` char(10) NOT NULL,
  `so_luong_sp` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `chitiethoadon`
--

INSERT INTO `chitiethoadon` (`ma_hoa_don`, `ma_sach`, `so_luong_sp`) VALUES
(8, 'S14', 2),
(10, 'S14', 1),
(11, 'S01', 2),
(12, 'S01', 3),
(13, '01', 1),
(13, '02', 1),
(14, '02', 1),
(14, '05', 1),
(15, '07', 1),
(16, '03', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `giohang`
--

CREATE TABLE `giohang` (
  `id` int(10) NOT NULL,
  `ma_sach` char(10) NOT NULL,
  `so_luong_sach` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoadon`
--

CREATE TABLE `hoadon` (
  `ma_hoa_don` int(11) NOT NULL,
  `id` int(10) NOT NULL,
  `ten_khach_hang` varchar(128) NOT NULL,
  `ngay_lap` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `trang_thai` varchar(128) NOT NULL,
  `trang_thai_thanh_toan` varchar(128) NOT NULL,
  `tong_tien` int(11) NOT NULL,
  `sdt` char(10) NOT NULL,
  `dia_chi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `hoadon`
--

INSERT INTO `hoadon` (`ma_hoa_don`, `id`, `ten_khach_hang`, `ngay_lap`, `trang_thai`, `trang_thai_thanh_toan`, `tong_tien`, `sdt`, `dia_chi`) VALUES
(8, 2, 'Admin', '2024-12-01 05:01:36', 'Canceled', 'Chưa thanh toán', 70400, '000000000', 'Hà Nội'),
(9, 2, 'Admin', '2024-12-01 05:01:34', 'Canceled', 'Chưa thanh toán', 70400, '000000000', 'Hà Nội'),
(10, 4, 'ngoanh', '2024-12-01 05:03:52', 'recieved', 'Đã thanh toán', 35200, '0324124122', 'hà nội'),
(11, 2, 'Admin', '2024-12-01 08:19:12', 'recieved', 'Đã thanh toán', 126000, '0000000', 'hồ chí minh'),
(12, 2, 'Admin', '2024-12-01 08:20:49', 'recieved', 'Đã thanh toán', 209997, '000000000', 'Hà Nội'),
(13, 2, 'Admin', '2024-12-04 11:41:51', 'sending', 'Chưa thanh toán', 71200, '000000000', 'Hà Nội'),
(14, 4, 'ngoanh', '2024-12-04 11:50:03', 'sending', 'Chưa thanh toán', 90000, '0324124122', 'hà nội'),
(15, 4, 'ngoanh', '2024-12-04 11:49:42', 'processing', 'Chưa thanh toán', 60200, '0324124122', 'hà nội'),
(16, 2, 'Admin', '2024-12-04 22:33:53', 'Canceled', 'Chưa thanh toán', 50000, '000000000', 'Hà Nội');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaisach`
--

CREATE TABLE `loaisach` (
  `ma_loai_sach` char(10) NOT NULL,
  `ten_loai_sach` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `loaisach`
--

INSERT INTO `loaisach` (`ma_loai_sach`, `ten_loai_sach`, `created_at`, `updated_at`) VALUES
('B1', 'Truyện Tranh', '2024-12-03 17:15:32', '2024-12-03 17:15:32'),
('B2', 'Sách Giáo Khoa', '2024-12-02 17:25:38', '2024-12-02 17:25:38'),
('B3', 'Tiểu Thuyết', '2024-12-02 17:26:24', '2024-12-02 17:26:24'),
('B4', 'Kỹ Năng Sống', '2024-12-02 17:26:58', '2024-12-02 17:26:58'),
('B5', 'Sách Quốc Tế', '2024-12-02 17:27:12', '2024-12-02 17:27:12');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhaxuatban`
--

CREATE TABLE `nhaxuatban` (
  `ma_nxb` varchar(10) NOT NULL,
  `ten_nxb` varchar(255) NOT NULL,
  `sdt_nxb` char(10) NOT NULL,
  `dia_chi_nxb` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `nhaxuatban`
--

INSERT INTO `nhaxuatban` (`ma_nxb`, `ten_nxb`, `sdt_nxb`, `dia_chi_nxb`, `created_at`, `updated_at`) VALUES
('N1', 'Nhà xuất bản Kim Đồng', '0240120424', 'Hà Nội', '2024-12-04 09:25:26', '2024-12-04 09:25:26'),
('N10', 'Nhà xuất bản Tổng Hợp TP Hồ Chí Minh', '0153513523', 'Hà Nội', '2024-12-04 09:43:53', '2024-12-04 09:43:53'),
('N11', 'Nhà xuất bản Thanh Niên', '0240123214', 'Hà Nội', '2024-12-04 11:05:20', '2024-12-04 11:05:20'),
('N2', 'NXB Dân Trí', '0535235314', 'Hà Nội', '2024-12-04 09:29:38', '2024-12-04 09:29:38'),
('N3', 'Thanh Niên', '0125034144', 'Hà Nội', '2024-12-04 09:31:37', '2024-12-04 09:31:37'),
('N4', 'Văn Học', '0743523412', 'Hà Nội', '2024-12-04 09:32:54', '2024-12-04 09:32:54'),
('N5', 'Chuokoron-Shinsha', '0351351344', 'Hà Nội', '2024-12-04 09:33:48', '2024-12-04 09:33:48'),
('N6', 'Hồng Đức', '0254352324', 'Hà Nội', '2024-12-04 09:35:49', '2024-12-04 09:35:49'),
('N7', 'Nhà xuất bản Đại học Quốc gia TP Hồ Chí Minh', '0512421414', 'Hà Nội', '2024-12-04 09:39:02', '2024-12-04 09:39:02'),
('N8', 'Nhà xuất bản Đại học Sư Phạm TP Hồ Chí Minh', '0351421455', 'Hà Nội', '2024-12-04 09:38:48', '2024-12-04 09:38:48'),
('N9', 'NXB Thế Giới', '0464252452', 'Hà Nội', '2024-12-04 09:40:41', '2024-12-04 09:40:41');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sach`
--

CREATE TABLE `sach` (
  `ma_sach` char(10) NOT NULL,
  `ten_sach` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gia_sach` int(12) NOT NULL,
  `khuyen_mai` int(12) NOT NULL,
  `gia_khuyen_mai` int(12) NOT NULL,
  `mo_ta` text NOT NULL,
  `so_luong` int(12) NOT NULL,
  `sold` int(11) NOT NULL,
  `hinh_anh` varchar(128) NOT NULL,
  `anh_1` varchar(255) DEFAULT NULL,
  `anh_2` varchar(255) DEFAULT NULL,
  `ma_loai_sach` char(10) NOT NULL,
  `ma_tac_gia` varchar(10) NOT NULL,
  `ma_nxb` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `sach`
--

INSERT INTO `sach` (`ma_sach`, `ten_sach`, `gia_sach`, `khuyen_mai`, `gia_khuyen_mai`, `mo_ta`, `so_luong`, `sold`, `hinh_anh`, `anh_1`, `anh_2`, `ma_loai_sach`, `ma_tac_gia`, `ma_nxb`, `created_at`, `updated_at`) VALUES
('01', 'conan', 40000, 12, 35200, 'conan', 39, 1, 'conan47.jpg', 'TRUYEN-TRANH-CONAN-TOAN-TAP-3-NIHONBOOK.jpg', '', 'B1', 'T1', 'N1', '2024-12-04 11:38:55', '2024-12-04 11:38:55'),
('02', 'doremon', 40000, 10, 36000, 'doraemon', 38, 2, 'img-03.jpg', 'doraemon2.jpg', 'doraemon3.jpg', 'B1', 'T2', 'N1', '2024-12-04 11:48:33', '2024-12-04 11:48:33'),
('03', 'Bên Nhau Trọn Đời', 50000, 0, 50000, 'bên nhau chọn đời', 39, 1, 'img-01.jpg', 'bntd.jpg', '', 'B3', 'T1', 'N4', '2024-12-04 22:32:59', '2024-12-04 22:32:59'),
('04', 'tập tô số', 40000, 0, 40000, 'tập tô số', 40, 0, 'img-08.jpg', 'detail1.png', 'detail2.png', 'B2', 'T1', 'N7', '2024-12-04 10:41:49', '2024-12-04 10:41:49'),
('05', 'Thỏ Bảy Màu', 60000, 10, 54000, 'Thỏ Bảy Màu', 59, 1, 'img-13.jpg', 'detail5.jpg', 'detail6.jpg', 'B1', 'T11', 'N2', '2024-12-04 11:48:33', '2024-12-04 11:48:33'),
('06', 'Hạnh Phúc Đích Thực', 50000, 0, 50000, 'Hạnh Phúc Đích Thực\r\n', 50, 0, 'thien.jpg', 'thien_1.jpg', 'thien_2.jpg', 'B4', 'T14', 'N2', '2024-12-04 11:28:38', '2024-12-04 11:28:38'),
('07', 'Xuất Phát Điểm', 70000, 14, 60200, 'Xuất Phát Điểm', 49, 1, 'img-11.jpg', 'sgk12_1.jpg', 'sgk12_2.jpg', 'B5', 'T1', 'N9', '2024-12-04 11:49:42', '2024-12-04 11:49:42');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tacgia`
--

CREATE TABLE `tacgia` (
  `ma_tac_gia` varchar(10) NOT NULL,
  `ten_tac_gia` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `tacgia`
--

INSERT INTO `tacgia` (`ma_tac_gia`, `ten_tac_gia`, `created_at`, `updated_at`) VALUES
('T1', 'Aoyama Gōshō ', '2024-12-04 07:10:30', '2024-12-04 07:10:30'),
('T10', 'Dale Carnegie', '2024-12-04 09:41:49', '2024-12-04 09:41:49'),
('T11', 'Huỳnh Thái Ngọc', '2024-12-04 11:23:31', '2024-12-04 11:23:31'),
('T12', 'José Mauro de Vasconcelos', '2024-12-04 11:25:13', '2024-12-04 11:25:13'),
('T13', 'Quách Tư Đặc', '2024-12-04 11:25:38', '2024-12-04 11:25:38'),
('T14', 'Martin Elias Peter Seligman', '2024-12-04 11:27:04', '2024-12-04 11:27:04'),
('T2', 'Fujiko F. Fujio', '2024-12-04 08:22:14', '2024-12-04 08:22:14'),
('T3', 'Đỗ Bảo Châu', '2024-12-04 09:29:13', '2024-12-04 09:29:13'),
('T4', 'Lê Văn Tuấn, Nguyễn Thế Duy', '2024-12-04 09:35:07', '2024-12-04 09:35:07'),
('T5', 'Cố Mạn', '2024-12-04 09:32:24', '2024-12-04 09:32:24'),
('T6', 'Thiên Lộc, Minh Nguyệt', '2024-12-04 09:36:33', '2024-12-04 09:36:33'),
('T7', 'Nguyễn Thị Ngọc Quyến', '2024-12-04 09:38:16', '2024-12-04 09:38:16'),
('T8', 'Nguyễn Văn Sơn', '2024-12-04 09:39:36', '2024-12-04 09:39:36'),
('T9', 'Mèo Maverick', '2024-12-04 09:40:24', '2024-12-04 09:40:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` char(10) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(2, 'Admin', 'admin@gmail.com', '$2y$10$WyMtvmxxvhAu8.XoXYEOAeMMqMYGOxiAWkrkjQyC2Rz/5dbeQcwJG', '000000000', 'Hà Nội', '2024-11-25 17:05:58', '2024-11-25 17:05:58'),
(4, 'ngoanh', 'ngo@gmail.com', '$2y$10$O8yKdJjYO1uUhU2NwFioRuzEkBGSpT2mtgz90WRGXizWogUKxWVqa', '0324124122', 'hà nội', '2024-11-25 10:03:54', '2024-11-25 10:03:54');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD PRIMARY KEY (`ma_hoa_don`,`ma_sach`);

--
-- Chỉ mục cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD PRIMARY KEY (`ma_sach`,`id`),
  ADD KEY `giohang_ibfk_1` (`id`);

--
-- Chỉ mục cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`ma_hoa_don`),
  ADD KEY `hoadon_ibfk_1` (`id`);

--
-- Chỉ mục cho bảng `loaisach`
--
ALTER TABLE `loaisach`
  ADD PRIMARY KEY (`ma_loai_sach`);

--
-- Chỉ mục cho bảng `nhaxuatban`
--
ALTER TABLE `nhaxuatban`
  ADD PRIMARY KEY (`ma_nxb`);

--
-- Chỉ mục cho bảng `sach`
--
ALTER TABLE `sach`
  ADD PRIMARY KEY (`ma_sach`),
  ADD KEY `sach_ibfk_1` (`ma_loai_sach`),
  ADD KEY `sach_ibfk_3` (`ma_nxb`),
  ADD KEY `sanpham_ibfk_2` (`ma_tac_gia`);

--
-- Chỉ mục cho bảng `tacgia`
--
ALTER TABLE `tacgia`
  ADD PRIMARY KEY (`ma_tac_gia`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  MODIFY `ma_hoa_don` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `giohang_ibfk_2` FOREIGN KEY (`ma_sach`) REFERENCES `sach` (`ma_sach`);

--
-- Các ràng buộc cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `sach`
--
ALTER TABLE `sach`
  ADD CONSTRAINT `sach_ibfk_1` FOREIGN KEY (`ma_loai_sach`) REFERENCES `loaisach` (`ma_loai_sach`),
  ADD CONSTRAINT `sach_ibfk_2` FOREIGN KEY (`ma_tac_gia`) REFERENCES `tacgia` (`ma_tac_gia`),
  ADD CONSTRAINT `sach_ibfk_3` FOREIGN KEY (`ma_nxb`) REFERENCES `nhaxuatban` (`ma_nxb`),
  ADD CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`ma_tac_gia`) REFERENCES `tacgia` (`ma_tac_gia`),
  ADD CONSTRAINT `sanpham_ibfk_3` FOREIGN KEY (`ma_nxb`) REFERENCES `nhaxuatban` (`ma_nxb`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
