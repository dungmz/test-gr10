CREATE DATABASE IF NOT EXISTS KTPM CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE KTPM;

DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS giangvien;
DROP TABLE IF EXISTS sinhvien;

CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hoTen VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  ngayTao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE giangvien (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tenGV VARCHAR(100) NOT NULL,
  chucVu VARCHAR(100),
  email VARCHAR(100) NOT NULL UNIQUE,
  soDienThoai VARCHAR(20),
  maTruong VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  ngayTao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sinhvien (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hoTen VARCHAR(100) NOT NULL,
  maSV VARCHAR(20) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  lop VARCHAR(50),
  nganh VARCHAR(100),
  ngayTao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin (hoTen, email, password) VALUES
('Quản trị viên', 'admin@ycp.edu.vn', '123456');

INSERT INTO giangvien (tenGV, chucVu, email, soDienThoai, maTruong, password) VALUES
('Nguyễn Văn Giảng', 'Giảng viên chính', 'gv@ycp.edu.vn', '0901123456', 'CNTT', '123456');

INSERT INTO sinhvien (hoTen, maSV, email, password, lop, nganh) VALUES
('Trần Minh Sinh', 'SV001', 'sv@ycp.edu.vn', '123456', 'K17A', 'Kỹ thuật phần mềm');

CREATE TABLE IF NOT EXISTS schools (
  stt INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  maDH VARCHAR(50) NOT NULL,
  matruong VARCHAR(50) UNIQUE
);

CREATE TABLE IF NOT EXISTS faculties (
  stt INT AUTO_INCREMENT PRIMARY KEY,
  maKhoa VARCHAR(50) UNIQUE NOT NULL,
  tenKhoa VARCHAR(255) NOT NULL,
  matruong VARCHAR(50) NOT NULL,
  FOREIGN KEY (matruong) REFERENCES schools(matruong)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS majors (
  stt INT AUTO_INCREMENT PRIMARY KEY,
  maNganh VARCHAR(50) UNIQUE NOT NULL,
  tenNganh VARCHAR(255) NOT NULL,
  maKhoa VARCHAR(50) NOT NULL,
  FOREIGN KEY (maKhoa) REFERENCES faculties(maKhoa)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS programs (
  stt INT AUTO_INCREMENT PRIMARY KEY,
  khoiKienThuc VARCHAR(255) NOT NULL,
  tongTinChi INT DEFAULT 0,
  tinChiBatBuoc INT DEFAULT 0,
  thaoTac VARCHAR(255) DEFAULT NULL
);

INSERT IGNORE INTO programs (khoiKienThuc, tongTinChi, tinChiBatBuoc, thaoTac) VALUES
('Khối kiến thức đại cương', 47, 47, NULL),
('Khối kiến thức cơ sở ngành', 31, 31, NULL),
('Khối kiến thức bổ trợ', 7, 5, NULL),
('Khối kiến thức chuyên ngành', 30, 25, NULL),
('Thực tập, Đồ án / Khóa luận tốt nghiệp', 14, 14, NULL);

CREATE TABLE IF NOT EXISTS employees (
  stt INT AUTO_INCREMENT PRIMARY KEY,
  tenNV VARCHAR(255) NOT NULL,
  chucVu VARCHAR(255) NOT NULL,
  email VARCHAR(255),
  soDienThoai VARCHAR(20),
  matruong VARCHAR(50),
  FOREIGN KEY (matruong) REFERENCES schools(matruong)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) AUTO_INCREMENT=1;

INSERT IGNORE INTO schools (name, maDH, matruong) VALUES
('Trường Kỹ thuật Phenikaa', 'PKA', 'PKA01'),
('Trường CNTT Phenikaa', 'PKA', 'PKA04'),
('Trường Y và Dược Phenikaa', 'PKA', 'PKA03'),
('Trường Kinh tế Phenikaa', 'PKA', 'PKA02'),
('Trường Ngoại ngữ và KHXH', 'PKA', 'PKA05'),
('Trường Khoa học và Sinh học và Môi trường', 'PKA', 'PKA06');

INSERT IGNORE INTO faculties (maKhoa, tenKhoa, matruong) VALUES
('PKA01-F01', 'Khoa Cơ điện tử', 'PKA01'),
('PKA01-F02', 'Khoa Cơ khí', 'PKA01'),
('PKA01-F03', 'Khoa Ô tô', 'PKA01'),
('PKA01-F04', 'Khoa Điện và Điện tử', 'PKA01'),
('PKA01-F05', 'Khoa Hóa học', 'PKA01'),
('PKA01-F06', 'Khoa Vật liệu', 'PKA01'),
('PKA04-F01', 'Khoa CNTT', 'PKA04'),
('PKA04-F02', 'Khoa KH Máy tính', 'PKA04'),
('PKA04-F03', 'Khoa ATTT', 'PKA04'),
('PKA04-F04', 'Khoa AI và Dữ liệu', 'PKA04'),
('PKA04-F05', 'Khoa Hệ thống', 'PKA04'),
('PKA04-F06', 'Khoa IoT', 'PKA04'),
('PKA03-F01', 'Khoa Y', 'PKA03'),
('PKA03-F02', 'Khoa Dược', 'PKA03'),
('PKA03-F03', 'Khoa Điều dưỡng', 'PKA03'),
('PKA03-F04', 'Khoa Cận lâm sàng', 'PKA03'),
('PKA03-F05', 'Khoa Phục hồi', 'PKA03'),
('PKA03-F06', 'Khoa Đông y', 'PKA03'),
('PKA03-F07', 'Khoa RHM', 'PKA03'),
('PKA02-F01', 'Khoa Quản trị', 'PKA02'),
('PKA02-F02', 'Khoa Kế toán', 'PKA02'),
('PKA02-F03', 'Khoa Tài chính', 'PKA02'),
('PKA02-F04', 'Khoa Nhân sự', 'PKA02'),
('PKA02-F05', 'Khoa Kiểm toán', 'PKA02'),
('PKA02-F06', 'Khoa Kinh doanh QT', 'PKA02'),
('PKA02-F07', 'Khoa Logistics', 'PKA02'),
('PKA02-F08', 'Khoa Marketing', 'PKA02'),
('PKA02-F09', 'Khoa Kinh tế số', 'PKA02'),
('PKA02-F10', 'Khoa Thương mại điện tử', 'PKA02'),
('PKA02-F11', 'Khoa TMĐT', 'PKA02'),
('PKA02-F12', 'Khoa Logistics số', 'PKA02'),
('PKA02-F13', 'Khoa Digital Marketing', 'PKA02'),
('PKA05-F01', 'Khoa Ngôn ngữ Anh', 'PKA05'),
('PKA05-F02', 'Khoa Ngôn ngữ Trung', 'PKA05'),
('PKA05-F03', 'Khoa Ngôn ngữ Hàn', 'PKA05'),
('PKA05-F04', 'Khoa Ngôn ngữ Pháp', 'PKA05'),
('PKA05-F05', 'Khoa Luật', 'PKA05'),
('PKA05-F06', 'Khoa Truyền thông', 'PKA05'),
('PKA05-F07', 'Khoa Báo chí', 'PKA05'),
('PKA05-F08', 'Khoa Tâm lý', 'PKA05'),
('PKA06-F01', 'Khoa Sinh học', 'PKA06'),
('PKA06-F02', 'Khoa Môi trường', 'PKA06'),
('PKA06-F03', 'Khoa Hóa học', 'PKA06');

INSERT IGNORE INTO majors (maNganh, tenNganh, maKhoa) VALUES
('MEM1', 'Kỹ thuật cơ điện tử', 'PKA01-F01'),
('MEM2', 'Kỹ thuật cơ khí', 'PKA01-F02'),
('VEE1', 'Kỹ thuật ô tô', 'PKA01-F03'),
('VEE3', 'Kỹ thuật phần mềm ô tô', 'PKA01-F03'),
('MEM3', 'Cơ điện tử ô tô', 'PKA01-F03'),
('EEE0', 'Kỹ thuật điện và điện tử', 'PKA01-F04'),
('EEE1', 'Kỹ thuật điều khiển và TĐH', 'PKA01-F04'),
('EEE2', 'Kỹ thuật y sinh', 'PKA01-F04'),
('EEE3', 'Kỹ thuật điện tử và viễn thông', 'PKA01-F04'),
('EEE-AI', 'Robot và trí tuệ nhân tạo', 'PKA01-F04'),
('CHE1', 'Kỹ thuật hóa học', 'PKA01-F05'),
('MSE1', 'Vật liệu tiên tiến và công nghệ nano', 'PKA01-F06'),
('MSE-AI', 'Vật liệu thông minh và trí tuệ nhân tạo', 'PKA01-F06'),
('MSE-IC', 'Công nghệ bán dẫn và đóng gói vi mạch', 'PKA01-F06'),
('ICT1', 'Công nghệ thông tin', 'PKA04-F01'),
('ICT2', 'Kỹ thuật phần mềm', 'PKA04-F01'),
('ICT3', 'Khoa học máy tính', 'PKA04-F02'),
('ICT4', 'An toàn thông tin', 'PKA04-F03'),
('ICT5', 'Trí tuệ nhân tạo', 'PKA04-F04'),
('ICT-DA', 'Khoa học dữ liệu và AI', 'PKA04-F04'),
('ICT6', 'Hệ thống thông tin', 'PKA04-F05'),
('ICT-IoT', 'Công nghệ IoT và nhúng', 'PKA04-F06'),
('MED1', 'Y khoa', 'PKA03-F01'),
('PHA1', 'Dược học', 'PKA03-F02'),
('NUR1', 'Điều dưỡng', 'PKA03-F03'),
('MTT1', 'Kỹ thuật xét nghiệm y học', 'PKA03-F04'),
('RTS1', 'Kỹ thuật hình ảnh y học', 'PKA03-F04'),
('RET1', 'Kỹ thuật phục hồi chức năng', 'PKA03-F05'),
('YTC1', 'Y học cổ truyền', 'PKA03-F06'),
('DEN1', 'Răng Hàm Mặt', 'PKA03-F07'),
('FBE1', 'Quản trị kinh doanh', 'PKA02-F01'),
('FBE2', 'Kế toán', 'PKA02-F02'),
('FBE3', 'Tài chính và Ngân hàng', 'PKA02-F03'),
('FBE4', 'Quản trị nhân lực', 'PKA02-F04'),
('FBE5', 'Kiểm toán', 'PKA02-F05'),
('FBE6', 'Kinh doanh quốc tế', 'PKA02-F06'),
('FBE7', 'Logistics và QL chuỗi cung ứng', 'PKA02-F07'),
('FBE8', 'Marketing', 'PKA02-F08'),
('FIDT1', 'Kinh tế số', 'PKA02-F09'),
('FIDT2', 'Kinh doanh số', 'PKA02-F10'),
('FIDT3', 'Thương mại điện tử', 'PKA02-F11'),
('FIDT4', 'Logistics số', 'PKA02-F12'),
('FIDT5', 'Công nghệ marketing', 'PKA02-F13'),
('FLE1', 'Ngôn ngữ Anh', 'PKA05-F01'),
('FLC1', 'Ngôn ngữ Trung Quốc', 'PKA05-F02'),
('FLK1', 'Ngôn ngữ Hàn Quốc', 'PKA05-F03'),
('FLF1', 'Ngôn ngữ Pháp', 'PKA05-F04'),
('FOL3', 'Luật', 'PKA05-F05'),
('FOL1', 'Luật kinh tế', 'PKA05-F05'),
('FOM1', 'Quan hệ công chúng và truyền thông đa phương tiện', 'PKA05-F06'),
('FOM2', 'Báo chí và truyền thông số', 'PKA05-F07'),
('FOP1', 'Tâm lý học ứng dụng', 'PKA05-F08'),
('BIO1', 'Công nghệ sinh học', 'PKA06-F01'),
('BMS', 'Khoa học y sinh', 'PKA06-F01'),
('BIO2', 'Sinh học ứng dụng', 'PKA06-F01'),
('ENV1', 'Công nghệ môi trường', 'PKA06-F02'),
('ENV2', 'Khoa học môi trường', 'PKA06-F02'),
('CHEM1', 'Hóa học', 'PKA06-F03');

INSERT IGNORE INTO employees (tenNV, chucVu, email, soDienThoai, matruong) VALUES
('Ngô Hồng Sơn', 'PGS.TS. Trưởng khoa', 'son.ngohong@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Mai Xuân Tráng', 'TS. Phó Trưởng khoa', 'trang.maixuan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Phạm Ngọc Hưng', 'TS. Phó Trưởng khoa', 'hung.phamngoc@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Lê Văn Vinh', 'PGS.TS. Giảng viên, Trưởng nhóm nghiên cứu', 'vinh.levan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Phạm Tuấn Minh', 'PGS.TS. Giảng viên', 'minh.phamtuan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Lê Hoàng Anh', 'TS. Giám đốc Trung tâm Công nghệ thông tin', 'anh.lehoang@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Công Lượng', 'TS. Giảng viên, Trưởng nhóm nghiên cứu', 'luong.nguyencong@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Phạm Tiến Lâm', 'PGS.TS. Giảng viên, Giám đốc CTĐT Khoa học máy tính, CTĐT Trí tuệ nhân tạo', 'lam.phamtien@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Phạm Văn Cảnh', 'PGS. TS. Giảng viên, Giám đốc CTĐT TN KHMT, ThS KHMT', 'canh.phamvan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Trịnh Thanh Bình', 'TS. Giảng viên, Giám đốc CTĐT Kỹ thuật phần mềm', 'binh.trinhthanh@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Thành Trung', 'ThS. Giảng viên, Giám đốc CTĐT CNTT Việt-Nhật', 'trung.nguyenthanh@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Vũ Thị Ngọc Anh', 'ThS. Giảng viên, Chủ tịch Công đoàn Khoa', 'anh.vuthingoc1@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Tạ Thúy Anh', 'TS. Giảng viên', 'anh.tathuy@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Văn Duy', 'TS. Giảng viên/Postdoc', 'duy.nguyenvan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Trần Đăng Hoan', 'TS. Giảng viên - Nghiên cứu viên', 'hoan.trandang@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Trịnh Thành', 'TS. Giảng viên', NULL, NULL, 'PKA04'),
('Đoàn Trung Sơn', 'TS. Giảng viên, Giám đốc CTĐT An toàn thông tin', 'son.doantrung@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Dương Thị Kim Huyền', 'TS. Giảng viên', 'huyen.duongthikim@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Thị Thùy Liên', 'TS. Giảng viên, Giám đốc CTĐT Công nghệ thông tin', 'lien.nguyenthithuy@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Vũ Văn Quang', 'ThS. Giảng viên', 'quang.vuvan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Minh Anh', 'ThS. Giảng viên - Nghiên cứu viên', 'anh.nguyenminh@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Phạm Hoàng Giang', 'ThS. Giảng viên - Nghiên cứu viên', 'giang.phamhoang@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Phạm Văn Hà', 'TS. Giảng viên', 'ha.phamvan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Văn Thiệu', 'ThS. Giảng viên', 'thieu.nguyenvan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Văn Cường', 'ThS. Giảng viên', 'cuong.nguyenvan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('La Văn Quân', 'ThS. Giảng viên', 'quan.lavan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Bùi Huy Toàn', 'TS. Giảng viên', 'toan.buihuy@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Đặng Thị Thuý An', 'TS. Giảng viên', 'an.dangthithuy@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Mai Thuý Nga', 'TS. Giảng viên', 'nga.maithuy@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Thị Khánh Trâm', 'ThS. Giảng viên', 'tram.nguyenthikhanh@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Lệ Thu', 'TS. Giảng viên', 'thu.nguyenle1@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Thanh Bình', 'TS. Giảng viên', 'binh.nguyenthanh@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Phạm Trung Dũng', 'ThS. Giảng viên', 'dung.phamtrung@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Vũ Quang Dũng', 'ThS. Giảng viên', 'dung.vuquang@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Văn Sơn', 'ThS. Giảng viên', 'son.nguyenvan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Thị Vân', 'TS. Giảng viên', 'van.nguyenthi1@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Hữu Đạt', 'ThS. Giảng viên, Bí thư Đoàn TN Trường', 'dat.nguyenhuu@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Đỗ Quốc Trường', 'TS. Giảng viên', 'truong.doquoc@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Phạm Kim Thành', 'ThS. Giảng viên', 'thanh.phamkim@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Xuân Quế', 'ThS. Giảng viên', 'que.nguyenxuan@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Thị Dinh', 'ThS. Giảng viên', 'dinh.nguyenthi@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Anh Tuấn', 'ThS. NCS. Giảng viên', 'tuan.nguyenanh2@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Đặng Thị Ngoan', 'ThS. Giảng viên', 'ngoan.thidang@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Lương Tùng Dương', 'ThS. Giảng viên', 'duong.luongtung@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Ngọc Hùng', 'ThS. Giảng viên', 'hung.ngocnguyen@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Vũ Thị Kiều Anh', 'ThS. Giảng viên', 'anh.vuthikieu@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Ngọc Giang', 'TS. Giảng viên', 'giang.nguyenngoc@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Kim Tuấn', 'TS. Giảng viên', 'tuan.nguyenkim@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Bùi Anh Tuấn', 'ThS. Giảng viên, Phó Bí thư Đoàn TN Trường', 'tuan.buianh@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Nguyễn Thị Ngọc Lan', 'CN. Giáo vụ', 'lan.nguyenthingoc@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Vũ Thị Huyền', 'CN. Giáo vụ', 'huyen.vuthi@phenikaa-uni.edu.vn', NULL, 'PKA04'),
('Đinh Thị Bảo Hương', 'Phó Trưởng khoa Phụ trách', 'huong.dinhthibao@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Trần Thị Thanh Hương', 'Phó Trưởng khoa kiêm nhiệm Trưởng Bộ môn Tiếng Anh chuyên ngành', 'huong.tranthithanh@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Thị Thanh Hương', 'Trưởng Bộ môn Lý thuyết tiếng', 'huong.nguyenthithanh@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Hoàng Văn Hoạt', 'Trưởng Bộ môn Dịch', 'hoat.hoangvan@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Dương Hồng Quân', 'Giảng viên - Phó Trưởng Bộ môn Thực hành tiếng', 'quan.duonghong@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Thị Thùy Linh', 'Giảng viên - Phó Trưởng Bộ môn Lý thuyết tiếng', 'linh.nguyenthithuy@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Phạm Thị Phương Thảo', 'Giảng viên - Phó Trưởng Bộ môn Tiếng Anh chuyên ngành', 'thao.phamthiphuong@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Đỗ Thị Trang', 'Giảng viên - Phó Trưởng Bộ môn Thực hành tiếng', 'trang.dothi@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Thị Bích Thủy', 'Giảng viên', 'thuy.nguyenthibich@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Đăng Sửu', 'Giảng viên', 'suu.nguyendang@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Thị Phượng', 'Thư ký/Giáo vụ', 'phuong.nguyenthi@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Ngọc Hải', 'Giảng viên', 'hai.nguyenngoc@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Phạm Thị Thanh Hương', 'Giảng viên', 'huong.phamthithanh@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Dương Thị Hồng Thái', 'Giảng viên', 'thai.duongthihong@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Vũ Anh Thư', 'Giảng viên', 'thu.vuanh@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Trần Thanh Thảo', 'Giảng viên', 'thao.tranthanh@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Phan Thị Mai', 'Giảng viên', 'mai.phanthi@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Lại Thị Phượng', 'Giảng viên', 'phuong.laitthi@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Phan Thùy Linh', 'Giảng viên', 'linh.phanthuy@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Lương Quỳnh Anh', 'Giảng viên', 'anh.luongquynh@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Vũ Hải Triều', 'Giảng viên', 'trieu.nguyenvuhai@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Trịnh Thùy Hằng', 'Giảng viên', 'hang.trinhthuy@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Vĩ Giang', 'Giảng viên', 'giang.nguyenvy@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Thị Kiều Lê', 'Giảng viên', 'le.nguyenthikieu@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Bùi Thanh Nga', 'Giảng viên', 'nga.buithanh@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Ngọc Hải', 'Giảng viên', 'hai.nguyenngoc@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Xuân Hải', 'Giảng viên', 'hai.nguyenxuan@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Lê Thị Thanh Mai', 'Giảng viên', 'mai.lethithanh@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Đặng Minh Tâm', 'Giảng viên', 'tam.dangminh@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Vũ Quỳnh Trúc', 'Giảng viên', 'truc.vuquynh@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Nguyễn Thị Thu Trang', 'Giảng viên', 'trang.nguyenthithu@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Phạm Việt Phú', 'Giảng viên', 'phu.phamviet@phenikaa-uni.edu.vn', NULL, 'PKA05'),
('Trần Đức Tân', 'GS.TS. Trưởng khoa; Trưởng nhóm nghiên cứu', 'tan.tranduc@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Lương Văn Sử', 'TS. Phó trưởng khoa', 'su.luongvan@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Trọng Thắng', 'TS. Phó trưởng khoa', 'thang.nguyentrong@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Văn Hiếu', 'TS. Hiệu trưởng Trường; Viện trưởng', 'hieu.nguyenvan@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Vũ Văn Hào', 'TS. Phó Viện trưởng', 'hao.vuvan@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Trần Quang Huy', 'PGS.TS. Phó Viện trưởng Viện Nghiên cứu', 'huy.tranquang@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Hoàng Quang Trung', 'TS. Phó Trưởng phòng Đào tạo và QLKH', 'trung.hoangquang@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Hoàng Trung', 'TS. Phó Trưởng Khoa', 'trung.nguyenhoang@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Trần Xuân Kiên', 'PGS.TS. Giảng viên/Nghiên cứu viên', 'kien.tranxuan@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Vũ Ngọc Hải', 'PGS.TS. Trợ lý Tổng giám đốc, Giảng viên', 'hai.vungoc@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Ngọc Việt', 'TS. Giảng viên/Nghiên cứu viên', 'viet.nguyenngoc@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Phạm Hữu Tuấn', 'TS. Giảng viên/Nghiên cứu viên', 'tuan.phamhuu@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Lê Việt Thông', 'TS. Giảng viên/Nghiên cứu viên', 'thong.leviet@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Trần Huy Hùng', 'TS. Giảng viên/Nghiên cứu viên', 'hung.tranhuy@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Vũ Ngọc Nam', 'TS. Giảng viên/Nghiên cứu viên', 'nam.vungoc@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Thị Thanh Vân', 'TS. Giảng viên/Nghiên cứu viên', 'van.nguyenthithanh@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Hoàng Phúc', 'TS. Giảng viên/Nghiên cứu viên', 'phuc.nguyenhoang@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Lý Thị Thanh Hà', 'TS. Giảng viên/Nghiên cứu viên', 'ha.lythithanh@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Đức Cự', 'TS. Giảng viên/Nghiên cứu viên', 'cu.nguyenduc@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Thị Hợp', 'TS. Giảng viên/Nghiên cứu viên', 'hop.nguyenthi@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Thị Hiền', 'TS. Giảng viên/Nghiên cứu viên', 'hien.nguyenthi@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Thị Ngọc Anh', 'TS. Giảng viên/Nghiên cứu viên', 'anh.nguyenthithi@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Thị Thủy', 'TS. Giảng viên/Nghiên cứu viên', 'thuy.nguyenthi@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Lê Thái Hưng', 'TS. Giảng viên/Nghiên cứu viên', 'hung.lethai@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Đặng Thị Hồng Vương', 'TS. Giảng viên/Nghiên cứu viên', 'vuong.dangthihong@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Vũ Đức Anh', 'TS. Giảng viên/Nghiên cứu viên', 'anh.vuduc@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Văn Tĩnh', 'TS. Giảng viên/Nghiên cứu viên', 'tinh.nguyenvan@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Phạm Thị Châu', 'TS. Giảng viên/Nghiên cứu viên', 'chau.phamthi@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Lê Trung Kiên', 'TS. Giảng viên/Nghiên cứu viên', 'kien.letrung@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Thị Trung Kiên', 'TS. Giảng viên/Nghiên cứu viên', 'kien.nguyenthitrung@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Đỗ Huy Quốc', 'TS. Giảng viên/Nghiên cứu viên', 'quoc.dohuy@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Dương Văn Quang', 'TS. Giảng viên/Nghiên cứu viên', 'quang.duongvan@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Nguyễn Thị Hồng Minh', 'ThS. Kỹ thuật viên', 'minh.nguyenthihong@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Phạm Thị Huyền', 'ThS. Giáo vụ Khoa', 'huyen.phamthi@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Mai Thị Thanh Thịnh', 'Cán bộ lớp chuyên nghiệp', 'thinh.maithithanh@phenikaa-uni.edu.vn', NULL, 'PKA01'),
('Dương Thu Trang', 'Cán bộ lớp chuyên nghiệp', 'trang.duongthu@phenikaa-uni.edu.vn', NULL, 'PKA01');

DROP TABLE IF EXISTS courses;
CREATE TABLE courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  maHP VARCHAR(50) NOT NULL UNIQUE,
  tenHP VARCHAR(255) NOT NULL,
  soTinChi INT NOT NULL,
  lyThuyet FLOAT DEFAULT 0,
  thucHanh FLOAT DEFAULT 0,
  hpTienQuyet VARCHAR(50) DEFAULT NULL,
  hpHocTruoc VARCHAR(50) DEFAULT NULL,
  khoaQuanLy VARCHAR(50),
  ghiChu VARCHAR(50),
  KhoiKienThuc VARCHAR(100)
);

INSERT INTO courses (maHP, tenHP, soTinChi, lyThuyet, thucHanh, hpTienQuyet, hpHocTruoc, khoaQuanLy, ghiChu, KhoiKienThuc) VALUES
('FFS702001', 'Pháp luật đại cương', 2, 2, 0, NULL, NULL, 'FFS', 'Bắt buộc', 'Đại cương'),
('FFS703002', 'Triết học Mác - Lênin', 3, 3, 0, NULL, NULL, 'FFS', 'Bắt buộc', 'Đại cương'),
('FFS702003', 'Kinh tế chính trị Mác - Lênin', 2, 2, 0, NULL, 'FFS703002', 'FFS', 'Bắt buộc', 'Đại cương'),
('FFS702004', 'Chủ nghĩa xã hội khoa học', 2, 2, 0, NULL, 'FFS702003', 'FFS', 'Bắt buộc', 'Đại cương'),
('FFS702005', 'Lịch sử Đảng Cộng sản Việt Nam', 2, 2, 0, NULL, 'FFS702004', 'FFS', 'Bắt buộc', 'Đại cương'),
('FFS702006', 'Tư tưởng Hồ Chí Minh', 2, 2, 0, NULL, 'FFS702005', 'FFS', 'Bắt buộc', 'Đại cương'),
('FFS703013', 'Vật lý 1', 3, 2.5, 0.5, NULL, NULL, 'FFS', 'Bắt buộc', 'Đại cương'),
('FFS703014', 'Vật lý 2', 3, 2.5, 0.5, 'FFS703013', NULL, 'FFS', 'Bắt buộc', 'Đại cương'),
('FEL702075', 'Tiếng Anh cơ bản 1', 2, 0, 2, NULL, NULL, 'FEL', 'Bắt buộc', 'Đại cương'),
('FEL703076', 'Tiếng Anh cơ bản 2', 3, 0, 3, NULL, 'FEL702075', 'FEL', 'Bắt buộc', 'Đại cương'),
('FEL703077', 'Tiếng Anh cơ bản 3', 3, 0, 3, NULL, 'FEL703076', 'FEL', 'Bắt buộc', 'Đại cương'),
('FEL703078', 'Tiếng Anh nâng cao 1', 3, 0, 3, NULL, 'FEL703077', 'FEL', 'Bắt buộc', 'Đại cương'),
('FEL703079', 'Tiếng Anh nâng cao 2', 3, 0, 3, NULL, 'FEL703078', 'FEL', 'Bắt buộc', 'Đại cương'),
('FEL702080', 'Tiếng Anh nâng cao 3', 3, 0, 2, NULL, 'FEL703079', 'FEL', 'Bắt buộc', 'Đại cương'),
('FFS703007', 'Đại số tuyến tính', 3, 3, 0, NULL, NULL, 'FFS', 'Bắt buộc', 'Đại cương'),
('FFS703063', 'Giải tích 1', 3, 3, 0, NULL, NULL, 'FFS', 'Bắt buộc', 'Đại cương'),
('FFS703064', 'Giải tích 2', 3, 3, 0, NULL, 'FFS703063', 'FFS', 'Bắt buộc', 'Đại cương');

INSERT INTO courses VALUES
(NULL, 'CSE703107', 'Cơ sở lập trình', 3, 2, 1, NULL, NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành'),
(NULL, 'CSE703024', 'Toán rời rạc', 3, 3, 0, NULL, NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành'),
(NULL, 'CSE703026', 'Cấu trúc dữ liệu và thuật toán', 3, 2, 1, 'CSE703107', NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành'),
(NULL, 'CSE703052', 'Thuật toán ứng dụng', 3, 2, 1, 'CSE703026', NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành'),
(NULL, 'CSE703057', 'Tự động hóa', 3, 2, 1, NULL, NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành'),
(NULL, 'CSE702017', 'Hệ điều hành', 3, 2, 1, 'CSE703107', NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành'),
(NULL, 'CSE703032', 'Kiến trúc máy tính', 2, 2, 0, NULL, NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành'),
(NULL, 'CSE703008', 'Cơ sở dữ liệu', 3, 2, 1, NULL, NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành'),
(NULL, 'CSE703029', 'Lập trình hướng đối tượng', 3, 2, 1, 'CSE703107', NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành'),
(NULL, 'CSE702036', 'Mạng máy tính', 3, 2, 1, NULL, NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành'),
(NULL, 'CSE702016', 'Giới thiệu ngành', 2, 1, 1, NULL, NULL, 'CSE', 'Bắt buộc', 'Cơ sở ngành');

INSERT INTO courses VALUES
(NULL, 'FBE703044', 'Kinh tế vi mô', 3, 3, 0, NULL, NULL, 'FBE', 'Bắt buộc', 'Bổ trợ'),
(NULL, 'FBE702001', 'Quản trị học', 2, 2, 0, NULL, NULL, 'FBE', 'Bắt buộc', 'Bổ trợ'),
(NULL, 'FTS702002', 'Kỹ năng quản lý dự án', 2, 1, 1, NULL, NULL, 'FBE', 'Tự chọn', 'Bổ trợ'),
(NULL, 'FTS702004', 'Kỹ năng tư duy sáng tạo và phản biện', 2, 1, 1, NULL, NULL, 'FBE', 'Tự chọn', 'Bổ trợ'),
(NULL, 'FTS702001', 'Kỹ năng khởi nghiệp và lãnh đạo', 2, 1, 1, NULL, NULL, 'FBE', 'Tự chọn', 'Bổ trợ'),
(NULL, 'FTS702003', 'Kỹ năng đàm phán, thương lượng', 2, 1, 1, NULL, NULL, 'FBE', 'Tự chọn', 'Bổ trợ');

INSERT INTO courses VALUES
(NULL, 'CSE703016', 'Giao diện người máy', 3, 2, 1, 'CSE702106', NULL, 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE702115', 'Các hệ thống thông minh', 2, 1, 1, 'CSE703006', NULL, 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE703064', 'Xây dựng ứng dụng web', 3, 2, 1, 'CSE702106', NULL, 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE703048', 'Phân tích và thiết kế phần mềm', 3, 2, 1, 'CSE703029', 'CSE703110', 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE703093', 'An toàn phần mềm', 3, 2, 1, 'CSE703010', NULL, 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE703094', 'Phát triển ứng dụng di động', 3, 2, 1, 'CSE703029', 'CSE703107', 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE703095', 'Yêu cầu phần mềm', 3, 2, 1, 'CSE702106', NULL, 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE703096', 'Đồ án chuyên ngành', 3, 2, 1, 'CSE703048;CSE703008', NULL, 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE702049', 'Quản trị dự án CNTT', 2, 2, 0, 'CSE703048', NULL, 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE703010', 'Đánh giá và kiểm định chất lượng phần mềm', 3, 2, 1, 'CSE703048', NULL, 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE703110', 'Kiến trúc phần mềm', 3, 2, 1, 'CSE703095', NULL, 'CSE', 'Bắt buộc', 'Chuyên ngành'),
(NULL, 'CSE702011', 'Điện toán đám mây', 2, 1, 1, 'CSE702036', NULL, 'CSE', 'Tự chọn', 'Chuyên ngành'),
(NULL, 'CSE702033', 'Lập trình trò chơi', 2, 1, 1, 'CSE703094', NULL, 'CSE', 'Tự chọn', 'Chuyên ngành'),
(NULL, 'CSE702101', 'Ngôn ngữ lập trình nâng cao', 2, 1, 1, 'CSE703029', NULL, 'CSE', 'Tự chọn', 'Chuyên ngành'),
(NULL, 'CSE702103', 'Linux và phần mềm mã nguồn mở', 2, 1, 1, 'CSE703048', 'CSE702017', 'CSE', 'Tự chọn', 'Chuyên ngành'),
(NULL, 'CSE702104', 'Trích xuất thông tin và tìm kiếm web', 2, 1, 1, 'CSE703006', NULL, 'CSE', 'Tự chọn', 'Chuyên ngành');

INSERT INTO courses VALUES
(NULL, 'CSE704067', 'Thực tập tốt nghiệp', 4, 0, 4, NULL, NULL, 'CSE', 'Bắt buộc', 'Thực tập, Đồ án'),
(NULL, 'CSE710111', 'Đồ án tốt nghiệp', 10, 0, 10, 'CSE703096', NULL, 'CSE', 'Bắt buộc', 'Thực tập, Đồ án');

CREATE TABLE phongban (
  maPB VARCHAR(20) PRIMARY KEY,
  tenPB VARCHAR(150) NOT NULL,
  diaChi VARCHAR(255),
  email VARCHAR(150),
  soDienThoai VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS translations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  entity_type VARCHAR(50) NOT NULL,
  entity_id VARCHAR(100) NOT NULL,
  field VARCHAR(50) NOT NULL,
  lang VARCHAR(5) NOT NULL,
  source_text TEXT NOT NULL,
  source_hash CHAR(64) NOT NULL,
  translated_text TEXT NOT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_translation (entity_type, entity_id, field, lang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO phongban (maPB, tenPB, diaChi, email, soDienThoai) VALUES
('PB01', 'PHÒNG HÀNH CHÍNH - QUẢN TRỊ - THIẾT BỊ', 'Tầng 1, Nhà A10 -Đại học Phenikaa', 'hcqt@phenikaa-uni.edu.vn', '0246.2918.118'),
('PB02', 'PHÒNG TỔ CHỨC NHÂN SỰ', 'Tầng 3, Nhà A9 -Đại học Phenikaa', 'tcns@phenikaa-uni.edu.vn | Tuyển dụng: hr@phenikaa-uni.edu.vn', '0246.2918.118'),
('PB03', 'PHÒNG ĐÀO TẠO', 'Tầng 4, Nhà A9 -Đại học Phenikaa', 'dtqlsv@phenikaa-uni.edu.vn', '0246.2918.118 (máy lẻ 150)'),
('PB04', 'PHÒNG TUYỂN SINH VÀ TRUYỀN THÔNG', 'Tầng 1, Nhà A9 -Đại học Phenikaa', 'truyenthong@phenikaa-uni.edu.vn | tuyensinh@phenikaa-uni.edu.vn', '0246.2918.118 (Máy lẻ: 101; 126) | Hotline: 0946511010, 09688811010, 0967511010, 0969511010, 0983811010'),
('PB05', 'PHÒNG TÀI CHÍNH - KẾ TOÁN', 'Tầng 3, Nhà A9 -Đại học Phenikaa', 'tckt@phenikaa-uni.edu.vn', '0246.2918.118'),
('PB06', 'TRUNG TÂM CÔNG NGHỆ THÔNG TIN', 'Tầng 3 - Nhà A10 - Đại học Phenikaa', 'itc@phenikaa-uni.edu.vn', '0246.2918.118'),
('PB07', 'TRUNG TÂM THÔNG TIN - THƯ VIỆN', 'Tầng 4 - 5 - 6 - Nhà A10 - Đại học Phenikaa', 'library@phenikaa-uni.edu.vn', '0246.2918.118'),
('PB08', 'PHÒNG KHOA HỌC CÔNG NGHỆ', 'Tầng 3 - Nhà A10 - Đại học Phenikaa', 'khcn@phenikaa-uni.edu.vn', '0246.2918.118'),
('PB09', 'PHÒNG ĐẢM BẢO CHẤT LƯỢNG VÀ KHẢO THÍ', 'Tầng 3 - Nhà A9 - Đại học Phenikaa', 'dbcl@phenikaa-uni.edu.vn', '0246.2918.118'),
('PB10', 'PHÒNG THANH TRA', 'Tầng 4 - Nhà A9 - Đại học Phenikaa', 'ttpc@phenikaa-uni.edu.vn', '0246.2918.118'),
('PB11', 'PHÒNG HỢP TÁC ĐỐI NGOẠI', 'Tầng 4 - Nhà A9 - Đại học Phenikaa', 'htdn@phenikaa-uni.edu.vn', '0246.2918.118'),
('PB12', 'PHÒNG CÔNG TÁC SINH VIÊN', 'Tầng 3 - Nhà A9 - Đại học Phenikaa', 'ctsv@phenikaa-uni.edu.vn', '0246.2918.118 (Máy lẻ: 138)'),
('PB13', 'PHÒNG PHÁP CHẾ', 'Tầng 4 - Nhà A9 - Đại học Phenikaa', 'ttpc@phenikaa-uni.edu.vn', '0246.2918.118'),
('PB14', 'VIỆN ĐÀO TẠO QUỐC TẾ', 'Tầng 26, Nhà A9 -Đại học Phenikaa', 'sie@phenikaa-uni.edu.vn', '00246.2918.118 (Máy lẻ: 141)');

DROP TABLE IF EXISTS de_xuat;
CREATE TABLE de_xuat (
  maDX INT AUTO_INCREMENT PRIMARY KEY,
  tieuDe VARCHAR(255) NOT NULL,
  noiDung TEXT NOT NULL,
  loaiDeXuat VARCHAR(100) NOT NULL,
  nguoiGui VARCHAR(100) NOT NULL,
  emailNguoiGui VARCHAR(150),
  tepDinhKem VARCHAR(255),
  ngayGui DATETIME DEFAULT CURRENT_TIMESTAMP,
  trangThai ENUM('Nháp','Chờ duyệt','Đã duyệt','Từ chối','Yêu cầu chỉnh sửa') DEFAULT 'Chờ duyệt',
  lyDoTuChoi TEXT,
  nguoiDuyet VARCHAR(100),
  ngayDuyet DATETIME,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_dx_trangThai (trangThai),
  INDEX idx_dx_nguoiGui (nguoiGui),
  INDEX idx_dx_ngayGui (ngayGui)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO de_xuat (tieuDe, noiDung, loaiDeXuat, nguoiGui, emailNguoiGui)
VALUES
('Đề xuất mở ngành Trí tuệ nhân tạo nâng cao', 'Đề nghị mở ngành học mới "Trí tuệ nhân tạo nâng cao" trực thuộc Khoa CNTT để đáp ứng nhu cầu học tập và nghiên cứu.', 'Ngành học', 'Nguyễn Văn Giảng', 'gv@ycp.edu.vn'),
('Đề xuất bổ sung học phần Kỹ năng lãnh đạo nhóm', 'Đề nghị thêm học phần mới nhằm phát triển kỹ năng lãnh đạo cho sinh viên khối ngành Kinh tế.', 'Học phần', 'Trần Thị Hoa', 'hoa.tranthi@phenikaa-uni.edu.vn'),
('Đề xuất chỉnh sửa tên Khoa KH Máy tính', 'Đề nghị đổi tên Khoa KH Máy tính thành Khoa Khoa học và Kỹ thuật Máy tính cho phù hợp với nội dung đào tạo.', 'Khoa', 'Phạm Ngọc Hưng', 'hung.phamngoc@phenikaa-uni.edu.vn');

DROP TABLE IF EXISTS de_xuat_log;
CREATE TABLE de_xuat_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  maDX INT NOT NULL,
  hanhDong ENUM('Chấp nhận','Từ chối','Yêu cầu chỉnh sửa') NOT NULL,
  nguoiThucHien VARCHAR(100) NOT NULL,
  noiDung TEXT,
  thoiGian DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (maDX) REFERENCES de_xuat(maDX) ON DELETE CASCADE,
  INDEX idx_dxlog_maDX (maDX),
  INDEX idx_dxlog_thoiGian (thoiGian)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nguoiNhan VARCHAR(100) NOT NULL,
  tieuDe VARCHAR(255) NOT NULL,
  noiDung TEXT,
  lienKet VARCHAR(255),
  isRead TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) UNIQUE,
  value VARCHAR(255)
);
 CREATE TABLE IF NOT EXISTS registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  maSV VARCHAR(50),
  maHP VARCHAR(50),
  ngayDK DATETIME,
  UNIQUE KEY unique_dk (maSV, maHP)
);
ALTER TABLE registrations 
ADD COLUMN trangThai VARCHAR(50) DEFAULT 'Chưa thanh toán',
ADD COLUMN ngayThanhToan DATETIME NULL;
